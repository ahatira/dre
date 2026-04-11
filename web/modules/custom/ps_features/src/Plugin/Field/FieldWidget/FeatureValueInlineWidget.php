<?php

declare(strict_types=1);

namespace Drupal\ps_features\Plugin\Field\FieldWidget;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_features\Service\FeatureManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'ps_feature_value_inline' widget.
 *
 * Provides a compact inline form interface for editing feature values,
 * optimized for bulk entry and space-constrained layouts. Displays all
 * fields in a single row with minimal labels.
 *
 * @see \Drupal\ps_features\Plugin\Field\FieldType\FeatureValueItem
 * @see docs/specs/04-ps-features.md#widgets
 */
#[FieldWidget(
  id: 'ps_feature_value_inline',
  label: new TranslatableMarkup('Feature Value Inline'),
  field_types: ['ps_feature_value'],
)]
class FeatureValueInlineWidget extends WidgetBase {

  /**
   * Constructs a FeatureValueInlineWidget object.
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
   *
   * @phpstan-return array<string, mixed>
   */
  public static function defaultSettings(): array {
    return [
      'compact' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['compact'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Compact mode'),
      '#default_value' => $this->getSetting('compact'),
      '#description' => $this->t('Display fields in a single row without labels.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    if ($this->getSetting('compact')) {
      $summary[] = $this->t('Compact mode enabled');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $item = $items[$delta];

    // Add inline container class.
    $element['#attributes']['class'][] = 'ps-feature-value-inline';
    $element['#attached']['library'][] = 'ps_features/widget.inline';

    // Feature selection (compact).
    $features = $this->featureManager->getFeatures();
    $feature_options = ['' => $this->t('- Feature -')];
    foreach ($features as $feature) {
      $feature_options[$feature->id()] = $feature->label();
    }

    $element['feature_id'] = [
      '#type' => 'select',
      '#title' => $this->getSetting('compact') ? NULL : $this->t('Feature'),
      '#title_display' => 'invisible',
      '#options' => $feature_options,
      /** @phpstan-ignore-next-line */
      '#default_value' => $item->get('feature_id')->getValue() ?? '',
      '#required' => $element['#required'] ?? FALSE,
      '#attributes' => ['class' => ['feature-select']],
    ];

    /** @phpstan-ignore-next-line */
    $selected_feature_id = $item->get('feature_id')->getValue();

    if ($selected_feature_id) {
      $feature = $this->featureManager->getFeature($selected_feature_id);

      if ($feature) {
        $element['value_type'] = [
          '#type' => 'hidden',
          '#value' => $feature->getValueType(),
        ];

        $element += $this->buildInlineValueFields($feature, $item);
      }
    }

    return $element;
  }

  /**
   * Build inline value input fields based on feature type.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature definition.
   * @param mixed $item
   *   The field item.
   *
   * @return array<string, mixed>
   *   Form elements array.
   */
  private function buildInlineValueFields($feature, $item): array {
    $elements = [];
    $value_type = $feature->getValueType();
    $compact = $this->getSetting('compact');

    switch ($value_type) {
      case 'string':
        $elements['value_string'] = [
          '#type' => 'textfield',
          '#title' => $compact ? NULL : $this->t('Value'),
          '#title_display' => 'invisible',
          '#default_value' => $item->value_string ?? '',
          '#size' => 20,
          '#maxlength' => 255,
          '#attributes' => ['class' => ['feature-value']],
        ];
        break;

      case 'numeric':
        $elements['value_numeric'] = [
          '#type' => 'number',
          '#title' => $compact ? NULL : $this->t('Value'),
          '#title_display' => 'invisible',
          '#default_value' => $item->value_numeric ?? NULL,
          '#step' => 0.01,
          '#size' => 10,
          '#attributes' => ['class' => ['feature-value']],
        ];

        if ($unit = $feature->getUnit()) {
          $elements['value_numeric']['#field_suffix'] = $unit;
        }
        break;

      case 'range':
        $elements['value_range_min'] = [
          '#type' => 'number',
          '#title' => $compact ? NULL : $this->t('Min'),
          '#title_display' => 'invisible',
          '#default_value' => $item->value_range_min ?? NULL,
          '#step' => 0.01,
          '#size' => 8,
          '#attributes' => ['class' => ['feature-range-min'], 'placeholder' => $this->t('Min')],
        ];

        $elements['range_separator'] = [
          '#markup' => '<span class="range-separator">-</span>',
        ];

        $elements['value_range_max'] = [
          '#type' => 'number',
          '#title' => $compact ? NULL : $this->t('Max'),
          '#title_display' => 'invisible',
          '#default_value' => $item->value_range_max ?? NULL,
          '#step' => 0.01,
          '#size' => 8,
          '#attributes' => ['class' => ['feature-range-max'], 'placeholder' => $this->t('Max')],
        ];

        if ($unit = $feature->getUnit()) {
          $elements['value_range_max']['#field_suffix'] = $unit;
        }
        break;

      case 'dictionary':
        $dictionary_type = $feature->getDictionaryType();
        if ($dictionary_type) {
          $options = ['' => $this->t('-')];
          $options += $this->dictionaryManager->getOptions($dictionary_type);

          $elements['dictionary_type'] = [
            '#type' => 'hidden',
            '#value' => $dictionary_type,
          ];

          $elements['value_string'] = [
            '#type' => 'select',
            '#title' => $compact ? NULL : $this->t('Value'),
            '#title_display' => 'invisible',
            '#options' => $options,
            '#default_value' => $item->value_string ?? '',
            '#attributes' => ['class' => ['feature-value']],
          ];
        }
        break;
    }

    return $elements;
  }

}
