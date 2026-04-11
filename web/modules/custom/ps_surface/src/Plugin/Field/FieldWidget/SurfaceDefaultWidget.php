<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Attribute\FieldWidget;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Default widget for ps_surface field.
 */
#[FieldWidget(
  id: 'ps_surface_default',
  label: new TranslatableMarkup('Surface widget'),
  field_types: ['ps_surface'],
)]
final class SurfaceDefaultWidget extends WidgetBase implements ContainerFactoryPluginInterface {

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
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   */
  public function __construct(
    string $plugin_id,
    array $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    private readonly DictionaryManagerInterface $dictionaryManager,
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
      $container->get('ps_dictionary.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    unset($form, $form_state);
    $item = $items[$delta];
    assert($item instanceof FieldItemInterface);

    // Main wrapper for surface fields with styling.
    $element['#type'] = 'container';
    $element['#attributes']['class'][] = 'ps-surface-widget';
    $element['#attached']['library'][] = 'ps_surface/widget';

    // Generate unique ID for value field for <label for=""> association.
    $field_name = $this->fieldDefinition->getName();
    $value_id = 'edit-' . str_replace('_', '-', $field_name) . '-' . $delta . '-value';
    $is_required = $this->fieldDefinition->isRequired();

    // Add field label on first item (delta 0) for single-value fields.
    if ($delta === 0 && $items->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() == 1) {
      $element['_label'] = [
        '#type' => 'html_tag',
        '#tag' => 'label',
        '#value' => $this->fieldDefinition->getLabel(),
        '#attributes' => [
          'for' => $value_id,
          'class' => ['form-item__label'],
        ],
        '#weight' => -100,
      ];
      if ($is_required) {
        $element['_label']['#attributes']['class'][] = 'js-form-required';
        $element['_label']['#attributes']['class'][] = 'form-required';
      }
    }

    // PRIMARY FIELDS CONTAINER (Value, Unit, Type).
    $element['_primary'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-surface-primary-fields']],
      '#weight' => 10,
    ];

    $element['_primary']['value'] = [
      '#type' => 'number',
      '#title' => $this->t('Value'),
      '#default_value' => $item->get('value')->getValue(),
      '#step' => 0.01,
      '#min' => 0,
      '#required' => $is_required,
      '#required_error' => $is_required ? $this->t('Enter a surface value.') : NULL,
      '#size' => 10,
      '#id' => $value_id,
    ];

    $element['_primary']['unit'] = [
      '#type' => 'select',
      '#title' => $this->t('Unit'),
      '#options' => ['' => $this->t('- Select -')] + $this->dictionaryManager->getOptions('surface_unit'),
      '#default_value' => $item->get('unit')->getValue() ?? $this->getFieldSetting('default_unit'),
      '#required' => $is_required,
      '#required_error' => $is_required ? $this->t('Select a unit for this surface.') : NULL,
    ];

    $element['_primary']['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Type'),
      '#options' => ['' => $this->t('- None -')] + $this->dictionaryManager->getOptions('surface_type'),
      '#default_value' => $item->get('type')->getValue(),
      '#required' => FALSE,
    ];

    // OPTIONAL FIELDS CONTAINER (Nature, Qualification).
    $element['_optional'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-surface-optional-fields']],
      '#weight' => 20,
    ];

    $element['_optional']['nature'] = [
      '#type' => 'select',
      '#title' => $this->t('Nature'),
      '#options' => ['' => $this->t('- None -')] + $this->dictionaryManager->getOptions('surface_nature'),
      '#default_value' => $item->get('nature')->getValue(),
      '#required' => FALSE,
    ];

    $element['_optional']['qualification'] = [
      '#type' => 'select',
      '#title' => $this->t('Qualification'),
      '#options' => ['' => $this->t('- None -')] + $this->dictionaryManager->getOptions('surface_qualification'),
      '#default_value' => $item->get('qualification')->getValue() ?? '',
      '#required' => FALSE,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   *
   * @param array<int, array<string, mixed>> $values
   *   Form values to massage.
   * @param array<string, mixed> $form
   *   The form structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return array<int, array<string, mixed>>
   *   Flattened form values.
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    unset($form, $form_state);
    $flattened = [];

    foreach ($values as $delta => $value) {
      // Flatten nested structure: extract fields from _primary and _optional.
      // Convert empty strings to NULL to match SurfaceItem field getter behavior.
      $flattened[$delta] = [
        'value' => $value['_primary']['value'] ?? NULL,
        'unit' => $this->cleanValue($value['_primary']['unit'] ?? NULL),
        'type' => $this->cleanValue($value['_primary']['type'] ?? NULL),
        'nature' => $this->cleanValue($value['_optional']['nature'] ?? NULL),
        'qualification' => $this->cleanValue($value['_optional']['qualification'] ?? NULL),
      ];
    }

    return $flattened;
  }

  /**
   * Converts empty strings to NULL.
   *
   * @param mixed $value
   *   The form value.
   *
   * @return string|null
   *   NULL if empty string, otherwise the value as-is.
   */
  private function cleanValue(mixed $value): ?string {
    if ($value === '' || $value === NULL) {
      return NULL;
    }
    return (string) $value;
  }

}
