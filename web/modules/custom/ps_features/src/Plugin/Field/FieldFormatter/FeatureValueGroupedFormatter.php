<?php

declare(strict_types=1);

namespace Drupal\ps_features\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_feature_value_grouped' formatter.
 *
 * Displays features grouped by FeatureGroup entities in collapsible sections.
 * Supports 4 display modes:
 * - label-only: Show only feature label (boolean=true, no custom text)
 * - boolean-custom: Show label + custom text (boolean=true + value_string)
 * - dictionary: Show label + resolved dictionary value
 * - label-value: Show label + string/numeric/range value.
 *
 * @see \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem
 * @see \Drupal\ps_features\Entity\FeatureGroup
 * @see docs/specs/04-ps-features.md#formatters
 */
#[FieldFormatter(
  id: 'ps_feature_value_grouped',
  label: new TranslatableMarkup('Feature Value Grouped'),
  description: new TranslatableMarkup('Display features organized by groups'),
  field_types: ['ps_feature_value'],
)]
class FeatureValueGroupedFormatter extends FormatterBase {

  /**
   * Constructs a FeatureValueGroupedFormatter object.
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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
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
    private readonly EntityTypeManagerInterface $entityTypeManager,
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
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_empty_groups' => FALSE,
      'collapsible_groups' => TRUE,
      'collapsed_by_default' => FALSE,
      'show_icons' => TRUE,
      'show_units' => TRUE,
      // New: behavior controls.
      'sort_by_weight' => TRUE,
      'merge_duplicates' => TRUE,
      'duplicate_separator' => ', ',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_empty_groups'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show empty groups'),
      '#default_value' => $this->getSetting('show_empty_groups'),
      '#description' => $this->t('Display groups even when they have no features.'),
    ];

    $elements['collapsible_groups'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make groups collapsible'),
      '#default_value' => $this->getSetting('collapsible_groups'),
      '#description' => $this->t('Allow users to expand/collapse groups.'),
    ];

    $elements['collapsed_by_default'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Collapse groups by default'),
      '#default_value' => $this->getSetting('collapsed_by_default'),
      '#description' => $this->t('Groups will be collapsed when page loads.'),
      '#states' => [
        'visible' => [
          ':input[name*="collapsible_groups"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $elements['show_icons'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show group icons'),
      '#default_value' => $this->getSetting('show_icons'),
      '#description' => $this->t('Display icon before group title.'),
    ];

    $elements['show_units'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show units'),
      '#default_value' => $this->getSetting('show_units'),
      '#description' => $this->t('Display unit suffix for numeric/range values.'),
    ];

    // Behavior: sorting & duplicates.
    $elements['sort_by_weight'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Sort features by weight'),
      '#default_value' => $this->getSetting('sort_by_weight'),
      '#description' => $this->t('Order features inside each group using the feature weight.'),
    ];

    $elements['merge_duplicates'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Merge duplicate feature entries'),
      '#default_value' => $this->getSetting('merge_duplicates'),
      '#description' => $this->t('If the same feature appears multiple times, display it once with merged values.'),
    ];

    $elements['duplicate_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Duplicate values separator'),
      '#default_value' => (string) $this->getSetting('duplicate_separator'),
      '#size' => 10,
      '#description' => $this->t('Used when merging multiple values (e.g., dictionaries, strings, numbers).'),
      '#states' => [
        'visible' => [
          ':input[name*="merge_duplicates"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $summary[] = $this->getSetting('show_empty_groups')
      ? $this->t('Show empty groups')
      : $this->t('Hide empty groups');

    if ($this->getSetting('collapsible_groups')) {
      $collapsed = $this->getSetting('collapsed_by_default')
        ? $this->t('collapsed')
        : $this->t('expanded');
      $summary[] = $this->t('Collapsible groups (@state)', ['@state' => $collapsed]);
    }
    else {
      $summary[] = $this->t('Non-collapsible groups');
    }

    $summary[] = $this->getSetting('show_icons')
      ? $this->t('Show icons')
      : $this->t('Hide icons');

    $summary[] = $this->getSetting('show_units')
      ? $this->t('Show units')
      : $this->t('Hide units');

    $summary[] = $this->getSetting('sort_by_weight')
      ? $this->t('Sorted by weight')
      : $this->t('Keep input order');

    $summary[] = $this->getSetting('merge_duplicates')
      ? $this->t('Merge duplicates (@sep)', ['@sep' => (string) $this->getSetting('duplicate_separator')])
      : $this->t('Do not merge duplicates');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];

    if ($items->isEmpty()) {
      return $elements;
    }

    // Group items by feature group.
    $grouped_items = $this->groupItemsByFeatureGroup($items);

    // Load group labels from feature_group dictionary.
    $groups = $this->getGroupsFromDictionary();

    $group_elements = [];

    foreach ($groups as $group_id => $group) {
      $group_items = $grouped_items[$group_id] ?? [];

      // Skip empty groups if configured.
      if (empty($group_items) && !$this->getSetting('show_empty_groups')) {
        continue;
      }

      $group_elements[$group_id] = [
        '#theme' => 'ps_features_grouped',
        '#group' => [
          'id' => $group_id,
          'label' => $group['label'],
          'description' => $group['description'] ?? '',
          'icon' => $this->getSetting('show_icons') ? ($group['icon'] ?? NULL) : NULL,
        ],
        '#features' => $this->buildFeaturesList($group_items),
        '#collapsible' => $this->getSetting('collapsible_groups'),
        '#collapsed' => $this->getSetting('collapsed_by_default'),
      ];
    }

    if (!empty($group_elements)) {
      $elements[0] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-features-grouped-formatter']],
        '#attached' => ['library' => ['ps_features/formatter.grouped']],
        'groups' => $group_elements,
      ];
    }

    return $elements;
  }

  /**
   * Gets groups from feature_group dictionary.
   *
   * @return array<string, array<string, mixed>>
   *   Array of groups keyed by lowercase group code.
   */
  private function getGroupsFromDictionary(): array {
    $groups = [];

    try {
      $entries = $this->dictionaryManager->getEntries('feature_group');
      foreach ($entries as $entry) {
        $code = $entry->get('code');
        $metadata = $entry->getMetadata();
        $groups[strtolower($code)] = [
          'label' => $entry->label(),
          'description' => $entry->getDescription() ?? '',
          'icon' => $metadata['icon'] ?? NULL,
          'weight' => $entry->getWeight(),
        ];
      }

      // Sort by weight.
      uasort($groups, static fn($a, $b) => $a['weight'] <=> $b['weight']);
    }
    catch (\Throwable $e) {
      // Dictionary not available, return empty.
    }

    return $groups;
  }

  /**
   * Builds the list of features for display.
   *
   * @param array<int, mixed> $items
   *   Array of field items.
   *
   * @return array
   *   Array of feature data for rendering.
   */
  protected function buildFeaturesList(array $items): array {
    $features = [];

    // When merging duplicates, aggregate by feature id.
    $merge = (bool) $this->getSetting('merge_duplicates');
    $separator = (string) $this->getSetting('duplicate_separator');

    if ($merge) {
      $by_id = [];
      foreach ($items as $item) {
        if (empty($item->feature_id)) {
          continue;
        }
        $feature = $this->featureManager->getFeature($item->feature_id);
        if (!$feature) {
          continue;
        }

        $display_value = $this->formatValue($item, $feature);
        if (!isset($by_id[$item->feature_id])) {
          $by_id[$item->feature_id] = [
            'feature' => $feature,
            'values' => [],
            'has_label_only' => FALSE,
            'yesno_true' => FALSE,
            'type' => $this->getDisplayType($item, $feature),
          ];
        }

        if ($display_value === '') {
          $by_id[$item->feature_id]['has_label_only'] = TRUE;
        }
        else {
          $by_id[$item->feature_id]['values'][] = $display_value;
        }

        // Track yes/no truthiness for merge semantics.
        if ($item->value_type === 'yesno') {
          $by_id[$item->feature_id]['yesno_true'] = $by_id[$item->feature_id]['yesno_true'] || (bool) $item->value_boolean;
        }
      }

      foreach ($by_id as $fid => $data) {
        $feature = $data['feature'];
        $value = '';
        if ($data['type'] === 'dictionary' || $data['type'] === 'label-value' || $data['type'] === 'boolean-custom') {
          if ($data['type'] === 'label-value' && $feature->getValueType() === 'yesno') {
            $value = $data['yesno_true'] ? (string) $this->t('Yes') : (string) $this->t('No');
          }
          else {
            $unique = array_values(array_unique($data['values']));
            if (!empty($unique)) {
              $value = implode($separator, $unique);
            }
          }
        }

        $features[] = [
          'id' => $feature->id(),
          'label' => $feature->label(),
          'value' => $value,
          'type' => $data['type'],
          'weight' => $feature->getWeight(),
        ];
      }
    }
    else {
      foreach ($items as $item) {
        if (empty($item->feature_id)) {
          continue;
        }
        $feature = $this->featureManager->getFeature($item->feature_id);
        if (!$feature) {
          continue;
        }
        $display_value = $this->formatValue($item, $feature);
        if ($display_value !== NULL) {
          $features[] = [
            'id' => $feature->id(),
            'label' => $feature->label(),
            'value' => $display_value,
            'type' => $this->getDisplayType($item, $feature),
            'weight' => $feature->getWeight(),
          ];
        }
      }
    }

    if ($this->getSetting('sort_by_weight')) {
      // Sort features by weight (ascending). Stable for identical weights.
      usort(
        $features,
        static function (array $a, array $b): int {
          return $a['weight'] <=> $b['weight'];
        }
      );
    }

    return $features;
  }

  /**
   * Formats a single feature value for display.
   *
   * @param mixed $item
   *   The field item.
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   *
   * @return string|null
   *   Formatted value or NULL to skip display.
   */
  protected function formatValue($item, $feature): ?string {
    $value_type = $feature->getValueType();

    switch ($value_type) {
      case 'flag':
        // Flag features display only the label, no value.
        // Return empty string to indicate "display label only".
        return '';

      case 'yesno':
        // Yes/No always displays, showing Yes or No based on value.
        return $item->value_boolean ? (string) $this->t('Yes') : (string) $this->t('No');

      case 'dictionary':
        if (empty($item->value_string)) {
          return NULL;
        }
        $dictionary_type = $feature->getDictionaryType();
        return $this->dictionaryManager->getLabel($dictionary_type, $item->value_string);

      case 'string':
        return !empty($item->value_string) ? $item->value_string : NULL;

      case 'numeric':
        if ($item->value_numeric === NULL) {
          return NULL;
        }
        $value = number_format((float) $item->value_numeric, 2, '.', ' ');
        if ($this->getSetting('show_units') && $unit = $feature->getUnit()) {
          $value .= ' ' . $unit;
        }
        return $value;

      case 'range':
        if ($item->value_range_min === NULL && $item->value_range_max === NULL) {
          return NULL;
        }
        $min = $item->value_range_min !== NULL ?
          number_format((float) $item->value_range_min, 2, '.', ' ') : '?';
        $max = $item->value_range_max !== NULL ?
          number_format((float) $item->value_range_max, 2, '.', ' ') : '?';
        $value = "{$min} - {$max}";
        if ($this->getSetting('show_units') && $unit = $feature->getUnit()) {
          $value .= ' ' . $unit;
        }
        return $value;

      default:
        return NULL;
    }
  }

  /**
   * Determines the display type for a feature.
   *
   * @param mixed $item
   *   The field item.
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   *
   * @return string
   *   One of: 'label-only', 'boolean-custom', 'dictionary', 'label-value'.
   */
  protected function getDisplayType($item, $feature): string {
    $value_type = $feature->getValueType();

    if ($value_type === 'flag') {
      return 'label-only';
    }

    if ($value_type === 'boolean') {
      return !empty($item->value_string) ? 'boolean-custom' : 'label-only';
    }

    if ($value_type === 'yesno') {
      return 'label-value';
    }

    if ($value_type === 'dictionary') {
      return 'dictionary';
    }

    return 'label-value';
  }

  /**
   * Groups field items by their feature's group.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field items.
   *
   * @return array<string, array<int, mixed>>
   *   Items grouped by group ID.
   */
  protected function groupItemsByFeatureGroup(FieldItemListInterface $items): array {
    $grouped = [];

    foreach ($items as $delta => $item) {
      // Skip items without feature_id.
      if (empty($item->feature_id)) {
        continue;
      }

      $feature = $this->featureManager->getFeature($item->feature_id);
      if ($feature) {
        $group_id = $feature->getGroup() ?? 'ungrouped';
        $grouped[$group_id][$delta] = $item;
      }
    }

    return $grouped;
  }

}
