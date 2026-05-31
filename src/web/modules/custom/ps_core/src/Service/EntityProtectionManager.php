<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Entity\EntityInterface;
use Psr\Log\LoggerInterface;

/**
 * Service for managing entity protection against external overwrites.
 */
final class EntityProtectionManager implements EntityProtectionManagerInterface {

  /**
   * Constructs an EntityProtectionManager.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger service.
   */
  public function __construct(
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function isProtected(EntityInterface $entity): bool {
    if (!$entity->hasField('internal_lock')) {
      return FALSE;
    }

    return (bool) $entity->get('internal_lock')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function protect(EntityInterface $entity): void {
    if (!$entity->hasField('internal_lock')) {
      $this->logger->warning(
        'Entity @type:@id does not have internal_lock field.',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
        ]
      );
      return;
    }

    $entity->set('internal_lock', TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function unprotect(EntityInterface $entity): void {
    if (!$entity->hasField('internal_lock')) {
      return;
    }

    $entity->set('internal_lock', FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function hasConflict(EntityInterface $entity, array $externalData): bool {
    if (!$entity->hasField('checksum')) {
      return FALSE;
    }

    $internalChecksum = (string) $entity->get('checksum')->value;
    $externalChecksum = (string) ($externalData['checksum'] ?? '');

    if ($internalChecksum === '' || $externalChecksum === '') {
      return FALSE;
    }

    $conflict = $internalChecksum !== $externalChecksum;

    if ($conflict) {
      $this->logger->info(
        'Conflict detected for @type:@id - Internal: @internal, External: @external',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
          '@internal' => $internalChecksum,
          '@external' => $externalChecksum,
        ]
      );
    }

    return $conflict;
  }

  /**
   * {@inheritdoc}
   */
  public function applyMergeStrategy(
    EntityInterface $entity,
    array $externalData,
    string $fieldName,
    string $strategy
  ): bool {
    // If entity is protected, skip update regardless of strategy.
    if ($this->isProtected($entity)) {
      $this->logger->debug(
        'Entity @type:@id is protected - skipping field @field update',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
          '@field' => $fieldName,
        ]
      );
      return FALSE;
    }

    // Ensure field exists on entity.
    if (!$entity->hasField($fieldName)) {
      $this->logger->warning(
        'Entity @type:@id does not have field @field',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
          '@field' => $fieldName,
        ]
      );
      return FALSE;
    }

    // Get external value.
    $externalValue = $externalData[$fieldName] ?? NULL;
    if ($externalValue === NULL) {
      return FALSE;
    }

    // Apply strategy.
    switch ($strategy) {
      case 'EXTERNAL_WINS':
        // External data always overwrites.
        $entity->set($fieldName, $externalValue);
        return TRUE;

      case 'INTERNAL_WINS':
        // Keep internal value if entity already exists and has value.
        if ($entity->isNew()) {
          $entity->set($fieldName, $externalValue);
          return TRUE;
        }

        $internalValue = $entity->get($fieldName)->value;
        if ($internalValue === NULL || $internalValue === '') {
          $entity->set($fieldName, $externalValue);
          return TRUE;
        }

        // Internal value exists, keep it.
        return FALSE;

      case 'MERGE_APPEND':
        // Merge arrays (deduplicate).
        $internalValue = $entity->get($fieldName)->getValue();
        $mergedValue = array_unique(
          array_merge((array) $internalValue, (array) $externalValue),
          SORT_REGULAR
        );
        $entity->set($fieldName, $mergedValue);
        return TRUE;

      case 'SKIP':
        // Explicitly skip update.
        return FALSE;

      default:
        $this->logger->error(
          'Unknown merge strategy @strategy for field @field',
          ['@strategy' => $strategy, '@field' => $fieldName]
        );
        return FALSE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function trackSource(EntityInterface $entity, array $metadata): void {
    if (!$entity->hasField('source_tracking')) {
      $this->logger->warning(
        'Entity @type:@id does not have source_tracking field.',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
        ]
      );
      return;
    }

    // Add timestamp if not provided.
    if (!isset($metadata['tracked_at'])) {
      $metadata['tracked_at'] = \Drupal::time()->getRequestTime();
    }

    $entity->set('source_tracking', json_encode($metadata, JSON_THROW_ON_ERROR));
  }

  /**
   * {@inheritdoc}
   */
  public function computeChecksum(array $data): string {
    // Sort array recursively by keys for consistent hashing.
    $data = $this->sortArrayRecursive($data);

    // Serialize and hash.
    return hash('sha256', serialize($data));
  }

  /**
   * Recursively sorts an array by keys.
   *
   * @param array $array
   *   The array to sort.
   *
   * @return array
   *   The sorted array.
   */
  private function sortArrayRecursive(array $array): array {
    ksort($array);
    foreach ($array as $key => $value) {
      if (is_array($value)) {
        $array[$key] = $this->sortArrayRecursive($value);
      }
    }
    return $array;
  }

}
