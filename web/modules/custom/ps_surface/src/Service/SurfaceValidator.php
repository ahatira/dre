<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Service;

use Drupal\ps_dictionary\Service\DictionaryManagerInterface;
use Drupal\ps_surface\Plugin\Field\FieldType\SurfaceItem;

/**
 * Default surface validator using ps_dictionary lookups.
 */
final class SurfaceValidator implements SurfaceValidatorInterface {

  /**
   * Constructs the validator.
   */
  public function __construct(private readonly DictionaryManagerInterface $dictionaryManager) {
  }

  /**
   * {@inheritdoc}
   */
  public function validateItem(SurfaceItem $item): array {
    return $this->validateRow([
      'value' => $item->getValue(),
      'unit' => $item->getUnit(),
      'type' => $item->getType(),
      'nature' => $item->getNature(),
      'qualification' => $item->getQualification(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateRow(array $values): array {
    $errors = [];

    $value = $values['value'] ?? NULL;
    if ($value !== NULL && !is_numeric($value)) {
      $errors[] = 'Surface value must be numeric.';
    }
    elseif ($value !== NULL && (float) $value < 0) {
      $errors[] = 'Surface value cannot be negative.';
    }

    $unit = $values['unit'] ?? NULL;
    if ($unit !== NULL && $unit !== '' && !$this->dictionaryManager->isValid('surface_unit', (string) $unit)) {
      $errors[] = sprintf("Invalid unit code '%s' (surface_unit dictionary).", (string) $unit);
    }

    $type = $values['type'] ?? NULL;
    if ($type !== NULL && $type !== '' && !$this->dictionaryManager->isValid('surface_type', (string) $type)) {
      $errors[] = sprintf("Invalid type code '%s' (surface_type dictionary).", (string) $type);
    }

    $nature = $values['nature'] ?? NULL;
    if ($nature !== NULL && $nature !== '' && !$this->dictionaryManager->isValid('surface_nature', (string) $nature)) {
      $errors[] = sprintf("Invalid nature code '%s' (surface_nature dictionary).", (string) $nature);
    }

    $qualification = $values['qualification'] ?? NULL;
    if ($qualification !== NULL && $qualification !== '' && !$this->dictionaryManager->isValid('surface_qualification', (string) $qualification)) {
      $errors[] = sprintf("Invalid qualification code '%s' (surface_qualification dictionary).", (string) $qualification);
    }

    return $errors;
  }

}
