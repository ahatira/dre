<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Dictionary Type configuration entity.
 *
 * Defines a dictionary type category representing a collection of
 * business codes (e.g., property_type, transaction_type, offer_status).
 */
interface DictionaryTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the human-readable label.
   *
   * @return string
   *   The label.
   */
  public function getLabel(): string;

  /**
   * Gets the optional description.
   *
   * @return string|null
   *   The description or NULL.
   */
  public function getDescription(): ?string;

  /**
   * Sets the description.
   *
   * @param string|null $description
   *   The description.
   *
   * @return $this
   */
  public function setDescription(?string $description): static;

  /**
   * Gets the metadata schema definition (YAML).
   *
   * @return string|null
   *   The YAML schema string or NULL.
   */
  public function getMetadataSchema(): ?string;

  /**
   * Sets the metadata schema definition.
   *
   * @param string|null $schema
   *   The YAML schema string.
   *
   * @return $this
   */
  public function setMetadataSchema(?string $schema): static;

  /**
   * Checks if dictionary type is locked.
   *
   * @return bool
   *   TRUE if locked.
   */
  public function isLocked(): bool;

  /**
   * Sets locked flag.
   *
   * @param bool $locked
   *   TRUE if locked.
   *
   * @return $this
   */
  public function setLocked(bool $locked): static;

}
