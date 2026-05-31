<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Offer-oriented surface widget with canonical-first ordering.
 *
 * Keeps canonical rows in fixed order (TOTAL, DISPO, ETREF, MINIM), then appends
 * any extra qualifications coming from XML (e.g. MAXIM).
 * Canonical rows keep a locked qualification code; extra rows remain editable.
 * Unit codes are loaded from the 'surface_unit' ps_dictionary type.
 *
 * @FieldWidget(
 *   id = "ps_surface_offer",
 *   label = @Translation("PS surface — Offer (TOTAL / DISPO / ETREF / MINIM)"),
 *   field_types = {
 *     "ps_surface_item"
 *   }
 * )
 */
final class SurfaceOfferWidget extends SurfaceItemDefaultWidget {

  /** Canonical qualification order for offer surface rows. */
    private const QUALIFICATIONS = ['TOTAL', 'DISPO', 'ETREF', 'MINIM'];

  private const QUALIFICATION_LABELS = [
    'TOTAL' => 'Total surface',
    'DISPO' => 'Available surface',
    'ETREF' => 'Reference surface (ERP)',
    'MINIM' => 'Minimum surface',
  ];

  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings, $entityTypeManager);
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   *
  * Reorders items to TOTAL→DISPO→ETREF→MINIM then appends extras.
   */
  protected function formMultipleElements(FieldItemListInterface $items, array &$form, FormStateInterface $form_state): array {
    // Index existing stored items by qualification and keep non-canonical rows.
    $by_qual = [];
    $extras = [];
    foreach ($items as $item) {
      $qualification = (string) ($item->qualification ?? '');
      if ($qualification === '') {
        continue;
      }

      if (in_array($qualification, self::QUALIFICATIONS, TRUE)) {
        $by_qual[$qualification] = $item->getValue();
      }
      else {
        $extras[] = $item->getValue();
      }
    }

    // Rebuild items with canonical rows first, then keep any extra rows.
    $ordered = [];
    foreach (self::QUALIFICATIONS as $qualification) {
      $ordered[] = $by_qual[$qualification] ?? [
        'qualification' => $qualification,
        'value' => NULL,
        'unit_code' => 'M2',
      ];
    }
    foreach ($extras as $extra) {
      $ordered[] = $extra;
    }
    $items->setValue($ordered);

    // Force form-state count to canonical rows + extras.
    $field_name = $items->getName();
    $parents = $form['#parents'] ?? [];
    $field_state = static::getWidgetState($parents, $field_name, $form_state);
    $field_state['items_count'] = count($ordered);
    static::setWidgetState($parents, $field_name, $form_state, $field_state);

    // Delegate rendering to the parent (handles #parents, #tree, AJAX, etc.).
    $elements = parent::formMultipleElements($items, $form, $form_state);

    // Canonical rows cannot be removed or reordered.
    foreach (array_keys(self::QUALIFICATIONS) as $delta) {
      unset($elements[$delta]['_weight'], $elements[$delta]['_operations']);
    }

    // Wrap with offer-specific CSS class.
    $elements['#attributes']['class'][] = 'ps-surface-offer-widget';

    return $elements;
  }

  /**
   * {@inheritdoc}
   *
   * Renders canonical rows as locked labels and delegates extra rows to parent.
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    if ($delta >= count(self::QUALIFICATIONS)) {
      return parent::formElement($items, $delta, $element, $form, $form_state);
    }

    $item = $items[$delta] ?? NULL;

    $qualification = $item?->qualification ?? self::QUALIFICATIONS[$delta] ?? 'TOTAL';
    $label = isset(self::QUALIFICATION_LABELS[$qualification])
      ? $this->t(self::QUALIFICATION_LABELS[$qualification])
      : $qualification;

    $element['#type'] = 'container';
    $element['#attributes']['class'][] = 'ps-surface-item';
    $element['#attached']['library'][] = 'ps_surface/surface-widget';

    // Qualification: fixed server-side value, never rendered as input.
    $element['qualification'] = [
      '#type' => 'value',
      '#value' => $qualification,
    ];

    $element['value'] = [
      '#type' => 'number',
      '#title' => $label,
      '#step' => 0.01,
      '#min' => 0,
      '#default_value' => $item?->value,
      '#attributes' => ['placeholder' => '0.00'],
    ];

    $element['unit_code'] = [
      '#type' => 'select',
      '#title' => $this->t('Unit'),
      '#title_display' => 'invisible',
      '#options' => $this->buildUnitOptions(),
      '#default_value' => $item?->unit_code ?? 'M2',
    ];

    return $element;
  }

}
