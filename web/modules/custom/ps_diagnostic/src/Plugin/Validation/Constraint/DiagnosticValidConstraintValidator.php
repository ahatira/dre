<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the DiagnosticValid constraint.
 */
class DiagnosticValidConstraintValidator extends ConstraintValidator implements ContainerInjectionInterface {

  /**
   * Constructs the validator.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    assert($constraint instanceof DiagnosticValidConstraint);

    if (!isset($value)) {
      return;
    }

    /** @var \Drupal\ps_diagnostic\Plugin\Field\FieldType\DiagnosticItem $item */
    $item = $value;
    $type_id = $item->get('type_id')->getValue();
    $numeric_value = $item->get('value')->getValue();
    $class = trim((string) $item->get('class')->getValue());
    $no_classification = (bool) $item->get('no_classification')->getValue();

    // Skip validation if no type selected (empty field).
    if (empty($type_id)) {
      return;
    }

    // Treat empty string as NULL for numeric value.
    if ($numeric_value === '') {
      $numeric_value = NULL;
    }

    // Rule 1: If type is selected, at least Value or Class must be provided,
    // unless "No classification" is checked.
    if (!$no_classification && $numeric_value === NULL && $class === '') {
      $this->context->buildViolation($constraint->missingData)
        ->atPath('value')
        ->addViolation();
      return;
    }

    // Rule 2: If both Value and Class are provided, they must be coherent.
    if ($numeric_value !== NULL && $class !== '') {
      try {
        $storage = $this->entityTypeManager->getStorage('diagnostic');
        /** @var \Drupal\ps_diagnostic\Entity\PsDiagnosticInterface|null $type */
        $type = $storage->load($type_id);

        if ($type !== NULL) {
          $calculated_class = $type->calculateClass((float) $numeric_value);

          // If calculated class differs from provided class, add violation.
          if ($calculated_class !== NULL && strtoupper($calculated_class) !== strtoupper($class)) {
            $this->context->buildViolation($constraint->incoherentClass)
              ->setParameters([
                '%provided' => $class,
                '%calculated' => $calculated_class,
                '%value' => $numeric_value,
              ])
              ->atPath('class')
              ->addViolation();
          }
        }
      }
      catch (\Exception) {
        // Silently skip validation if type cannot be loaded.
      }
    }
  }

}
