<?php

declare(strict_types=1);

namespace Drupal\ps_features\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_feature_value_default' widget.
 *
 * Provides a comprehensive form interface for editing feature values with
 * dynamic field visibility based on selected value type. Supports all 5
 * value types (boolean, dictionary, string, numeric, range) with appropriate
 * input elements and validation.
 *
 * @see \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem
 * @see docs/specs/04-ps-features.md#widgets
 */
#[FieldWidget(
  id: 'ps_feature_value_default',
  label: new TranslatableMarkup('Feature Value Default'),
  field_types: ['ps_feature_value'],
)]
class FeatureValueDefaultWidget extends WidgetBase {

  /**
   * Constructs a FeatureValueDefaultWidget object.
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
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly FeatureManagerInterface $featureManager,
    private readonly DictionaryManagerInterface $dictionaryManager,
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
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'show_description' => TRUE,
      'placeholder_text' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_description'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show feature description'),
      '#default_value' => $this->getSetting('show_description'),
    ];

    $elements['placeholder_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder text'),
      '#default_value' => $this->getSetting('placeholder_text'),
      '#description' => $this->t('Text to display when no value is entered.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    if ($this->getSetting('show_description')) {
      $summary[] = $this->t('Show description: Yes');
    }

    if ($placeholder = $this->getSetting('placeholder_text')) {
      $summary[] = $this->t('Placeholder: @placeholder', ['@placeholder' => $placeholder]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $item = $items[$delta];

    // Add CSS/JS library.
    $element['#attached']['library'][] = 'ps_features/widget.default';
    $element['#attributes']['class'][] = 'ps-feature-value-default';

    // Pass features metadata to JavaScript for dynamic field generation.
    $features_metadata = [];
    $features = $this->featureManager->getFeatures();
    foreach ($features as $feature) {
      $features_metadata[$feature->id()] = [
        'label' => $feature->label(),
        'value_type' => $feature->getValueType(),
        'dictionary_type' => $feature->getDictionaryType(),
        'unit' => $feature->getUnit(),
        'description' => $feature->getDescription(),
        'validation_rules' => $feature->getValidationRules(),
      ];
    }

    // Get dictionary options for all dictionary types used by features.
    $dictionary_options = [];
    foreach ($features as $feature) {
      if ($feature->getValueType() === 'dictionary' && $dict_type = $feature->getDictionaryType()) {
        if (!isset($dictionary_options[$dict_type])) {
          $options = $this->dictionaryManager->getOptions($dict_type);
          // Only add non-empty dictionaries.
          if (!empty($options)) {
            $dictionary_options[$dict_type] = $options;
          }
        }
      }
    }

    $element['#attached']['drupalSettings']['psFeatures']['features'] = $features_metadata;
    $element['#attached']['drupalSettings']['psFeatures']['dictionaries'] = $dictionary_options;
    $element['#attached']['drupalSettings']['psFeatures']['settings'] = [
      'showDescription' => $this->getSetting('show_description'),
      'placeholder_text' => $this->getSetting('placeholder_text'),
    ];

    // Feature selection.
    $feature_options = ['' => $this->t('- Select a feature -')];
    foreach ($features as $feature) {
      $feature_options[$feature->id()] = $feature->label();
    }

    /** @phpstan-var \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem $item */
    $element['feature_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Feature'),
      '#options' => $feature_options,
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('feature_id')->getValue() ?? '',
      '#required' => $element['#required'] ?? FALSE,
      '#attributes' => [
        'class' => ['feature-select'],
        'data-delta' => $delta,
      ],
      '#wrapper_attributes' => ['class' => ['feature-select-wrapper']],
    ];

    // Hidden fields for all value types (will be shown/hidden by JS).
    $element['value_type'] = [
      '#type' => 'hidden',
      '#attributes' => ['class' => ['field-value-type']],
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('value_type')->getValue() ?? '',
    ];

    $element['dictionary_type'] = [
      '#type' => 'hidden',
      '#attributes' => ['class' => ['field-dictionary-type']],
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('dictionary_type')->getValue() ?? '',
    ];

    // Container for dynamic value fields.
    $element['value_fields'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['value-fields-container']],
    ];

    // Flag field - presence means TRUE, no checkbox needed.
    $element['value_fields']['flag_message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="field-flag-message">' . $this->t('✓ Feature is present (TRUE)') . '</div>',
      '#wrapper_attributes' => ['class' => ['field-wrapper', 'field-wrapper-flag'], 'style' => 'display:none;'],
    ];

    // Hidden field to store TRUE value for flag type.
    $element['value_fields']['flag_value_hidden'] = [
      '#type' => 'hidden',
      '#value' => TRUE,
      '#attributes' => ['class' => ['field-flag-value']],
    ];

    // Yesno field - checkbox for yes/no choice.
    $element['value_fields']['value_boolean'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Yes'),
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('value_boolean')->getValue() ?? FALSE,
      '#attributes' => ['class' => ['field-boolean']],
      '#wrapper_attributes' => ['class' => ['field-wrapper', 'field-wrapper-yesno'], 'style' => 'display:none;'],
    ];

    // Hidden field to store TRUE value for flag/yesno types.
    $element['value_fields']['value_boolean_hidden'] = [
      '#type' => 'hidden',
      '#value' => TRUE,
      '#attributes' => ['class' => ['field-boolean-value']],
    ];

    // String field (for string and dictionary types).
    $element['value_fields']['value_string'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Value'),
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('value_string')->getValue() ?? '',
      '#maxlength' => 255,
      '#attributes' => ['class' => ['field-string']],
      '#wrapper_attributes' => ['class' => ['field-wrapper', 'field-wrapper-string'], 'style' => 'display:none;'],
    ];

    // Dictionary select (will be populated by JS).
    $element['value_fields']['value_dictionary'] = [
      '#type' => 'select',
      '#title' => $this->t('Value'),
      '#options' => ['' => $this->t('- Select -')],
      '#default_value' => $item->get('value_string')->getValue() ?? '',
      '#attributes' => ['class' => ['field-dictionary']],
      '#wrapper_attributes' => ['class' => ['field-wrapper', 'field-wrapper-dictionary'], 'style' => 'display:none;'],
    ];

    // Numeric field.
    $element['value_fields']['value_numeric'] = [
      '#type' => 'number',
      '#title' => $this->t('Value'), /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('value_numeric')->getValue() ?? NULL,
      '#step' => 0.01,
      '#attributes' => ['class' => ['field-numeric']],
      '#wrapper_attributes' => ['class' => ['field-wrapper', 'field-wrapper-numeric'], 'style' => 'display:none;'],
    ];

    // Range fields.
    $element['value_fields']['range_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['range-fields-wrapper', 'field-wrapper', 'field-wrapper-range'],
        'style' => 'display:none;',
      ],
    ];

    $element['value_fields']['range_container']['value_range_min'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum'),
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('value_range_min')->getValue() ?? NULL,
      '#step' => 0.01,
      '#attributes' => ['class' => ['field-range-min']],
    ];

    $element['value_fields']['range_container']['separator'] = [
      '#markup' => '<span class="range-separator">→</span>',
    ];

    $element['value_fields']['range_container']['value_range_max'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum'),
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('value_range_max')->getValue() ?? NULL,
      '#step' => 0.01,
      '#attributes' => ['class' => ['field-range-max']],
    ];

    // Unit suffix (will be updated by JS).
    $element['value_fields']['unit_display'] = [
      '#markup' => '<span class="unit-suffix" style="display:none;"></span>',
    ];

    // Complement free text available for all features.
    $element['value_fields']['complement'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Complement'),
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('complement')->getValue() ?? '',
      '#maxlength' => 255,
      '#attributes' => ['class' => ['field-complement']],
      '#wrapper_attributes' => ['class' => ['field-wrapper', 'field-wrapper-complement'], 'style' => 'display:none;'],
    ];

    // Description (will be updated by JS).
    if ($this->getSetting('show_description')) {
      $element['value_fields']['description'] = [
        '#markup' => '<div class="feature-description" style="display:none;"></div>',
        '#weight' => -10,
      ];
    }

    return $element;
  }

  /**
   * AJAX callback to update value fields when feature changes.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The updated form element.
   */
  public static function ajaxUpdateValueFields(array &$form, FormStateInterface $form_state): array {
    $triggering_element = $form_state->getTriggeringElement();
    if (!$triggering_element) {
      return ['#markup' => ''];
    }

    $parents = array_slice($triggering_element['#parents'], 0, -1);
    $parents[] = 'value_fields';

    $element = $form;
    foreach ($parents as $parent) {
      if (!isset($element[$parent])) {
        return ['#markup' => ''];
      }
      $element = $element[$parent];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * @param array<int, mixed> $values
   *   The form values.
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array<int, mixed>
   *   The massaged values.
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    foreach ($values as &$value) {
      // Clean up empty values.
      if (empty($value['feature_id'])) {
        $value = [];
        continue;
      }

      // Flatten value_fields structure.
      if (isset($value['value_fields'])) {
        $value = array_merge($value, $value['value_fields']);
        unset($value['value_fields']);
      }
    }

    return $values;
  }

}
