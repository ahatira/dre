<?php

declare(strict_types=1);

namespace Drupal\ps_division\Service;

use Drupal\ps_division\Entity\DivisionInterface;

/**
 * Interface for Division Manager service.
 *
 * Provides business logic for division entities including validation
 * and summary generation.
 */
interface DivisionManagerInterface {

  /**
   * Validates division entity against business rules.
   *
   * Checks surface values, dictionary codes, and business constraints.
   *
   * @param \Drupal\ps_division\Entity\DivisionInterface $division
   *   The division entity to validate.
   *
   * @return array<int, string>
   *   Array of validation error messages. Empty if valid.
   */
  public function validate(DivisionInterface $division): array;

  /**
   * Generates a summary array for a division.
   *
   * @param \Drupal\ps_division\Entity\DivisionInterface $division
   *   The division entity.
   *
   * @return array<string, mixed>
   *   Summary data array with id, building_name, type, nature, lot, total_surface.
   */
  public function getSummary(DivisionInterface $division): array;

}
