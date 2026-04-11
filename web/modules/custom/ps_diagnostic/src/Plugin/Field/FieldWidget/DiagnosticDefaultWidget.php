<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_diagnostic\Service\DiagnosticClassCalculatorInterface;
use Drupal\ps_diagnostic\Service\DiagnosticNormalizerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default widget for ps_diagnostic field.
 *
 * Provides an organized form for entering diagnostic data with dictionary-backed
 * dropdowns for type, validity dates, and status flags. Matches ps_surface/ps_price
 * pattern with primary and optional fields containers and integrated styling.
 */
#[FieldWidget(
  id: 'ps_diagnostic_default',
  label: new TranslatableMarkup('Diagnostic (default)'),
  field_types: ['ps_diagnostic'],
)]
final class DiagnosticDefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs the widget.
   *
   * @param string $plugin_id
   *   The plugin ID.
   * @param array<string, mixed> $plugin_definition
   *   The plugin definition array.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition.
   * @param array<string, mixed> $settings
   *   The settings.
   * @param array<string, mixed> $third_party_settings
   *   Third-party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\ps_diagnostic\Service\DiagnosticClassCalculatorInterface $classCalculator
   *   The class calculator service.
   * @param \Drupal\ps_diagnostic\Service\DiagnosticNormalizerInterface $normalizer
   *   The normalizer service.
   */
  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly DiagnosticClassCalculatorInterface $classCalculator,
    private readonly DiagnosticNormalizerInterface $normalizer,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
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
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('ps_diagnostic.class_calculator'),
      $container->get('ps_diagnostic.normalizer'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    unset($form, $form_state);
    $item = $items[$delta];
    assert($item instanceof FieldItemInterface);

    // Main wrapper for diagnostic fields with styling.
    $element['#type'] = 'container';
    $element['#attributes']['class'][] = 'ps-diagnostic-widget';
    $element['#attached']['library'][] = 'ps_diagnostic/widget';

    // Generate unique ID for type_id field for <label for=""> association.
    $field_name = $this->fieldDefinition->getName();
    $type_id_id = 'edit-' . str_replace('_', '-', $field_name) . '-' . $delta . '-type-id';
    $is_required = $this->fieldDefinition->isRequired();

    // Add field label on first item (delta 0) for single-value fields.
    if ($delta === 0 && $items->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() == 1) {
      $element['_label'] = [
        '#type' => 'html_tag',
        '#tag' => 'label',
        '#value' => $this->fieldDefinition->getLabel(),
        '#attributes' => [
          'for' => $type_id_id,
          'class' => ['form-item__label'],
        ],
        '#weight' => -100,
      ];
      if ($is_required) {
        $element['_label']['#attributes']['class'][] = 'js-form-required';
        $element['_label']['#attributes']['class'][] = 'form-required';
      }
    }

    // PRIMARY FIELDS CONTAINER (Type ID, Numeric value, Label code).
    $element['_primary'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-diagnostic-primary-fields']],
      '#weight' => 10,
    ];

    $element['_primary']['type_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => $this->getDiagnosticTypeOptions(),
      '#default_value' => $item->get('type_id')->getValue(),
      '#required' => $is_required,
      '#required_error' => $is_required ? $this->t('Select a diagnostic.') : NULL,
      '#id' => $type_id_id,
      '#description' => $this->t('Reference to diagnostic (dpe, ges, asbestos, lead, termites, electrical).'),
      '#ajax' => [
        'callback' => [$this, 'ajaxCalculateClass'],
        'wrapper' => 'diagnostic-class-wrapper-' . $delta,
        'event' => 'change',
      ],
    ];

    $element['_primary']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Value'),
      '#default_value' => $item->get('value')->getValue(),
      '#step' => 0.01,
      '#min' => 0,
      '#placeholder' => 'e.g., 150.5',
      '#required' => FALSE,
      '#description' => $this->t('Numeric value for automatic class calculation.'),
      '#ajax' => [
        'callback' => [$this, 'ajaxCalculateClass'],
        'wrapper' => 'diagnostic-class-wrapper-' . $delta,
        'event' => 'change',
      ],
    ];

    $element['_primary']['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#default_value' => $item->get('class')->getValue(),
      '#size' => 10,
      '#maxlength' => 10,
      '#placeholder' => 'A, B, C, etc.',
      '#required' => FALSE,
      '#description' => $this->t('Energy class label (A-G). Auto-calculated from numeric value or manually set.'),
      '#prefix' => '<div id=\"diagnostic-class-wrapper-' . $delta . '\">',
      '#suffix' => '</div>',
    ];

    // VALIDITY FIELDS CONTAINER (Valid from, Valid to).
    $element['_validity'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-diagnostic-validity-fields']],
      '#weight' => 20,
    ];

    $element['_validity']['valid_from'] = [
      '#type' => 'date',
      '#title' => $this->t('Valid from'),
      '#default_value' => $item->get('valid_from')->getValue(),
      '#required' => FALSE,
      '#description' => $this->t('Diagnostic date (ISO 8601: YYYY-MM-DD).'),
    ];

    $element['_validity']['valid_to'] = [
      '#type' => 'date',
      '#title' => $this->t('Valid to'),
      '#default_value' => $item->get('valid_to')->getValue(),
      '#required' => FALSE,
      '#description' => $this->t('Validity end date (auto-calculated from diagnostic date + type duration).'),
    ];

    // STATUS FLAGS CONTAINER (No classification, Non applicable).
    $element['_flags'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-diagnostic-flags']],
      '#weight' => 30,
    ];

    $element['_flags']['no_classification'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('No classification'),
      '#default_value' => (bool) ($item->get('no_classification')->getValue() ?? FALSE),
      '#description' => $this->t('Check if no class can be determined (displays "?").'),
    ];

    $element['_flags']['non_applicable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Non applicable'),
      '#default_value' => (bool) ($item->get('non_applicable')->getValue() ?? FALSE),
      '#description' => $this->t('Check if diagnostic is not applicable (displays "N/A").'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * @param array<int, mixed> $values
   *   The submitted values.
   *
   * @return array<int, mixed>
   *   The massaged values.
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    $massaged = [];

    foreach ($values as $delta => $value) {
      // Flatten nested structure from containers back to flat field properties.
      $item = [];

      // Extract from _primary container.
      if (isset($value['_primary']['type_id'])) {
        $item['type_id'] = $value['_primary']['type_id'];
      }
      if (isset($value['_primary']['value'])) {
        $item['value'] = $value['_primary']['value'];
      }
      if (isset($value['_primary']['class'])) {
        $item['class'] = $value['_primary']['class'];
      }

      // Extract from _validity container.
      if (isset($value['_validity']['valid_from'])) {
        $item['valid_from'] = $value['_validity']['valid_from'];
      }
      if (isset($value['_validity']['valid_to'])) {
        $item['valid_to'] = $value['_validity']['valid_to'];
      }

      // Extract from _flags container.
      if (isset($value['_flags']['no_classification'])) {
        $item['no_classification'] = (bool) $value['_flags']['no_classification'];
      }
      if (isset($value['_flags']['non_applicable'])) {
        $item['non_applicable'] = (bool) $value['_flags']['non_applicable'];
      }

      // Normalize and auto-calculate class if applicable.
      $massaged[$delta] = $this->normalizer->normalize($item);
    }

    return $massaged;
  }

  /**
   * AJAX callback to auto-calculate diagnostic class from value.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array
   *   The class field element to replace.
   */
  public function ajaxCalculateClass(array &$form, FormStateInterface $form_state): array {
    // Get triggering element to find delta and field name.
    $trigger = $form_state->getTriggeringElement();
    $parents = $trigger['#array_parents'];

    // Navigate to the field element (remove last 2 levels: _primary and field name).
    // Remove field name (type_id or value).
    array_pop($parents);
    // Remove _primary.
    array_pop($parents);

    // Get the field element.
    $field_element = $form;
    foreach ($parents as $parent) {
      $field_element = $field_element[$parent];
    }

    // Extract values.
    $values = $form_state->getValues();
    $field_name = $this->fieldDefinition->getName();

    // Find delta from parents.
    $delta = NULL;
    foreach ($parents as $parent) {
      if (is_numeric($parent)) {
        $delta = (int) $parent;
        break;
      }
    }

    if ($delta !== NULL && isset($values[$field_name][$delta])) {
      $item_values = $values[$field_name][$delta];

      // Get type_id and value.
      $type_id = $item_values['_primary']['type_id'] ?? NULL;
      $value = $item_values['_primary']['value'] ?? NULL;

      // Auto-calculate class if type and value present.
      if (!empty($type_id) && is_numeric($value) && (float) $value >= 0) {
        $calculated_class = $this->classCalculator->calculateClass($type_id, (float) $value);

        if ($calculated_class !== NULL) {
          // Update the form element default value.
          $field_element['_primary']['class']['#value'] = $calculated_class;
        }
      }
    }

    // Return the class field wrapper.
    return $field_element['_primary']['class'];
  }

  /**
   * Gets diagnostic options from entity storage.
   *
   * @return array<string, string>
   *   Options keyed by ID with label as value, including empty option.
   */
  private function getDiagnosticTypeOptions(): array {
    $options = ['' => $this->t('- Select -')];

    try {
      $storage = $this->entityTypeManager->getStorage('diagnostic');
      $types = $storage->loadMultiple();

      foreach ($types as $type) {
        $options[$type->id()] = $type->label();
      }
    }
    catch (\Exception) {
      // Silently fall back to empty options if storage fails.
    }

    return $options;
  }

}
