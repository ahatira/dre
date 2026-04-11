<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Ensures unit is present when a value is provided.
 */
final class SurfaceCompletenessConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    // No services required for this validator.
    unset($container);
    return new self();
  }

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof SurfaceCompletenessConstraint) {
      return;
    }

    if (!$value instanceof SurfaceItem) {
      return;
    }

    $hasValue = $value->getValue() !== NULL;
    $hasUnit = $value->getUnit() !== NULL;

    // If a value is provided, enforce unit presence.
    if ($hasValue && !$hasUnit) {
      $this->context
        ->buildViolation($constraint->missingUnitMessage)
        ->atPath('unit')
        ->addViolation();
    }
  }

}
