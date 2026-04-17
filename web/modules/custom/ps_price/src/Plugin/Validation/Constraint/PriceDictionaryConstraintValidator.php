<?php

declare(strict_types=1);

namespace Drupal\ps_price\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\ps_price\Plugin\Field\FieldType\PriceItem;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Ensures ps_price items reference valid dictionary codes and valid amounts.
 */
final class PriceDictionaryConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Constructs the validator.
   */
  public function __construct(
    private readonly DictionaryManagerInterface $dictionaryManager,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_dictionary.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof PriceDictionaryConstraint) {
      return;
    }

    if (!$value instanceof PriceItem) {
      return;
    }

    $amount = $value->getAmount();
    $onRequest = $value->isOnRequest();
    $hasAmount = $amount !== NULL;

    // Ignore entirely empty rows.
    $hasAnyOptionalData = $value->getUnitCode() !== NULL
      || $value->getPeriodCode() !== NULL
      || $value->getValueTypeCode() !== NULL
      || $value->getCurrencyCode() !== NULL
      || (bool) ($value->get('is_from')->getValue() ?? FALSE)
      || (bool) ($value->get('is_vat_excluded')->getValue() ?? FALSE)
      || (bool) ($value->get('is_charges_included')->getValue() ?? FALSE)
      || $onRequest;
    if (!$hasAmount && !$hasAnyOptionalData) {
      return;
    }

    // Business rule: when marked "on request", skip all other validations.
    if ($onRequest) {
      return;
    }

    // Explicit status rule (XOR): either amount or on-request.
    if ($onRequest && $hasAmount) {
      $this->context
        ->buildViolation($constraint->onRequestWithAmountMessage)
        ->atPath('amount')
        ->addViolation();
    }

    if (!$onRequest && !$hasAmount) {
      $this->context
        ->buildViolation($constraint->missingAmountOrOnRequestMessage)
        ->atPath('amount')
        ->addViolation();
      return;
    }

    if ($hasAmount && $amount < 0) {
      $this->context
        ->buildViolation($constraint->negativeAmountMessage)
        ->atPath('amount')
        ->addViolation();
    }

    // Currency validation is required only when amount is provided.
    $currency = $value->getCurrencyCode();
    if ($hasAmount) {
      if ($currency === NULL || $currency === '') {
        $this->context
          ->buildViolation($constraint->missingCurrencyMessage)
          ->atPath('currency_code')
          ->addViolation();
      }
      elseif (!$this->dictionaryManager->isValid('currency', $currency)) {
        $this->context
          ->buildViolation($constraint->invalidCurrencyMessage)
          ->atPath('currency_code')
          ->setParameter('%currency', (string) $currency)
          ->addViolation();
      }
    }

    // Unit code validation (optional).
    $unit = $value->getUnitCode();
    if ($unit !== NULL && !$this->dictionaryManager->isValid('price_unit', $unit)) {
      $this->context
        ->buildViolation($constraint->invalidUnitMessage)
        ->atPath('unit_code')
        ->setParameter('%unit', (string) $unit)
        ->addViolation();
    }

    // Period code validation (optional).
    $period = $value->getPeriodCode();
    if ($period !== NULL && !$this->dictionaryManager->isValid('price_period', $period)) {
      $this->context
        ->buildViolation($constraint->invalidPeriodMessage)
        ->atPath('period_code')
        ->setParameter('%period', (string) $period)
        ->addViolation();
    }

    // Value type code validation (optional).
    $valueType = $value->getValueTypeCode();
    if ($valueType !== NULL && !$this->dictionaryManager->isValid('price_value_type', $valueType)) {
      $this->context
        ->buildViolation($constraint->invalidValueTypeMessage)
        ->atPath('value_type_code')
        ->setParameter('%value_type', (string) $valueType)
        ->addViolation();
    }
  }

}
