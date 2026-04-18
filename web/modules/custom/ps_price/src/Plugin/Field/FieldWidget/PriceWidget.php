<?php

declare(strict_types=1);

namespace Drupal\ps_price\Plugin\Field\FieldWidget;

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
 * Default price field widget.
 *
 * Provides an organized form for entering price data with dictionary-backed
 * dropdowns for currency, unit, period, and value type. Matches ps_surface
 * pattern with primary and optional fields containers and integrated styling.
 */
#[FieldWidget(
  id: 'ps_price_default',
  label: new TranslatableMarkup('Default'),
  field_types: ['ps_price'],
  weight: 0,
)]
final class PriceWidget extends WidgetBase implements ContainerFactoryPluginInterface {

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
  public static function defaultSettings(): array {
    return [
      'show_unit' => TRUE,
      'show_period' => TRUE,
      'show_value_type' => TRUE,
      'show_flags' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $elements = parent::settingsForm($form, $form_state);

    $elements['show_unit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show unit field'),
      '#default_value' => $this->getSetting('show_unit'),
      '#description' => $this->t('Allow users to specify price unit (per m², global, etc.).'),
    ];

    $elements['show_period'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show period field'),
      '#default_value' => $this->getSetting('show_period'),
      '#description' => $this->t('Allow users to specify price period (annual, monthly, etc.).'),
    ];

    $elements['show_value_type'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show value type field'),
      '#default_value' => $this->getSetting('show_value_type'),
      '#description' => $this->t('Allow users to specify if price is minimum, maximum, etc.'),
    ];

    $elements['show_flags'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show business flags'),
      '#default_value' => $this->getSetting('show_flags'),
      '#description' => $this->t('Display checkboxes for "Starting Price", "HT", and "CC" flags.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary = [];

    $visible_fields = [];
    if ($this->getSetting('show_unit')) {
      $visible_fields[] = $this->t('Unit');
    }
    if ($this->getSetting('show_period')) {
      $visible_fields[] = $this->t('Period');
    }
    if ($this->getSetting('show_value_type')) {
      $visible_fields[] = $this->t('Type');
    }
    if ($this->getSetting('show_flags')) {
      $visible_fields[] = $this->t('Flags');
    }

    if (!empty($visible_fields)) {
      $summary[] = $this->t('Visible: @fields', [
        '@fields' => implode(', ', $visible_fields),
      ]);
    }
    else {
      $summary[] = $this->t('Only amount and currency visible');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state,
  ): array {
    unset($form, $form_state);

    $item = $items[$delta];
    assert($item instanceof FieldItemInterface);

    // Main wrapper for price fields with styling.
    $element['#type'] = 'container';
    $element['#attributes']['class'][] = 'ps-price-widget';
    $element['#attached']['library'][] = 'ps_price/widget';

    // Generate unique ID for amount field for <label for=""> association.
    $field_name = $this->fieldDefinition->getName();
    $amount_id = 'edit-' . str_replace('_', '-', $field_name) . '-' . $delta . '-amount';
    $on_request_id = 'edit-' . str_replace('_', '-', $field_name) . '-' . $delta . '-is-on-request';
    $is_required = $this->fieldDefinition->isRequired();

    // Add field label on first item (delta 0) for single-value fields.
    if ($delta === 0 && $items->getFieldDefinition()->getFieldStorageDefinition()->getCardinality() == 1) {
      $element['_label'] = [
        '#type' => 'html_tag',
        '#tag' => 'label',
        '#value' => $this->fieldDefinition->getLabel(),
        '#attributes' => [
          'for' => $amount_id,
          'class' => ['form-item__label'],
        ],
        '#weight' => -100,
      ];
      if ($is_required) {
        $element['_label']['#attributes']['class'][] = 'js-form-required';
        $element['_label']['#attributes']['class'][] = 'form-required';
      }
    }

    // PRIMARY FIELDS CONTAINER (Amount, Currency, Unit).
    $element['_primary'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-price-primary-fields']],
      '#weight' => 10,
    ];

    $element['_primary']['amount'] = [
      '#type' => 'number',
      '#title' => $this->t('Amount'),
      '#default_value' => $item->get('amount')->getValue(),
      '#step' => 0.01,
      '#min' => 0,
      '#required' => FALSE,
      '#required_error' => $is_required ? $this->t('Enter a price amount.') : NULL,
      '#size' => 10,
      '#id' => $amount_id,
      '#wrapper_attributes' => ['class' => ['ps-price-amount']],
      '#states' => [
        'disabled' => [
          ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $element['_primary']['currency_code'] = [
      '#type' => 'select',
      '#title' => $this->t('Currency'),
      '#options' => ['' => $this->t('- Select -')] + $this->dictionaryManager->getOptions('currency'),
      '#default_value' => $item->get('currency_code')->getValue(),
      '#required' => FALSE,
      '#required_error' => $is_required ? $this->t('Select a currency for this price.') : NULL,
      '#wrapper_attributes' => ['class' => ['ps-price-currency']],
      '#states' => [
        'disabled' => [
          ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $element['_primary']['is_on_request'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('On request'),
      '#default_value' => (bool) ($item->get('is_on_request')->getValue() ?? FALSE),
      '#id' => $on_request_id,
      '#wrapper_attributes' => ['class' => ['ps-price-on-request-inline']],
    ];

    // OPTIONAL FIELDS CONTAINER (Period, Value type, Unit).
    $element['_optional'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-price-optional-fields']],
      '#weight' => 20,
    ];

    if ($this->getSetting('show_unit')) {
      $element['_optional']['unit_code'] = [
        '#type' => 'select',
        '#title' => $this->t('Unit'),
        '#options' => ['' => $this->t('- None -')] + $this->dictionaryManager->getOptions('price_unit'),
        '#default_value' => $item->get('unit_code')->getValue(),
        '#required' => FALSE,
        '#wrapper_attributes' => ['class' => ['ps-price-unit']],
        '#description' => $this->t('e.g., per square meter, globally'),
        '#states' => [
          'disabled' => [
            ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    if ($this->getSetting('show_period')) {
      $element['_optional']['period_code'] = [
        '#type' => 'select',
        '#title' => $this->t('Period'),
        '#options' => ['' => $this->t('- None -')] + $this->dictionaryManager->getOptions('price_period'),
        '#default_value' => $item->get('period_code')->getValue(),
        '#required' => FALSE,
        '#wrapper_attributes' => ['class' => ['ps-price-period']],
        '#states' => [
          'disabled' => [
            ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    if ($this->getSetting('show_value_type')) {
      $element['_optional']['value_type_code'] = [
        '#type' => 'select',
        '#title' => $this->t('Type'),
        '#options' => ['' => $this->t('- None -')] + $this->dictionaryManager->getOptions('price_value_type'),
        '#default_value' => $item->get('value_type_code')->getValue(),
        '#required' => FALSE,
        '#wrapper_attributes' => ['class' => ['ps-price-value-type']],
        '#states' => [
          'disabled' => [
            ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    // FLAGS CONTAINER (Business flags).
    if ($this->getSetting('show_flags')) {
      $element['_flags'] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-price-flags']],
        '#weight' => 30,
      ];

      $element['_flags']['is_from'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Starting Price'),
        '#default_value' => (bool) ($item->get('is_from')->getValue() ?? FALSE),
        '#wrapper_attributes' => ['class' => ['ps-price-flag', 'ps-price-flag--from']],
        '#states' => [
          'disabled' => [
            ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
          ],
        ],
      ];

      $element['_flags']['is_vat_excluded'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('HT'),
        '#default_value' => (bool) ($item->get('is_vat_excluded')->getValue() ?? FALSE),
        '#wrapper_attributes' => ['class' => ['ps-price-flag', 'ps-price-flag--vat']],
        '#states' => [
          'disabled' => [
            ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
          ],
        ],
      ];

      $element['_flags']['is_charges_included'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('CC'),
        '#default_value' => (bool) ($item->get('is_charges_included')->getValue() ?? FALSE),
        '#wrapper_attributes' => ['class' => ['ps-price-flag', 'ps-price-flag--charges']],
        '#states' => [
          'disabled' => [
            ':input[id="' . $on_request_id . '"]' => ['checked' => TRUE],
          ],
        ],
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    unset($form, $form_state);

    $result = [];

    foreach ($values as $value) {
      $amount = $value['amount'] ?? ($value['_primary']['amount'] ?? NULL);
      $currency = $value['currency_code'] ?? ($value['_primary']['currency_code'] ?? NULL);
      $unit = $value['unit_code'] ?? ($value['_optional']['unit_code'] ?? NULL);
      $period = $value['period_code'] ?? ($value['_optional']['period_code'] ?? NULL);
      $valueType = $value['value_type_code'] ?? ($value['_optional']['value_type_code'] ?? NULL);
      $isFrom = $value['is_from'] ?? ($value['_flags']['is_from'] ?? 0);
      $isVatExcluded = $value['is_vat_excluded'] ?? ($value['_flags']['is_vat_excluded'] ?? 0);
      $isChargesIncluded = $value['is_charges_included'] ?? ($value['_flags']['is_charges_included'] ?? 0);

      $onRequest = !empty($value['is_on_request']) || !empty($value['_primary']['is_on_request']);
      $hasAmount = $amount !== NULL && $amount !== '';
      $hasOptionalData = ($currency !== NULL && $currency !== '')
        || ($unit !== NULL && $unit !== '')
        || ($period !== NULL && $period !== '')
        || ($valueType !== NULL && $valueType !== '')
        || !empty($isFrom)
        || !empty($isVatExcluded)
        || !empty($isChargesIncluded);

      // Skip completely empty rows (no amount, no on_request and no optional data).
      // This prevents Drupal's automatic empty row from creating validation errors.
      if (!$hasAmount && !$onRequest && !$hasOptionalData) {
        continue;
      }

      $cleanValue = [];

      if ($onRequest) {
        $cleanValue['amount'] = NULL;
        $cleanValue['currency_code'] = NULL;
        $cleanValue['is_on_request'] = 1;
      }
      else {
        $cleanValue['amount'] = $hasAmount ? $amount : NULL;
        $cleanValue['currency_code'] = $currency;
        $cleanValue['is_on_request'] = 0;
      }

      $cleanValue['unit_code'] = $unit && $unit !== '' ? $unit : NULL;
      $cleanValue['period_code'] = $period && $period !== '' ? $period : NULL;
      $cleanValue['value_type_code'] = $valueType && $valueType !== '' ? $valueType : NULL;
      $cleanValue['is_from'] = (int) !empty($isFrom);
      $cleanValue['is_vat_excluded'] = (int) !empty($isVatExcluded);
      $cleanValue['is_charges_included'] = (int) !empty($isChargesIncluded);

      unset($cleanValue['_primary'], $cleanValue['_optional'], $cleanValue['_flags']);

      $result[] = $cleanValue;
    }

    return $result;
  }

}
