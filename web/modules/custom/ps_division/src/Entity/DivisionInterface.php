<?php

declare(strict_types=1);

namespace Drupal\ps_division\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Interface for Division content entity.
 *
 * Represents a spatial subdivision (lot, floor, apartment) with structural
 * classification and surfaces. Used for divisible real estate properties.
 *
 * @see \Drupal\ps_division\Entity\Division
 */
interface DivisionInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface, EntityPublishedInterface {

  /**
   * Gets the building name (entity label).
   *
   * @return string
   *   Building name.
   */
  public function getBuildingName(): string;

  /**
   * Sets the building name (entity label).
   *
   * @param string $name
   *   Building name.
   *
   * @return static
   */
  public function setBuildingName(string $name): static;

  /**
   * Gets the lot identifier.
   *
   * @return string|null
   *   Lot identifier or NULL.
   */
  public function getLot(): ?string;

  /**
   * Sets the lot identifier.
   *
   * @param string|null $lot
   *   Lot identifier or NULL.
   *
   * @return static
   */
  public function setLot(?string $lot): static;

  /**
   * Gets availability notes (translatable).
   *
   * @return string|null
   *   Availability text or NULL.
   */
  public function getAvailability(): ?string;

  /**
   * Sets availability notes.
   *
   * @param string|null $availability
   *   Availability text or NULL.
   *
   * @return static
   */
  public function setAvailability(?string $availability): static;

  /**
   * Computes total surface value (sum of all surfaces).
   *
   * Returns raw numeric sum without unit. Complexity: O(n) where n
   * is the number of surface items (typically small).
   *
   * @return float
   *   Total surface value.
   */
  public function getTotalSurface(): float;

}
