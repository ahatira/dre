<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Validates ps_surface field items against dictionary codes and value rules.
 */
final class SurfaceDictionaryConstraint extends Constraint {

  /**
   * Message for invalid unit code.
   */
  public string $invalidUnitMessage = "Invalid surface unit code '%unit' (expected ps_dictionary: surface_unit).";

  /**
   * Message for invalid type code.
   */
  public string $invalidTypeMessage = "Invalid surface type code '%type' (expected ps_dictionary: surface_type).";

  /**
   * Message for invalid nature code.
   */
  public string $invalidNatureMessage = "Invalid surface nature code '%nature' (expected ps_dictionary: surface_nature).";

  /**
   * Message for invalid qualification code.
   */
  public string $invalidQualificationMessage = "Invalid surface qualification code '%qualification' (expected ps_dictionary: surface_qualification).";

  /**
   * Message for negative values.
   */
  public string $negativeValueMessage = 'Surface value cannot be negative.';

  /**
   * Message when a required type is missing (via settings).
   */
  public string $missingTypeMessage = 'Type is required.';

  /**
   * Message when a required nature is missing (via settings).
   */
  public string $missingNatureMessage = 'Nature is required.';

  /**
   * Message when a required qualification is missing (via settings).
   */
  public string $missingQualificationMessage = 'Qualification is required.';

}
