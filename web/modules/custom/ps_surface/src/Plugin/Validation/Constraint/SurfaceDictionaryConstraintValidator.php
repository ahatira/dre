<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Plugin\Validation\Constraint;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem;
use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Ensures ps_surface items reference valid dictionary codes.
 *
 * Dictionary type mappings are hardcoded
 * since they are locked in ps_dictionary:
 * - surface_unit: Units of measurement
 *   (m², ft², hectares, etc.)
 * - surface_type: Surface types
 *   (apartment, office, retail, etc.)
 * - surface_nature: Surface natures
 *   (interior, exterior, habitable, etc.)
 * - surface_qualification: Qualifications
 *   (available, leased, reserved, etc.)
 */
final class SurfaceDictionaryConstraintValidator extends ConstraintValidator implements
  ContainerInjectionInterface {

  private const DICTIONARY_UNIT = 'surface_unit';
  private const DICTIONARY_TYPE = 'surface_type';
  private const DICTIONARY_NATURE = 'surface_nature';
  private const DICTIONARY_QUALIFICATION = 'surface_qualification';

  /**
   * Constructs the validator.
   */
  public function __construct(
    private readonly DictionaryManagerInterface $dictionaryManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('ps_dictionary.manager'),
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {
    if (!$constraint instanceof SurfaceDictionaryConstraint) {
      return;
    }

    if (!$value instanceof SurfaceItem) {
      return;
    }

    $config = $this->configFactory->get('ps_surface.settings');

    $allowNegative = (bool) ($config->get('validation.allow_negative') ?? FALSE);
    $requireType = (bool) ($config->get('validation.require_type') ?? FALSE);
    $requireNature = (bool) ($config->get('validation.require_nature') ?? FALSE);
    $requireQualification = (bool) ($config->get('validation.require_qualification') ?? FALSE);

    $surfaceValue = $value->getValue();
    if ($surfaceValue !== NULL && $surfaceValue < 0 && !$allowNegative) {
      $this->context
        ->buildViolation($constraint->negativeValueMessage)
        ->atPath('value')
        ->addViolation();
    }

    $unit = $value->getUnit();
    if ($unit !== NULL && !$this->dictionaryManager->isValid(self::DICTIONARY_UNIT, $unit)) {
      $this->context
        ->buildViolation($constraint->invalidUnitMessage)
        ->atPath('unit')
        ->setParameter('%unit', (string) $unit)
        ->addViolation();
    }

    $type = $value->getType();
    if ($type !== NULL && !$this->dictionaryManager->isValid(self::DICTIONARY_TYPE, $type)) {
      $this->context
        ->buildViolation($constraint->invalidTypeMessage)
        ->atPath('type')
        ->setParameter('%type', (string) $type)
        ->addViolation();
    }

    // Required by settings: type.
    if ($surfaceValue !== NULL && $requireType && $type === NULL) {
      $this->context
        ->buildViolation($constraint->missingTypeMessage)
        ->atPath('type')
        ->addViolation();
    }

    $nature = $value->getNature();
    if ($nature !== NULL && !$this->dictionaryManager->isValid(self::DICTIONARY_NATURE, $nature)) {
      $this->context
        ->buildViolation($constraint->invalidNatureMessage)
        ->atPath('nature')
        ->setParameter('%nature', (string) $nature)
        ->addViolation();
    }

    // Required by settings: nature.
    if ($surfaceValue !== NULL && $requireNature && $nature === NULL) {
      $this->context
        ->buildViolation($constraint->missingNatureMessage)
        ->atPath('nature')
        ->addViolation();
    }

    $qualification = $value->getQualification();
    if ($qualification !== NULL && !$this->dictionaryManager->isValid(self::DICTIONARY_QUALIFICATION, $qualification)) {
      $this->context
        ->buildViolation($constraint->invalidQualificationMessage)
        ->atPath('qualification')
        ->setParameter('%qualification', (string) $qualification)
        ->addViolation();
    }

    // Required by settings: qualification.
    if ($surfaceValue !== NULL && $requireQualification && $qualification === NULL) {
      $this->context
        ->buildViolation($constraint->missingQualificationMessage)
        ->atPath('qualification')
        ->addViolation();
    }
  }

}
