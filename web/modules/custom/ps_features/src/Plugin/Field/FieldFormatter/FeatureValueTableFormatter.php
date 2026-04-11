<?php

declare(strict_types=1);

namespace Drupal\ps_features\Plugin\Field\FieldFormatter;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_feature_value_table' formatter.
 *
 * Displays multiple feature values in a structured table format with
 * columns for feature name, value, and unit. Supports grouping by
 * feature metadata groups for organized presentation.
 *
 * @see \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem
 * @see docs/specs/04-ps-features.md#formatters
 */
#[FieldFormatter(
  id: 'ps_feature_value_table',
  label: new TranslatableMarkup('Feature Value Table'),
  field_types: ['ps_feature_value'],
)]
class FeatureValueTableFormatter extends FormatterBase {

  /**
   * Constructs a FeatureValueTableFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array<string, mixed> $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array<string, mixed> $third_party_settings
   *   Any third party settings.
   * @param \Drupal\ps_features\Service\FeatureManagerInterface $featureManager
   *   The feature manager service.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    private readonly FeatureManagerInterface $featureManager,
    private readonly DictionaryManagerInterface $dictionaryManager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('ps_features.manager'),
      $container->get('ps_dictionary.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_description' => FALSE,
      'group_by' => 'none',
      'show_empty' => FALSE,
      'striped' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_description'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature descriptions'),
      '#default_value' => $this->getSetting('show_description'),
    ];

    $elements['group_by'] = [
      '#type' => 'select',
      '#title' => $this->t('Group by'),
      '#options' => [
        'none' => $this->t('No grouping'),
        'metadata.group' => $this->t('Metadata group'),
      ],
      '#default_value' => $this->getSetting('group_by'),
      '#description' => $this->t('Organize features into sections.'),
    ];

    $elements['show_empty'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show empty values'),
      '#default_value' => $this->getSetting('show_empty'),
      '#description' => $this->t('Display rows even when value is empty.'),
    ];

    $elements['striped'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Striped rows'),
      '#default_value' => $this->getSetting('striped'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    if ($this->getSetting('show_description')) {
      $summary[] = $this->t('Show descriptions: Yes');
    }

    $group_by = $this->getSetting('group_by');
    if ($group_by !== 'none') {
      $summary[] = $this->t('Group by: @group', ['@group' => $group_by]);
    }

    if ($this->getSetting('show_empty')) {
      $summary[] = $this->t('Show empty values: Yes');
    }

    if ($this->getSetting('striped')) {
      $summary[] = $this->t('Striped rows: Yes');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    $rows = [];
    foreach ($items as $item) {
      if (empty($item->feature_id)) {
        continue;
      }

      $feature = $this->featureManager->getFeature($item->feature_id);

      if (!$feature) {
        continue;
      }

      $formatted_value = $this->formatValue($item, $feature);

      if (empty($formatted_value) && !$this->getSetting('show_empty')) {
        continue;
      }

      $row = [
        'feature' => $feature,
        'label' => $feature->label(),
        'value' => $formatted_value,
        'description' => $this->getSetting('show_description') ? $feature->getDescription() : NULL,
      ];

      $rows[] = $row;
    }

    if (empty($rows)) {
      return $elements;
    }

    $group_by = $this->getSetting('group_by');

    if ($group_by === 'metadata.group') {
      $grouped = $this->groupRows($rows);
      $elements[0] = $this->buildGroupedTable($grouped);
    }
    else {
      $elements[0] = $this->buildTable($rows);
    }

    return $elements;
  }

  /**
   * Build table render array.
   *
   * @param array<array<string, mixed>> $rows
   *   Table rows data.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  private function buildTable(array $rows): array {
    $header = [
      $this->t('Feature'),
      $this->t('Value'),
    ];

    if ($this->getSetting('show_description')) {
      $header[] = $this->t('Description');
    }

    $table_rows = [];
    foreach ($rows as $row) {
      $table_row = [
        $row['label'],
        $row['value'],
      ];

      if ($this->getSetting('show_description')) {
        $table_row[] = $row['description'] ?? '';
      }

      $table_rows[] = $table_row;
    }

    return [
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $table_rows,
      '#attributes' => [
        'class' => [
          'ps-features-table',
          $this->getSetting('striped') ? 'striped' : '',
        ],
      ],
      '#attached' => [
        'library' => ['ps_features/formatter.table'],
      ],
    ];
  }

  /**
   * Group rows by metadata group.
   *
   * @param array<array<string, mixed>> $rows
   *   Table rows data.
   *
   * @return array<string, array<array<string, mixed>>>
   *   Grouped rows.
   */
  private function groupRows(array $rows): array {
    $grouped = [];

    foreach ($rows as $row) {
      $metadata = $row['feature']->getMetadata();
      $group = $metadata['group'] ?? $this->t('Other');
      $grouped[(string) $group][] = $row;
    }

    return $grouped;
  }

  /**
   * Build grouped table render array.
   *
   * @param array<string, array<array<string, mixed>>> $grouped
   *   Grouped rows data.
   *
   * @return array<string, mixed>
   *   Render array.
   */
  private function buildGroupedTable(array $grouped): array {
    $build = [];

    foreach ($grouped as $group => $rows) {
      $build[$group] = [
        '#type' => 'details',
        '#title' => $group,
        '#open' => TRUE,
        'table' => $this->buildTable($rows),
      ];
    }

    return $build;
  }

  /**
   * Format value based on type.
   *
   * @param mixed $item
   *   The field item.
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature definition.
   *
   * @return string
   *   Formatted value string.
   */
  private function formatValue($item, $feature): string {
    $value_type = $item->value_type;
    $unit = $feature->getUnit();

    return match ($value_type) {
      'boolean' => $item->value_boolean ? $this->t('Yes') : $this->t('No'),
      'string' => (string) $item->value_string,
      'numeric' => $this->formatNumeric($item->value_numeric, $unit),
      'range' => $this->formatRange($item->value_range_min, $item->value_range_max, $unit),
      'dictionary' => $this->formatDictionary($item->value_string, $item->dictionary_type),
      default => '',
    };
  }

  /**
   * Format numeric value.
   */
  private function formatNumeric(?float $value, ?string $unit): string {
    if ($value === NULL) {
      return '';
    }

    $formatted = number_format($value, 2, '.', ' ');

    if ($unit) {
      $formatted .= ' ' . $unit;
    }

    return $formatted;
  }

  /**
   * Format range value.
   */
  private function formatRange(?float $min, ?float $max, ?string $unit): string {
    if ($min === NULL && $max === NULL) {
      return '';
    }

    $formatted = sprintf(
      '%s - %s',
      $min !== NULL ? number_format($min, 2, '.', ' ') : '?',
      $max !== NULL ? number_format($max, 2, '.', ' ') : '?'
    );

    if ($unit) {
      $formatted .= ' ' . $unit;
    }

    return $formatted;
  }

  /**
   * Format dictionary value.
   */
  private function formatDictionary(?string $code, ?string $dictionary_type): string {
    if (empty($code) || empty($dictionary_type)) {
      return '';
    }

    $label = $this->dictionaryManager->getLabel($dictionary_type, $code);
    return $label ? (string) $label : $code;
  }

}
