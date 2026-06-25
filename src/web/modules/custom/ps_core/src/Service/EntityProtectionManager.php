<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
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
    $field = $this->resolveLockField($entity);
    if ($field === NULL) {
      return FALSE;
    }

    return (bool) $entity->get($field)->value;
  }

  /**
   * {@inheritdoc}
   */
  public function protect(EntityInterface $entity): void {
    $field = $this->resolveLockField($entity);
    if ($field === NULL) {
      $this->logger->warning(
        'Entity @type:@id does not have an internal lock field.',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
        ]
      );
      return;
    }

    $entity->set($field, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function unprotect(EntityInterface $entity): void {
    $field = $this->resolveLockField($entity);
    if ($field === NULL) {
      return;
    }

    $entity->set($field, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function hasConflict(EntityInterface $entity, array $externalData): bool {
    $field = $this->resolveChecksumField($entity);
    if ($field === NULL) {
      return FALSE;
    }

    $internalChecksum = (string) $entity->get($field)->value;
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
    $field = $this->resolveTrackingField($entity);
    if ($field === NULL) {
      $this->logger->warning(
        'Entity @type:@id does not have a source tracking field.',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
        ]
      );
      return;
    }

    if (!isset($metadata['tracked_at'])) {
      $metadata['tracked_at'] = \Drupal::time()->getRequestTime();
    }

    $entity->set($field, json_encode($metadata, JSON_THROW_ON_ERROR));
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
      $array[$key] = $this->normalizeChecksumValue($value);
    }
    return $array;
  }

  /**
   * Normalizes a value so it can be safely serialized for checksum hashing.
   */
  private function normalizeChecksumValue(mixed $value): mixed {
    if ($value instanceof \SimpleXMLElement) {
      return (string) $value;
    }
    if (is_array($value)) {
      return $this->sortArrayRecursive($value);
    }
    if (is_object($value)) {
      return method_exists($value, '__toString') ? (string) $value : $value::class;
    }
    return $value;
  }

  /**
   * Resolves the internal lock field name for an entity.
   */
  private function resolveLockField(EntityInterface $entity): ?string {
    if (!$entity instanceof FieldableEntityInterface) {
      return NULL;
    }
    foreach (['internal_lock', 'field_internal_lock'] as $field) {
      if ($entity->hasField($field)) {
        return $field;
      }
    }
    return NULL;
  }

  /**
   * Resolves the checksum field name for an entity.
   */
  private function resolveChecksumField(EntityInterface $entity): ?string {
    if (!$entity instanceof FieldableEntityInterface) {
      return NULL;
    }
    foreach (['checksum', 'field_source_checksum'] as $field) {
      if ($entity->hasField($field)) {
        return $field;
      }
    }
    return NULL;
  }

  /**
   * Resolves the source tracking field name for an entity.
   */
  private function resolveTrackingField(EntityInterface $entity): ?string {
    if (!$entity instanceof FieldableEntityInterface) {
      return NULL;
    }
    foreach (['source_tracking', 'field_source_tracking'] as $field) {
      if ($entity->hasField($field)) {
        return $field;
      }
    }
    return NULL;
  }

}
