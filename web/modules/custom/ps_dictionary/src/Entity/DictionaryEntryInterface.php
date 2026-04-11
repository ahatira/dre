<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Interface for Dictionary Entry configuration entity.
 *
 * Defines a single code-label pair within a dictionary type, supporting
 * weighting, status flags, and custom metadata.
 */
interface DictionaryEntryInterface extends ConfigEntityInterface {

  /**
   * Gets the dictionary type ID.
   *
   * @return string
   *   The type ID.
   */
  public function getType(): string;

  /**
   * Gets the business code.
   *
   * @return string
   *   The code.
   */
  public function getCode(): string;

  /**
   * Sets the code.
   *
   * @param string $code
   *   The code.
   *
   * @return $this
   */
  public function setCode(string $code): static;

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
   * Gets the weight for sorting.
   *
   * @return int
   *   The weight.
   */
  public function getWeight(): int;

  /**
   * Sets the weight.
   *
   * @param int $weight
   *   The weight.
   *
   * @return $this
   */
  public function setWeight(int $weight): static;

  /**
   * Checks if entry is active.
   *
   * @return bool
   *   TRUE if active.
   */
  public function isActive(): bool;

  /**
   * Checks if entry is deprecated.
   *
   * @return bool
   *   TRUE if deprecated.
   */
  public function isDeprecated(): bool;

  /**
   * Gets custom metadata.
   *
   * @return array<string, mixed>
   *   The metadata array.
   */
  public function getMetadata(): array;

  /**
   * Sets metadata.
   *
   * @param array<string, mixed> $metadata
   *   The metadata array.
   *
   * @return $this
   */
  public function setMetadata(array $metadata): static;

  /**
   * Gets a metadata value by key.
   *
   * @param string $key
   *   The metadata key.
   * @param mixed $default
   *   The default value if key not found.
   *
   * @return mixed
   *   The metadata value or default.
   */
  public function getMetadataValue(string $key, mixed $default = NULL): mixed;

  /**
   * Sets a metadata value.
   *
   * @param string $key
   *   The metadata key.
   * @param mixed $value
   *   The value.
   *
   * @return $this
   */
  public function setMetadataValue(string $key, mixed $value): static;

  /**
   * Checks if metadata key exists.
   *
   * @param string $key
   *   The metadata key.
   *
   * @return bool
   *   TRUE if key exists.
   */
  public function hasMetadataKey(string $key): bool;

  /**
   * Gets metadata value with type coercion.
   *
   * @param string $key
   *   The metadata key.
   * @param string $type
   *   The target type: string, int, float, bool, array.
   * @param mixed $default
   *   The default value if key not found.
   *
   * @return mixed
   *   The typed value or default.
   */
  public function getMetadataTyped(string $key, string $type = 'string', mixed $default = NULL): mixed;

}
