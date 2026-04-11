<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validates ps_surface completeness.
 *
 * Requires unit when a value is provided.
 */
final class SurfaceCompletenessConstraint extends Constraint {

  /**
   * Message when value is set but unit is missing.
   */
  public string $missingUnitMessage = 'Unit is required when a value is provided.';

}
