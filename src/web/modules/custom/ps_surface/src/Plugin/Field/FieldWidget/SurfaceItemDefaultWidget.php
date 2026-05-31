<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_surface_item_default' widget.
 *
 * Renders qualification + value + unit_code on a single inline row.
 * Unit codes are loaded from the 'surface_unit' ps_dictionary type.
 *
 * @FieldWidget(
 *   id = "ps_surface_item_default",
 *   label = @Translation("PS surface item"),
 *   field_types = {
 *     "ps_surface_item"
 *   }
 * )
 */
class SurfaceItemDefaultWidget extends WidgetBase {

  private const KNOWN_QUALIFICATIONS = [
    'TOTAL',
    'DISPO',
    'ETREF',
    'MINIM',
    'MAXIM',
  ];

  private const QUALIFICATION_DICTIONARY_TYPE = 'surface_qualification';

  private const UNIT_DICTIONARY_TYPE = 'surface_unit';

  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $item = $items[$delta] ?? NULL;
    $required = (bool) ($element['#required'] ?? FALSE);

    $element['#type'] = 'container';
    $element['#attributes']['class'][] = 'ps-surface-item';
    $element['#attached']['library'][] = 'ps_surface/surface-widget';

    $element['qualification'] = [
      '#type' => 'select',
      '#title' => $this->t('Qualification'),
      '#default_value' => $item?->qualification ?? '',
      '#required' => $required,
      '#options' => $this->buildQualificationOptions(),
      '#description' => $this->t('Select a qualification code from dictionary entries.'),
    ];

    $element['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Value'),
      '#step' => 0.01,
      '#min' => 0,
      '#default_value' => $item?->value,
      '#required' => $required,
      '#attributes' => ['placeholder' => '0.00'],
    ];

    $element['unit_code'] = [
      '#type' => 'select',
      '#title' => $this->t('Unit'),
      '#options' => $this->buildUnitOptions(),
      '#default_value' => $item?->unit_code ?? 'M2',
    ];

    return $element;
  }

  /**
   * Builds unit options from the 'surface_unit' dictionary type.
   *
   * @return array<string, string>
   */
  public function buildUnitOptions(): array {
    return $this->buildDictionaryOptions(
      self::UNIT_DICTIONARY_TYPE,
      ['M2' => 'M2'],
    );
  }

  /**
   * Builds qualification options from the 'surface_qualification' dictionary.
   *
   * @return array<string, string>
   */
  public function buildQualificationOptions(): array {
    $fallback = array_combine(self::KNOWN_QUALIFICATIONS, self::KNOWN_QUALIFICATIONS);
    return $this->buildDictionaryOptions(
      self::QUALIFICATION_DICTIONARY_TYPE,
      $fallback ?: ['TOTAL' => 'TOTAL'],
    );
  }

  /**
   * Builds options from a dictionary type.
   *
   * @param string $dictionaryType
   *   Dictionary type machine name.
   * @param array<string, string> $fallback
   *   Fallback options when no active entries exist.
   *
   * @return array<string, string>
   */
  protected function buildDictionaryOptions(string $dictionaryType, array $fallback): array {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $ids = $storage->getQuery()
      ->condition('type', $dictionaryType)
      ->condition('status', TRUE)
      ->sort('weight', 'ASC')
      ->accessCheck(FALSE)
      ->execute();

    if (empty($ids)) {
      return $fallback;
    }

    $entries = $storage->loadMultiple($ids);
    $options = [];
    $prefix_len = strlen($dictionaryType . '.');

    foreach ($entries as $entry) {
      $code = strtoupper(substr($entry->id(), $prefix_len));
      $options[$code] = $entry->label();
    }

    return $options ?: $fallback;
  }

}
