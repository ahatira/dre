<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Service;

use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;

/**
 * Interface for Dictionary Manager service.
 *
 * Provides centralized management of business dictionaries including:
 * - Code validation with caching (O(1) performance)
 * - Label retrieval with translation support
 * - Options generation for form elements
 * - Cache management and invalidation.
 */
interface DictionaryManagerInterface {

  /**
   * Validates if a code is active in a dictionary type.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   The code to validate.
   *
   * @return bool
   *   TRUE if code exists and is active.
   */
  public function isValid(string $type, string $code): bool;

  /**
   * Gets the human-readable label for a code.
   *
   * Only returns label if entry is active. Deprecated entries return NULL.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   The code.
   * @param string|null $langcode
   *   Optional language code (for future multi-language support).
   *
   * @return string|null
   *   The label or NULL if not found/inactive.
   */
  public function getLabel(string $type, string $code, ?string $langcode = NULL): ?string;

  /**
   * Gets options array for form elements.
   *
   * Suitable for use in select fields, checkboxes, radios.
   * Returns: ['CODE' => 'Label', ...]
   *
   * @param string $type
   *   Dictionary type ID.
   * @param bool $activeOnly
   *   If TRUE, only active entries included (default: TRUE).
   *
   * @return array<string, string>
   *   Options keyed by code, sorted by weight then label.
   */
  public function getOptions(string $type, bool $activeOnly = TRUE): array;

  /**
   * Gets a single dictionary entry.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   The code.
   *
   * @return \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|null
   *   The entry or NULL if not found.
   */
  public function getEntry(string $type, string $code): ?DictionaryEntryInterface;

  /**
   * Gets all entries for a dictionary type.
   *
   * Sorted by weight then label.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param bool $activeOnly
   *   If TRUE, only active entries included (default: TRUE).
   *
   * @return array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface>
   *   Entries keyed by ID.
   */
  public function getEntries(string $type, bool $activeOnly = TRUE): array;

  /**
   * Checks if a code is deprecated.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   The code.
   *
   * @return bool
   *   TRUE if deprecated.
   */
  public function isDeprecated(string $type, string $code): bool;

  /**
   * Gets custom metadata for a code.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   The code.
   *
   * @return array<string, mixed>
   *   The metadata array.
   */
  public function getMetadata(string $type, string $code): array;

  /**
   * Gets a specific metadata value for a code.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   The code.
   * @param string $key
   *   The metadata key.
   * @param mixed $default
   *   The default value if key not found.
   *
   * @return mixed
   *   The metadata value or default.
   */
  public function getMetadataValue(string $type, string $code, string $key, mixed $default = NULL): mixed;

  /**
   * Clears dictionary cache.
   *
   * @param string|null $type
   *   Dictionary type ID or NULL to clear all caches.
   */
  public function clearCache(?string $type = NULL): void;

  /**
   * Gets all available dictionary types as an associative array.
   *
   * Suitable for form selects and dropdowns (field configuration, etc).
   * Returns: ['property_type' => 'Property type', ...]
   *
   * Cached per normal cache lifecycle.
   *
   * @return array<string, string>
   *   Dictionary types keyed by ID with labels as values.
   */
  public function getAvailableTypes(): array;

}
