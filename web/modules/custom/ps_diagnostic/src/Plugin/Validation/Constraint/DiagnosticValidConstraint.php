<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Validation\Constraint;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Validation\Attribute\Constraint;
use Symfony\Component\Validator\Constraint as SymfonyConstraint;

/**
 * Validates diagnostic field data coherence.
 *
 * Ensures that:
 * - If type is selected, at least Value or Class is provided (unless no_classification).
 * - If both Value and Class are provided, they must match the calculated class.
 */
#[Constraint(
  id: 'DiagnosticValid',
  label: new TranslatableMarkup('Diagnostic data validation'),
)]
class DiagnosticValidConstraint extends SymfonyConstraint {

  /**
   * Error message when type is selected but no value/class provided.
   *
   * @var string
   */
  public string $missingData = 'When a diagnostic is selected, you must provide either a numeric value or a class label, or check "No classification".';

  /**
   * Error message when class doesn\'t match calculated value.
   *
   * @var string
   */
  public string $incoherentClass = 'The class "%provided" does not match the calculated class "%calculated" for the numeric value %value.';

}
