<?php

declare(strict_types=1);

namespace Drupal\ps_division\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Division Type config entity.
 */
interface DivisionTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the division type description.
   *
   * @return string
   *   The description.
   */
  public function getDescription(): string;

  /**
   * Sets the division type description.
   *
   * @param string $description
   *   The description.
   *
   * @return static
   */
  public function setDescription(string $description): static;

}
