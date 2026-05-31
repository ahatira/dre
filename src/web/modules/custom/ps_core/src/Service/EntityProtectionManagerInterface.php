<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for entity protection manager service.
 *
 * Manages the internal/external data protection system to prevent
 * automated imports from overwriting manually curated data.
 */
interface EntityProtectionManagerInterface {

  /**
   * Checks if an entity is protected against external overwrites.
   *
   * An entity is protected if its internal_lock field is TRUE,
   * indicating manual curation that should not be overwritten
   * by automated imports.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   TRUE if the entity is protected, FALSE otherwise.
   */
  public function isProtected(EntityInterface $entity): bool;

  /**
   * Marks an entity as protected (manual override).
   *
   * Sets the internal_lock field to TRUE to prevent future
   * automated overwrites.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to protect.
   *
   * @return void
   */
  public function protect(EntityInterface $entity): void;

  /**
   * Removes protection from an entity.
   *
   * Sets the internal_lock field to FALSE to allow future
   * automated overwrites.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to unprotect.
   *
   * @return void
   */
  public function unprotect(EntityInterface $entity): void;

  /**
   * Detects conflicts between internal and external data via checksums.
   *
   * Compares the entity's stored checksum with the external data checksum
   * to determine if the data has diverged.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   * @param array $externalData
   *   External data array containing 'checksum' key.
   *
   * @return bool
   *   TRUE if a conflict is detected, FALSE otherwise.
   */
  public function hasConflict(EntityInterface $entity, array $externalData): bool;

  /**
   * Applies a merge strategy when updating entity from external data.
   *
   * Strategies:
   * - EXTERNAL_WINS: External data always overwrites (e.g., prices, surfaces)
   * - INTERNAL_WINS: Keep internal value if modified (e.g., SEO, descriptions)
   * - MERGE_APPEND: Merge arrays (e.g., media galleries, documents)
   * - SKIP: Do not update if entity is protected
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to update.
   * @param array $externalData
   *   External data array with field values.
   * @param string $fieldName
   *   The field name to update.
   * @param string $strategy
   *   Merge strategy: EXTERNAL_WINS|INTERNAL_WINS|MERGE_APPEND|SKIP.
   *
   * @return bool
   *   TRUE if the field was updated, FALSE otherwise.
   */
  public function applyMergeStrategy(
    EntityInterface $entity,
    array $externalData,
    string $fieldName,
    string $strategy
  ): bool;

  /**
   * Records source tracking metadata for an entity.
   *
   * Stores structured JSON payload with source system identifier,
   * timestamp, and other traceability information.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to track.
   * @param array $metadata
   *   Metadata array (source_system, source_id, timestamp, etc.).
   *
   * @return void
   */
  public function trackSource(EntityInterface $entity, array $metadata): void;

  /**
   * Computes a checksum for entity data.
   *
   * Generates a SHA256 hash of the entity's field values for
   * idempotence checking during imports.
   *
   * @param array $data
   *   Data array to hash.
   *
   * @return string
   *   SHA256 checksum.
   */
  public function computeChecksum(array $data): string;

}
