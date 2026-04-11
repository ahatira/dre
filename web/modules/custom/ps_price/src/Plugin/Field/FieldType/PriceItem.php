<?php

declare(strict_types=1);

namespace Drupal\ps_price\Plugin\Field\FieldType;

use Drupal\Core\Field\Attribute\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\ps_price\Plugin\Validation\Constraint\PriceDictionaryConstraint;

/**
 * Defines the 'ps_price' field type.
 *
 * Stores structured price data with amount, currency, unit, period and flags.
 * Supports business flags (on_request, from, VAT excluded, charges included).
 *
 * XML mapping from BUDGETS_LIST/BUDGET:
 * - AMOUNT → amount
 * - CURRENCY_CODE → currency_code (currency dictionary)
 * - VALUE_TYPE_CODE → value_type_code (price_value_type dictionary: MIN, MAX)
 * - UNIT_CODE → unit_code (price_unit dictionary: SUR, GLO, OTH)
 * - PERIOD_CODE → period_code (price_period dictionary: ANN, MEN, TRI, SEM)
 * - FROM flag → is_from
 * - VAT_EXCLUDED flag → is_vat_excluded (HT = Hors Taxes)
 * - CHARGES_INCLUDED flag → is_charges_included (CC = Charges Comprises)
 *
 * @see specs/mockup/xml/OBL_ES_20251108111254.xml
 */
#[FieldType(
  id: 'ps_price',
  label: new TranslatableMarkup('Price'),
  description: new TranslatableMarkup('Stores structured price with currency, unit, period, and business flags.'),
  category: 'propertysearch',
  default_widget: 'ps_price_default',
  default_formatter: 'ps_price_full',
)]
class PriceItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    return [
      'default_currency' => 'EUR',
      'default_period' => 'ANN',
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    return [] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {
    unset($field_definition);
    $properties = [];

    $properties['amount'] = DataDefinition::create('float')
      ->setLabel(new TranslatableMarkup('Amount'))
      ->setDescription(new TranslatableMarkup('Main price amount'));

    $properties['currency_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Currency code'))
      ->setDescription(new TranslatableMarkup('ISO currency code (e.g., EUR, USD)'));

    $properties['value_type_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Value type code'))
      ->setDescription(new TranslatableMarkup('Value type code from dictionary (MIN, MAX)'));

    $properties['unit_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Unit code'))
      ->setDescription(new TranslatableMarkup('Price unit code from dictionary (e.g., /m²/an)'));

    $properties['period_code'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Period code'))
      ->setDescription(new TranslatableMarkup('Period code from dictionary (e.g., year, month)'));

    $properties['is_from'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Starting price'))
      ->setDescription(new TranslatableMarkup('Display "Starting from" prefix'));

    $properties['is_vat_excluded'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('VAT excluded'))
      ->setDescription(new TranslatableMarkup('Price excludes VAT (HT)'));

    $properties['is_charges_included'] = DataDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Charges included'))
      ->setDescription(new TranslatableMarkup('Price includes charges (CC)'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {
    unset($field_definition);
    return [
      'columns' => [
        'amount' => [
          'type' => 'numeric',
          'precision' => 15,
          'scale' => 2,
        ],
        'currency_code' => [
          'type' => 'varchar',
          'length' => 3,
        ],
        'value_type_code' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'unit_code' => [
          'type' => 'varchar',
          'length' => 64,
        ],
        'period_code' => [
          'type' => 'varchar',
          'length' => 32,
        ],
        'is_from' => [
          'type' => 'int',
          'size' => 'tiny',
          'default' => 0,
        ],
        'is_vat_excluded' => [
          'type' => 'int',
          'size' => 'tiny',
          'default' => 0,
        ],
        'is_charges_included' => [
          'type' => 'int',
          'size' => 'tiny',
          'default' => 0,
        ],
      ],
      'indexes' => [
        'amount' => ['amount'],
        'currency_code' => ['currency_code'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $amount = $this->get('amount')->getValue();
    return $amount === NULL || $amount === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function mainPropertyName(): ?string {
    return 'amount';
  }

  /**
   * Gets the amount value.
   */
  public function getAmount(): ?float {
    $value = $this->get('amount')->getValue();
    return $value !== NULL ? (float) $value : NULL;
  }

  /**
   * Gets the currency code.
   */
  public function getCurrencyCode(): ?string {
    return $this->get('currency_code')->getValue();
  }

  /**
   * Gets the unit code.
   */
  public function getUnitCode(): ?string {
    return $this->get('unit_code')->getValue();
  }

  /**
   * Gets the period code.
   */
  public function getPeriodCode(): ?string {
    return $this->get('period_code')->getValue();
  }

  /**
   * Gets the value type code.
   */
  public function getValueTypeCode(): ?string {
    return $this->get('value_type_code')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();
    $constraints[] = new PriceDictionaryConstraint();
    return $constraints;
  }

}
