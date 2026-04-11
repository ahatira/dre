<?php

declare(strict_types=1);

namespace Drupal\ps_features\Plugin\Field\FieldWidget;

use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_feature_value_grouped' widget.
 *
 * Provides a grouped interface for editing feature values organized by
 * feature groups from the feature_group dictionary (Equipments, Services, etc.).
 * Features are displayed in expandable sections with drag-and-drop reordering.
 *
 * Key features:
 * - Organized by feature_group dictionary entries
 * - Drag-and-drop within groups for ordering
 * - Modal browser for adding features
 * - Adaptive value fields based on feature value_type
 * - Inline editing of existing values
 * - Delete confirmation for removing features.
 *
 * @see \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem
 * @see docs/specs/04-ps-features.md#widgets
 */
#[FieldWidget(
  id: 'ps_feature_value_grouped',
  label: new TranslatableMarkup('Feature Value Grouped'),
  description: new TranslatableMarkup('Organize features by groups with drag-and-drop support'),
  field_types: ['ps_feature_value'],
)]
class FeatureValueGroupedWidget extends WidgetBase {

  /**
   * Constructs a FeatureValueGroupedWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array<string, mixed> $settings
   *   The widget settings.
   * @param array<string, mixed> $third_party_settings
   *   Any third party settings.
   * @param \Drupal\ps_features\Service\FeatureManagerInterface $featureManager
   *   The feature manager service.
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly FeatureManagerInterface $featureManager,
    private readonly DictionaryManagerInterface $dictionaryManager,
    private readonly RendererInterface $renderer,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
      $configuration['third_party_settings'],
      $container->get('ps_features.manager'),
      $container->get('ps_dictionary.manager'),
      $container->get('renderer'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_descriptions' => TRUE,
      'allow_reorder' => TRUE,
      'collapsed_groups' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function isMultiple(): bool {
    // This widget handles all field items (deltas) at once.
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_descriptions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature descriptions'),
      '#default_value' => $this->getSetting('show_descriptions'),
      '#description' => $this->t('Display description text below each feature.'),
    ];

    $elements['allow_reorder'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow drag-and-drop reordering'),
      '#default_value' => $this->getSetting('allow_reorder'),
      '#description' => $this->t('Enable drag handles for reordering features within groups.'),
    ];

    $elements['collapsed_groups'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Collapse groups by default'),
      '#default_value' => $this->getSetting('collapsed_groups'),
      '#description' => $this->t('Groups will be collapsed when the form loads.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $summary[] = $this->getSetting('show_descriptions')
      ? $this->t('Show descriptions: Yes')
      : $this->t('Show descriptions: No');

    $summary[] = $this->getSetting('allow_reorder')
      ? $this->t('Reordering: Enabled')
      : $this->t('Reordering: Disabled');

    $summary[] = $this->getSetting('collapsed_groups')
      ? $this->t('Groups: Collapsed by default')
      : $this->t('Groups: Expanded by default');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    // This widget handles all items at once, not per-delta.
    if ($delta > 0) {
      return [];
    }

    // Get all features grouped by dictionary groups.
    /** @var array<string, array{label: string, weight: int, features: array<\Drupal\ps_features\Entity\FeatureInterface>}> $all_features_grouped */
    $all_features_grouped = $this->featureManager->getFeaturesByGroup();

    // Prepare dictionary options for front-end (for dictionary-type features).
    $dictionary_options = [];
    foreach ($all_features_grouped as $group_id => $group_data) {
      foreach ($group_data['features'] as $feature) {
        if ($feature->getValueType() === 'dictionary') {
          $dict_type = $feature->getDictionaryType();
          if ($dict_type && !isset($dictionary_options[$dict_type])) {
            // Ensure all labels are plain strings for JSON encoding into drupalSettings.
            $raw_options = $this->dictionaryManager->getOptions($dict_type);
            $dictionary_options[$dict_type] = array_map(static fn($v) => (string) $v, $raw_options);
          }
        }
      }
    }

    // Prepare existing values for JavaScript.
    $existing_values = [];
    foreach ($items as $item) {
      if (!empty($item->feature_id)) {
        // Load feature entity to get metadata.
        $feature = $this->featureManager->getFeature($item->feature_id);
        if (!$feature) {
          continue;
        }

        $item_data = [
          'feature_id' => $item->feature_id,
          'feature_label' => (string) $feature->label(),
          'feature_type' => $feature->getValueType(),
          'feature_unit' => $feature->getUnit() ?? '',
          'feature_description' => (string) $feature->getDescription(),
          'feature_required' => $feature->isRequired(),
          'value_type' => $item->value_type,
          'config' => [
            'value_boolean' => (bool) $item->value_boolean,
            'value_string' => (string) $item->value_string,
            'value_numeric' => $item->value_numeric !== NULL ? (float) $item->value_numeric : NULL,
            'value_range_min' => $item->value_range_min !== NULL ? (float) $item->value_range_min : NULL,
            'value_range_max' => $item->value_range_max !== NULL ? (float) $item->value_range_max : NULL,
          ],
        ];

        // Add dictionary-specific data if applicable.
        if ($feature->getValueType() === 'dictionary') {
          $dict_type = $feature->getDictionaryType();
          $item_data['feature_dictionary_type'] = $dict_type ?? '';
          if ($dict_type && isset($dictionary_options[$dict_type])) {
            $item_data['feature_dictionary_options'] = $dictionary_options[$dict_type];
          }
        }

        $existing_values[] = $item_data;
      }
    }

    // Build the widget using the drag-and-drop template.
    $element = [
      '#theme' => 'ps_feature_widget_builder',
      '#features_by_group' => $all_features_grouped,
      '#dictionary_options' => $dictionary_options,
      '#existing_values' => $existing_values,
      '#field_name' => $this->fieldDefinition->getName(),
      '#delta' => $delta,
      '#element' => $element,
      '#cache' => [
        'tags' => ['ps_features_list'],
      ],
      '#attached' => [
        'library' => ['ps_features/widget.builder'],
        'drupalSettings' => [
          'ps_features' => [
            'dictionaries' => $dictionary_options,
          ],
        ],
      ],
      // Hidden input to store JSON data - will be populated by JavaScript.
      'data' => [
        '#type' => 'hidden',
        '#default_value' => json_encode($existing_values),
        '#attributes' => [
          'class' => ['ps-feature-builder-data'],
          'data-field-name' => $this->fieldDefinition->getName(),
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */

  /**
   * {@inheritdoc}
   *
   * @param array<int, array<string, mixed>> $values
   *   Submitted values keyed by delta.
   * @param array<string, mixed> $form
   *   The form structure.
   *
   * @return array<int, array<string, mixed>>
   *   Normalized field values.
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $massaged = [];

    // Extract JSON data from hidden input.
    if (isset($values[0]['data'])) {
      $json_data = $values[0]['data'];
      $decoded = json_decode($json_data, TRUE);

      if (is_array($decoded)) {
        foreach ($decoded as $item) {
          if (!empty($item['feature_id'])) {
            $feature_id = $item['feature_id'];
            $value_type = $item['feature_type'] ?? '';
            $config = $item['config'] ?? [];

            // Map configuration to field values based on type.
            $field_item = [
              'feature_id' => $feature_id,
              'value_type' => $value_type,
              'value_boolean' => NULL,
              'value_string' => NULL,
              'value_numeric' => NULL,
              'value_range_min' => NULL,
              'value_range_max' => NULL,
            ];

            // Extract values from config based on type.
            switch ($value_type) {
              case 'yesno':
                $field_item['value_boolean'] = !empty($config['value_boolean']);
                break;

              case 'string':
                $field_item['value_string'] = $config['value_string'] ?? '';
                break;

              case 'numeric':
                $field_item['value_numeric'] = $config['value_numeric'] ?? NULL;
                break;

              case 'range':
                $field_item['value_range_min'] = $config['value_range_min'] ?? NULL;
                $field_item['value_range_max'] = $config['value_range_max'] ?? NULL;
                break;

              case 'dictionary':
                $field_item['value_string'] = $config['value_string'] ?? '';
                break;
            }

            // Always include complement if present
            $field_item['complement'] = $config['complement'] ?? NULL;

            $massaged[] = $field_item;
          }
        }
      }
    }

    return $massaged;
  }

}
