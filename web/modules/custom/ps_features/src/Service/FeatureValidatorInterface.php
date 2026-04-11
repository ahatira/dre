<?php

declare(strict_types=1);

namespace Drupal\ps_features\Service;

use Drupal\ps_features\Entity\FeatureInterface;

/**
 * Interface for the Feature Validator service.
 *
 * Validates feature values against their definitions, ensuring type safety
 * and rule compliance before storage.
 *
 * @see \Drupal\ps_features\Service\FeatureValidator
 * @see docs/specs/04-ps-features.md#validation
 */
interface FeatureValidatorInterface {

  /**
   * Validates a feature value.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   Feature definition.
   * @param array<string, mixed> $value
   *   Value to validate.
   *
   * @return array<string>
   *   Validation errors (empty if valid).
   */
  public function validate(FeatureInterface $feature, array $value): array;

  /**
   * Validates a numeric value.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   Feature definition.
   * @param array<string, mixed> $value
   *   Value to validate.
   *
   * @return array<string>
   *   Validation errors.
   */
  public function validateNumeric(FeatureInterface $feature, array $value): array;

  /**
   * Validates a range value.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   Feature definition.
   * @param array<string, mixed> $value
   *   Value to validate.
   *
   * @return array<string>
   *   Validation errors.
   */
  public function validateRange(FeatureInterface $feature, array $value): array;

  /**
   * Validates a dictionary value.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   Feature definition.
   * @param array<string, mixed> $value
   *   Value to validate.
   *
   * @return array<string>
   *   Validation errors.
   */
  public function validateDictionary(FeatureInterface $feature, array $value): array;

  /**
   * Validates a string value.
   *
   * @param \Drupal\ps_features\Entity\FeatureInterface $feature
   *   Feature definition.
   * @param array<string, mixed> $value
   *   Value to validate.
   *
   * @return array<string>
   *   Validation errors.
   */
  public function validateString(FeatureInterface $feature, array $value): array;

}
