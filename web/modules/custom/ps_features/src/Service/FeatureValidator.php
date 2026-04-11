<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_features\Entity\FeatureInterface;

/**
 * Feature Validator service.
 *
 * Validates feature values according to their type and rules.
 *
 * @see \Drupal\ps_features\Service\FeatureValidatorInterface
 * @see docs/specs/04-ps-features.md#validation
 */
final class FeatureValidator implements FeatureValidatorInterface {

  use StringTranslationTrait;

  /**
   * Constructs a FeatureValidator.
   *
   * @param \Drupal\ps_dictionary\Service\DictionaryManagerInterface $dictionaryManager
   *   The dictionary manager service.
   * @param \Drupal\Core\StringTranslation\TranslationInterface|null $stringTranslation
   *   The string translation service.
   */
  public function __construct(
    private readonly DictionaryManagerInterface $dictionaryManager,
    ?TranslationInterface $stringTranslation = NULL,
  ) {
    if ($stringTranslation) {
      $this->setStringTranslation($stringTranslation);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validate(FeatureInterface $feature, array $value): array {
    $valueType = $feature->getValueType();

    return match ($valueType) {
      'flag' => $this->validateFlag($feature, $value),
      'yesno' => $this->validateYesNo($feature, $value),
      'numeric' => $this->validateNumeric($feature, $value),
      'range' => $this->validateRange($feature, $value),
      'dictionary' => $this->validateDictionary($feature, $value),
      'string' => $this->validateString($feature, $value),
      default => [(string) $this->t('Unknown value type: @type', ['@type' => $valueType])],
    };
  }

  /**
   * Validates a flag feature value.
   *
   * Flag features have no stored value - their flag presence in the field
   * indicates TRUE. This method always returns empty array (valid).
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   * @param array<string, mixed> $value
   *   The field value (unused for flag type).
   *
   * @return array
   *   Empty array (flag is always valid if the field item exists).
   */
  public function validateFlag(FeatureInterface $feature, array $value): array {
    // Flag features are valid by definition if the field item exists.
    // No value needs to be stored or validated.
    return [];
  }

  /**
   * Validates a yes/no feature value.
   *
   * Yes/No features always display their value (Yes or No).
   * They require value_boolean to be set.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   The feature entity.
   * @param array<string, mixed> $value
   *   The field value array.
   *
   * @return array
   *   Array of error messages (empty if valid).
   */
  public function validateYesNo(FeatureInterface $feature, array $value): array {
    if (!isset($value['value_boolean'])) {
      if ($feature->isRequired()) {
        return [(string) $this->t('Yes/No value is required.')];
      }
      return [];
    }

    if (!is_bool($value['value_boolean'])) {
      return [(string) $this->t('Value must be a boolean.')];
    }

    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function validateNumeric(FeatureInterface $feature, array $value): array {
    $errors = [];

    if (!isset($value['value_numeric'])) {
      if ($feature->isRequired()) {
        $errors[] = (string) $this->t('Numeric value is required.');
      }
      return $errors;
    }

    if (!is_numeric($value['value_numeric'])) {
      $errors[] = (string) $this->t('Value must be numeric.');
      return $errors;
    }

    $numericValue = (float) $value['value_numeric'];
    $rules = $feature->getValidationRules();

    if (isset($rules['min']) && $numericValue < $rules['min']) {
      $errors[] = (string) $this->t('Value must be at least @min.', ['@min' => $rules['min']]);
    }

    if (isset($rules['max']) && $numericValue > $rules['max']) {
      $errors[] = (string) $this->t('Value must be at most @max.', ['@max' => $rules['max']]);
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function validateRange(FeatureInterface $feature, array $value): array {
    $errors = [];

    $hasMin = isset($value['value_range_min']);
    $hasMax = isset($value['value_range_max']);

    if (!$hasMin && !$hasMax) {
      if ($feature->isRequired()) {
        $errors[] = (string) $this->t('Range values are required.');
      }
      return $errors;
    }

    if ($hasMin && !is_numeric($value['value_range_min'])) {
      $errors[] = (string) $this->t('Minimum value must be numeric.');
    }

    if ($hasMax && !is_numeric($value['value_range_max'])) {
      $errors[] = (string) $this->t('Maximum value must be numeric.');
    }

    if ($hasMin && $hasMax) {
      $min = (float) $value['value_range_min'];
      $max = (float) $value['value_range_max'];

      if ($min > $max) {
        $errors[] = (string) $this->t('Minimum value cannot be greater than maximum value.');
      }

      $rules = $feature->getValidationRules();

      if (isset($rules['min']) && $min < $rules['min']) {
        $errors[] = (string) $this->t('Minimum value must be at least @min.', ['@min' => $rules['min']]);
      }

      if (isset($rules['max']) && $max > $rules['max']) {
        $errors[] = (string) $this->t('Maximum value must be at most @max.', ['@max' => $rules['max']]);
      }
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function validateDictionary(FeatureInterface $feature, array $value): array {
    $errors = [];
    $dictionaryType = $feature->getDictionaryType();

    if (!$dictionaryType) {
      $errors[] = (string) $this->t('Dictionary type not configured for this feature.');
      return $errors;
    }

    if (!isset($value['value_string']) || empty($value['value_string'])) {
      if ($feature->isRequired()) {
        $errors[] = (string) $this->t('Dictionary value is required.');
      }
      return $errors;
    }

    $code = (string) $value['value_string'];

    if (!$this->dictionaryManager->isValid($dictionaryType, $code)) {
      $errors[] = (string) $this->t('Invalid dictionary code: @code', ['@code' => $code]);
    }

    return $errors;
  }

  /**
   * {@inheritdoc}
   */
  public function validateString(FeatureInterface $feature, array $value): array {
    $errors = [];

    if (!isset($value['value_string']) || empty($value['value_string'])) {
      if ($feature->isRequired()) {
        $errors[] = (string) $this->t('String value is required.');
      }
      return $errors;
    }

    if (!is_string($value['value_string'])) {
      $errors[] = (string) $this->t('Value must be a string.');
      return $errors;
    }

    $rules = $feature->getValidationRules();

    if (isset($rules['allowed_values']) && is_array($rules['allowed_values'])) {
      if (!in_array($value['value_string'], $rules['allowed_values'], TRUE)) {
        $errors[] = (string) $this->t('Value must be one of: @values', [
          '@values' => implode(', ', $rules['allowed_values']),
        ]);
      }
    }

    return $errors;
  }

}
