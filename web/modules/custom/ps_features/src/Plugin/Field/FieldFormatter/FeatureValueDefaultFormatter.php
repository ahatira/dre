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
use Drupal\ps_features\Entity\FeatureInterface;
use Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_feature_value_default' formatter.
 *
 * Displays feature values with appropriate formatting based on value type.
 * Provides options to show/hide feature labels, units, and descriptions.
 *
 * @see \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem
 * @see docs/specs/04-ps-features.md#formatters
 */
#[FieldFormatter(
  id: 'ps_feature_value_default',
  label: new TranslatableMarkup('Feature Value Default'),
  field_types: ['ps_feature_value'],
)]
class FeatureValueDefaultFormatter extends FormatterBase {

  /**
   * Constructs a FeatureValueDefaultFormatter object.
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
      'hide_empty' => TRUE,
      'show_label' => TRUE,
      'show_unit' => TRUE,
      'show_description' => FALSE,
      'show_complement' => TRUE,
      'empty_text' => '',
      // Behavior controls.
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

    $elements['hide_empty'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide empty values'),
      '#default_value' => $this->getSetting('hide_empty'),
      '#description' => $this->t('Do not render features with no value.'),
    ];

    $elements['show_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature label'),
      '#default_value' => $this->getSetting('show_label'),
    ];

    $elements['show_unit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show unit'),
      '#default_value' => $this->getSetting('show_unit'),
      '#description' => $this->t('Display unit suffix for numeric and range values.'),
    ];

    $elements['show_description'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature description'),
      '#default_value' => $this->getSetting('show_description'),
    ];

    $elements['show_complement'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show complement free text'),
      '#default_value' => $this->getSetting('show_complement'),
      '#description' => $this->t('Displays the additional complement text stored with the value item if present.'),
    ];

    $elements['empty_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Empty text'),
      '#default_value' => $this->getSetting('empty_text'),
      '#description' => $this->t('Text to display when no value is set.'),
    ];

    // Behavior: sorting & duplicates.
    $elements['sort_by_weight'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Sort features by weight'),
      '#default_value' => $this->getSetting('sort_by_weight'),
      '#description' => $this->t('Order items using the feature weight.'),
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

    $summary[] = $this->getSetting('hide_empty')
      ? $this->t('Hide empty: Yes')
      : $this->t('Hide empty: No');

    if ($this->getSetting('show_label')) {
      $summary[] = $this->t('Show label: Yes');
    }

    if ($this->getSetting('show_unit')) {
      $summary[] = $this->t('Show unit: Yes');
    }

    if ($this->getSetting('show_description')) {
      $summary[] = $this->t('Show description: Yes');
    }

    if ($this->getSetting('show_complement')) {
      $summary[] = $this->t('Show complement: Yes');
    }

    if ($empty_text = $this->getSetting('empty_text')) {
      $summary[] = $this->t('Empty text: @text', ['@text' => $empty_text]);
    }

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

    $merge = (bool) $this->getSetting('merge_duplicates');
    $sort = (bool) $this->getSetting('sort_by_weight');
    $separator = (string) $this->getSetting('duplicate_separator');

    if ($merge) {
      /**
       * @var array<string, array{
       *   feature: FeatureInterface,
       *   values: array<int, string>,
       *   has_label_only: bool,
       *   yesno_true: bool,
       * }> $by_id
       */
      $by_id = [];
      foreach ($items as $item) {
        if (!$item instanceof FeatureValueItem) {
          continue;
        }

        $featureId = (string) ($item->get('feature_id')->getValue() ?? '');
        if ($featureId === '') {
          continue;
        }

        $feature = $this->featureManager->getFeature($featureId);
        if (!$feature) {
          continue;
        }

        $formatted = $this->formatValue($item, $feature);

        if (!isset($by_id[$featureId])) {
          $by_id[$featureId] = [
            'feature' => $feature,
            'values' => [],
            'has_label_only' => FALSE,
            'yesno_true' => FALSE,
          ];
        }

        if ($formatted === '') {
          $by_id[$featureId]['has_label_only'] = TRUE;
        }
        else {
          $by_id[$featureId]['values'][] = $formatted;
        }

        $valueType = (string) ($item->get('value_type')->getValue() ?? '');
        if ($valueType === 'yesno') {
          $by_id[$featureId]['yesno_true'] = $by_id[$featureId]['yesno_true'] || (bool) ($item->get('value_boolean')->getValue());
        }
      }

      /**
       * @var array<int, array{weight: int, element: array<string, mixed>}> $list
       */
      $list = [];
      foreach ($by_id as $fid => $data) {
        $feature = $data['feature'];
        $value = '';

        $value_type = $feature->getValueType();
        if ($value_type === 'yesno') {
          $value = $data['yesno_true'] ? (string) $this->t('Yes') : (string) $this->t('No');
        }
        else {
          $unique = array_values(array_unique($data['values']));
          if (!empty($unique)) {
            $value = implode($separator, $unique);
          }
        }

        // Handle empty/hide logic after merge.
        if ($value === '') {
          if ($this->getSetting('hide_empty') && !$data['has_label_only']) {
            continue;
          }
          if ($this->getSetting('empty_text') !== '') {
            $value = (string) $this->getSetting('empty_text');
          }
        }

        $list[] = [
          'weight' => method_exists($feature, 'getWeight') ? (int) $feature->getWeight() : 0,
          'element' => [
            '#theme' => 'ps_feature_value',
            '#feature_id' => $fid,
            '#feature_label' => $this->getSetting('show_label') ? $feature->label() : NULL,
            '#value' => $value,
            '#value_type' => $value_type,
            '#description' => $this->getSetting('show_description') ? $feature->getDescription() : NULL,
          ],
        ];
      }

      if ($sort) {
        usort($list, static fn(array $a, array $b): int => $a['weight'] <=> $b['weight']);
      }

      foreach ($list as $i => $row) {
        $elements[$i] = $row['element'];
      }
    }
    else {
      /**
       * @var array<int, array{weight: int, element: array<string, mixed>}> $list
       */
      $list = [];
      foreach ($items as $item) {
        if (!$item instanceof FeatureValueItem) {
          continue;
        }

        $featureId = (string) ($item->get('feature_id')->getValue() ?? '');
        if ($featureId === '') {
          continue;
        }

        $feature = $this->featureManager->getFeature($featureId);
        if (!$feature) {
          continue;
        }

        $formatted_value = $this->formatValue($item, $feature);

        // Handle empty values according to settings.
        if ($formatted_value === '') {
          if ($this->getSetting('hide_empty')) {
            continue;
          }
          if ($this->getSetting('empty_text') !== '') {
            $formatted_value = (string) $this->getSetting('empty_text');
          }
        }

        $valueType = (string) ($item->get('value_type')->getValue() ?? '');

        $list[] = [
          'weight' => method_exists($feature, 'getWeight') ? (int) $feature->getWeight() : 0,
          'element' => [
            '#theme' => 'ps_feature_value',
            '#feature_id' => $featureId,
            '#feature_label' => $this->getSetting('show_label') ? $feature->label() : NULL,
            '#value' => $formatted_value,
            '#value_type' => $valueType,
            '#description' => $this->getSetting('show_description') ? $feature->getDescription() : NULL,
          ],
        ];
      }

      if ($sort) {
        usort($list, static fn(array $a, array $b): int => $a['weight'] <=> $b['weight']);
      }

      foreach ($list as $i => $row) {
        $elements[$i] = $row['element'];
      }
    }

    return $elements;
  }

  /**
   * Format value based on type.
   *
   * @param \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem $item
   *   The field item.
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature definition.
   *
   * @return string
   *   Formatted value string.
   */
  private function formatValue(FeatureValueItem $item, FeatureInterface $feature): string {
    $valueType = (string) ($item->get('value_type')->getValue() ?? '');
    $show_unit = $this->getSetting('show_unit');
    $unit = $feature->getUnit();

    $valueBoolean = (bool) ($item->get('value_boolean')->getValue());
    $valueStringRaw = $item->get('value_string')->getValue();
    $valueString = $valueStringRaw === NULL ? NULL : (string) $valueStringRaw;
    $valueNumericRaw = $item->get('value_numeric')->getValue();
    $valueNumeric = is_numeric($valueNumericRaw) ? (float) $valueNumericRaw : NULL;
    $rangeMinRaw = $item->get('value_range_min')->getValue();
    $rangeMin = is_numeric($rangeMinRaw) ? (float) $rangeMinRaw : NULL;
    $rangeMaxRaw = $item->get('value_range_max')->getValue();
    $rangeMax = is_numeric($rangeMaxRaw) ? (float) $rangeMaxRaw : NULL;
    $dictionaryTypeRaw = $item->get('dictionary_type')->getValue();
    $dictionaryType = $dictionaryTypeRaw !== NULL ? (string) $dictionaryTypeRaw : NULL;

    $formatted = match ($valueType) {
      'flag' => (string) $this->t('Present'),
      'yesno', 'boolean' => $valueBoolean ? (string) $this->t('Yes') : (string) $this->t('No'),
      'string' => $valueString !== NULL && $valueString !== '' ? $valueString : '',
      'numeric' => $this->formatNumeric(
        $valueNumeric,
        $unit,
        $show_unit
      ),
      'range' => $this->formatRange(
        $rangeMin,
        $rangeMax,
        $unit,
        $show_unit
      ),
      'dictionary' => $this->formatDictionary(
        $valueString,
        $dictionaryType
      ),
      default => '',
    };

    if ($this->getSetting('show_complement')) {
      $complementRaw = $item->get('complement')->getValue();
      $comp = trim((string) ($complementRaw ?? ''));
      if ($comp !== '') {
        $formatted = $formatted !== '' ? $formatted . ' - ' . $comp : $comp;
      }
    }

    return $formatted;
  }

  /**
   * Format numeric value.
   *
   * @param float|null $value
   *   The numeric value.
   * @param string|null $unit
   *   The unit suffix.
   * @param bool $show_unit
   *   Whether to show the unit.
   *
   * @return string
   *   Formatted numeric value.
   */
  private function formatNumeric(?float $value, ?string $unit, bool $show_unit): string {
    if ($value === NULL) {
      return '';
    }

    $formatted = number_format($value, 2, '.', ' ');

    if ($show_unit && $unit) {
      $formatted .= ' ' . $unit;
    }

    return $formatted;
  }

  /**
   * Format range value.
   *
   * @param float|null $min
   *   The minimum value.
   * @param float|null $max
   *   The maximum value.
   * @param string|null $unit
   *   The unit suffix.
   * @param bool $show_unit
   *   Whether to show the unit.
   *
   * @return string
   *   Formatted range value.
   */
  private function formatRange(?float $min, ?float $max, ?string $unit, bool $show_unit): string {
    if ($min === NULL && $max === NULL) {
      return '';
    }

    $min_formatted = $min !== NULL ? number_format($min, 2, '.', ' ') : '';
    $max_formatted = $max !== NULL ? number_format($max, 2, '.', ' ') : '';

    if ($min !== NULL && $max !== NULL) {
      $formatted = sprintf('%s - %s', $min_formatted, $max_formatted);
    }
    elseif ($min !== NULL) {
      $formatted = sprintf('%s %s', $this->t('From'), $min_formatted);
    }
    else {
      $formatted = sprintf('%s %s', $this->t('Up to'), $max_formatted);
    }

    if ($show_unit && $unit) {
      $formatted .= ' ' . $unit;
    }

    return $formatted;
  }

  /**
   * Format dictionary value.
   *
   * @param string|null $code
   *   The dictionary code.
   * @param string|null $dictionary_type
   *   The dictionary type.
   *
   * @return string
   *   Formatted dictionary label.
   */
  private function formatDictionary(?string $code, ?string $dictionary_type): string {
    if (empty($code) || empty($dictionary_type)) {
      return '';
    }

    $label = $this->dictionaryManager->getLabel($dictionary_type, $code);
    return $label ? (string) $label : $code;
  }

}
