<?php

declare(strict_types=1);

namespace Drupal\ps_price\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validates ps_price field items against dictionary codes and value rules.
 */
final class PriceDictionaryConstraint extends Constraint {

  /**
   * Message for invalid currency code.
   */
  public string $invalidCurrencyMessage = "Invalid currency code '%currency' (expected ps_dictionary: currency).";

  /**
   * Message for invalid unit code.
   */
  public string $invalidUnitMessage = "Invalid price unit code '%unit' (expected ps_dictionary: price_unit).";

  /**
   * Message for invalid period code.
   */
  public string $invalidPeriodMessage = "Invalid price period code '%period' (expected ps_dictionary: price_period).";

  /**
   * Message for invalid value type code.
   */
  public string $invalidValueTypeMessage = "Invalid price value type code '%value_type' (expected ps_dictionary: price_value_type).";

  /**
   * Message for negative amounts.
   */
  public string $negativeAmountMessage = 'Price amount cannot be negative.';

  /**
   * Message when currency is missing (required field).
   */
  public string $missingCurrencyMessage = 'Currency is required.';

}
