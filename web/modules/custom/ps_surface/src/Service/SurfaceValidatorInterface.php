<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Service;

use Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem;

/**
 * Validates surface items against business rules and dictionary codes.
 */
interface SurfaceValidatorInterface {

  /**
   * Validates a surface item instance.
   *
   * @param \Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem $item
   *   Item to validate.
   *
   * @return array<int, string>
   *   List of validation error messages (empty when valid).
   */
  public function validateItem(SurfaceItem $item): array;

  /**
   * Validates a raw surface row (e.g., form values).
   *
   * @param array<string, mixed> $values
   *   Raw values with keys: value, unit, type, nature, qualification.
   *
   * @return array<int, string>
   *   List of validation error messages (empty when valid).
   */
  public function validateRow(array $values): array;

}
