<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default formatter for ps_diagnostic field.
 *
 * Displays diagnostic data with optional validity dates, numeric value,
 * and class label with smart handling of special statuses (N/A, no classification).
 */
#[FieldFormatter(
  id: 'ps_diagnostic_default',
  label: new TranslatableMarkup('Diagnostic (default)'),
  field_types: ['ps_diagnostic'],
)]
final class DiagnosticDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the formatter.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param array<string, mixed> $plugin_definition
   *   The plugin definition array.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array<string, mixed> $settings
   *   The settings.
   * @param string $label
   *   The label.
   * @param string $view_mode
   *   The view mode.
   * @param array<string, mixed> $third_party_settings
   *   Third-party settings.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    string $label,
    string $view_mode,
    array $third_party_settings,
    private readonly DictionaryManagerInterface $dictionaryManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_dictionary.manager'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_numeric_value' => TRUE,
      'show_type_label' => TRUE,
      'show_validity_dates' => FALSE,
      'default_layout' => 'horizontal',
      'dim_empty' => TRUE,
      'dim_opacity' => 30,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form = parent::settingsForm($form, $form_state);

    $form['show_numeric_value'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show numeric value'),
      '#default_value' => $this->getSetting('show_numeric_value'),
      '#description' => $this->t('Display the numeric value alongside the class label.'),
    ];

    $form['show_type_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show type label'),
      '#default_value' => $this->getSetting('show_type_label'),
      '#description' => $this->t('Display the diagnostic label.'),
    ];

    $form['show_validity_dates'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show validity dates'),
      '#default_value' => $this->getSetting('show_validity_dates'),
      '#description' => $this->t('Display "valid from ... to ..." dates.'),
    ];

    $form['default_layout'] = [
      '#type' => 'select',
      '#title' => $this->t('Layout'),
      '#options' => [
        'horizontal' => $this->t('Horizontal'),
        'vertical' => $this->t('Vertical'),
        'compact' => $this->t('Compact'),
      ],
      '#default_value' => $this->getSetting('default_layout'),
      '#description' => $this->t('Display layout for diagnostic items.'),
    ];

    $form['dim_empty'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Dim empty diagnostics'),
      '#default_value' => $this->getSetting('dim_empty'),
      '#description' => $this->t('Reduce opacity for diagnostics without value or class.'),
    ];

    $form['dim_opacity'] = [
      '#type' => 'number',
      '#title' => $this->t('Dim opacity (%)'),
      '#min' => 10,
      '#max' => 90,
      '#step' => 5,
      '#default_value' => $this->getSetting('dim_opacity'),
      '#description' => $this->t('Opacity percentage when dimmed (10-90).'),
      '#states' => [
        'visible' => [
          ':input[name="fields[field_diagnostics][settings_edit_form][settings][dim_empty]"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];
    $summary[] = $this->getSetting('show_numeric_value') ? $this->t('Numeric value: visible') : $this->t('Numeric value: hidden');
    $summary[] = $this->getSetting('show_type_label') ? $this->t('Type: visible') : $this->t('Type: hidden');
    $summary[] = $this->getSetting('show_validity_dates') ? $this->t('Dates: visible') : $this->t('Dates: hidden');

    $layouts = ['horizontal' => $this->t('Horizontal'), 'vertical' => $this->t('Vertical'), 'compact' => $this->t('Compact')];
    $layout = $this->getSetting('default_layout');
    $summary[] = $this->t('Layout: @layout', ['@layout' => $layouts[$layout] ?? $layout]);

    if ($this->getSetting('dim_empty')) {
      $summary[] = $this->t('Dim empty: @opacity%', ['@opacity' => $this->getSetting('dim_opacity')]);
    }
    else {
      $summary[] = $this->t('Dim empty: disabled');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    unset($langcode);
    $elements = [];

    $entity = $items->getEntity();
    $typeStorage = $this->entityTypeManager->getStorage('diagnostic');

    foreach ($items as $delta => $item) {
      $classLabel = $item->get('class')->getValue();
      $noClassification = (bool) $item->get('no_classification')->getValue();
      $nonApplicable = (bool) $item->get('non_applicable')->getValue();

      if ($noClassification) {
        $classLabel = '?';
      }
      elseif ($nonApplicable) {
        $classLabel = 'N/A';
      }

      // Always prepare type label (template decides visibility).
      $typeId = $item->get('type_id')->getValue();
      $typeLabel = NULL;
      $diagnosticType = NULL;
      if ($typeId !== NULL && $typeId !== '') {
        $typeLabel = $this->dictionaryManager->getLabel('diagnostic_type', $typeId) ?? $typeId;
        $diagnosticType = $typeStorage->load($typeId);
      }

      // Always format numeric value (template decides visibility).
      $rawValue = $item->get('value')->getValue();
      $numericValue = NULL;
      if ($rawValue !== NULL && $rawValue !== '') {
        $numericValue = number_format((float) $rawValue, 1, '.', ' ');
      }

      // Determine if item should be dimmed (empty diagnostic).
      $shouldDim = (bool) $this->getSetting('dim_empty')
        && ($numericValue === NULL || $numericValue === '')
        && ($classLabel === NULL || $classLabel === '' || $classLabel === '?' || $classLabel === 'N/A');

      $elements[$delta] = [
        '#theme' => 'ps_diagnostic_item',
        '#item' => $item,
        '#diagnostic_type' => $diagnosticType,
        '#class_label' => $classLabel,
        '#type_label' => $typeLabel,
        '#numeric_value' => $numericValue,
        '#valid_from' => $item->get('valid_from')->getValue(),
        '#valid_to' => $item->get('valid_to')->getValue(),
        '#no_classification' => $noClassification,
        '#non_applicable' => $nonApplicable,
        '#show_type_label' => (bool) $this->getSetting('show_type_label'),
        '#show_numeric_value' => (bool) $this->getSetting('show_numeric_value'),
        '#show_validity_dates' => (bool) $this->getSetting('show_validity_dates'),
        '#default_layout' => $this->getSetting('default_layout'),
        '#dim_diagnostic' => $shouldDim,
        '#dim_opacity' => (int) $this->getSetting('dim_opacity'),
        '#attached' => [
          'library' => ['ps_diagnostic/formatter'],
        ],
        '#cache' => [
          'tags' => $entity->getCacheTags(),
          'contexts' => ['languages'],
        ],
      ];
    }

    return $elements;
  }

}
