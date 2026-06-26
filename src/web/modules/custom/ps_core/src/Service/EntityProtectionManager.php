<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionRegistry;
use Psr\Log\LoggerInterface;

/**
 * Service for managing entity protection against external overwrites.
 */
final class EntityProtectionManager implements EntityProtectionManagerInterface {

  /**
   * Constructs an EntityProtectionManager.
   */
  public function __construct(
    private readonly LoggerInterface $logger,
    private readonly ConfigEntityProtectionRegistry $configEntityProtectionRegistry,
    private readonly TimeInterface $time,
    private readonly ConflictWindowProviderInterface $conflictWindowProvider,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function supports(EntityInterface $entity): bool {
    return $this->resolveLockProperty($entity) !== NULL
      || $this->resolveTrackingProperty($entity) !== NULL
      || $this->resolveChecksumProperty($entity) !== NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function isProtected(EntityInterface $entity): bool {
    $lockProperty = $this->resolveLockProperty($entity);
    if ($lockProperty === NULL) {
      return FALSE;
    }

    return $this->readBooleanProperty($entity, $lockProperty);
  }

  /**
   * {@inheritdoc}
   */
  public function isCatalogueProtected(EntityInterface $entity): bool {
    if (method_exists($entity, 'isCatalogueProtected')) {
      return $entity->isCatalogueProtected();
    }

    return $this->isProtected($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function protect(EntityInterface $entity): void {
    $lockProperty = $this->resolveLockProperty($entity);
    if ($lockProperty === NULL) {
      $this->logger->warning(
        'Entity @type:@id does not have an internal lock field.',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
        ]
      );
      return;
    }

    $this->writeProperty($entity, $lockProperty, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function unprotect(EntityInterface $entity): void {
    $lockProperty = $this->resolveLockProperty($entity);
    if ($lockProperty === NULL) {
      return;
    }

    $this->writeProperty($entity, $lockProperty, FALSE);
  }

  /**
   * {@inheritdoc}
   */
  public function isFieldLocked(EntityInterface $entity, string $fieldName): bool {
    if ($fieldName === '') {
      return FALSE;
    }

    $definition = $this->configEntityProtectionRegistry->getDefinitionForEntity($entity);
    if ($definition === NULL) {
      return FALSE;
    }

    $fieldLocks = $this->normalizeFieldLocks($entity->get($definition->getFieldLocksProperty()));
    return !empty($fieldLocks[$fieldName]);
  }

  /**
   * {@inheritdoc}
   */
  public function setFieldLocked(EntityInterface $entity, string $fieldName, bool $locked = TRUE): void {
    if ($fieldName === '') {
      return;
    }

    $definition = $this->configEntityProtectionRegistry->getDefinitionForEntity($entity);
    if ($definition === NULL) {
      $this->logger->warning(
        'Entity @type:@id is not registered for config entity protection.',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
        ]
      );
      return;
    }

    $property = $definition->getFieldLocksProperty();
    $fieldLocks = $this->normalizeFieldLocks($entity->get($property));
    if ($locked) {
      $fieldLocks[$fieldName] = TRUE;
    }
    else {
      unset($fieldLocks[$fieldName]);
    }

    $entity->set($property, $fieldLocks);
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldLocks(EntityInterface $entity): array {
    $definition = $this->configEntityProtectionRegistry->getDefinitionForEntity($entity);
    if ($definition === NULL) {
      return [];
    }

    return $this->normalizeFieldLocks($entity->get($definition->getFieldLocksProperty()));
  }

  /**
   * {@inheritdoc}
   */
  public function hasConflict(EntityInterface $entity, array $externalData): bool {
    $checksumProperty = $this->resolveChecksumProperty($entity);
    if ($checksumProperty === NULL) {
      return FALSE;
    }

    $internalChecksum = $this->readStringProperty($entity, $checksumProperty);
    $externalChecksum = (string) ($externalData['checksum'] ?? '');

    if ($internalChecksum === '' || $externalChecksum === '') {
      return FALSE;
    }

    if ($internalChecksum === $externalChecksum) {
      return FALSE;
    }

    $window = $this->conflictWindowProvider->getConflictWindowSeconds();
    if ($window > 0 && !$this->hasRecentLocalModification($entity, $window)) {
      return FALSE;
    }

    $this->logConflict($entity, $internalChecksum, $externalChecksum);

    return TRUE;
  }

  /**
   * Logs a governance conflict event.
   */
  private function logConflict(EntityInterface $entity, string $internalChecksum, string $externalChecksum): void {
    $this->logger->notice('ps_core governance: {action}', [
      'action' => 'conflict_detected',
      'entity_type' => $entity->getEntityTypeId(),
      'entity_id' => $entity->id(),
      'internal_checksum' => $internalChecksum,
      'external_checksum' => $externalChecksum,
    ]);
  }

  /**
   * Whether the entity was modified locally after the last CRM import.
   *
   * Used when conflict_window_seconds is greater than zero.
   */
  private function hasRecentLocalModification(EntityInterface $entity, int $windowSeconds): bool {
    $changedTime = $this->getEntityChangedTime($entity);
    if ($changedTime === NULL) {
      return FALSE;
    }

    $lastImportTime = $this->getLastImportTimestamp($entity);
    if ($lastImportTime !== NULL && $changedTime <= $lastImportTime) {
      return FALSE;
    }

    return ($this->time->getRequestTime() - $changedTime) <= $windowSeconds;
  }

  /**
   * Reads the entity changed timestamp when available.
   */
  private function getEntityChangedTime(EntityInterface $entity): ?int {
    if (!method_exists($entity, 'getChangedTime')) {
      return NULL;
    }

    $changed = $entity->getChangedTime();
    return is_int($changed) ? $changed : NULL;
  }

  /**
   * Reads the last CRM import timestamp from source tracking metadata.
   */
  private function getLastImportTimestamp(EntityInterface $entity): ?int {
    $trackingProperty = $this->resolveTrackingProperty($entity);
    if ($trackingProperty === NULL) {
      return NULL;
    }

    $raw = $this->readStringProperty($entity, $trackingProperty);
    if ($raw === '') {
      return NULL;
    }

    try {
      $tracking = json_decode($raw, TRUE, 512, JSON_THROW_ON_ERROR);
    }
    catch (\JsonException) {
      return NULL;
    }

    if (!is_array($tracking)) {
      return NULL;
    }

    foreach (['import_timestamp', 'tracked_at'] as $key) {
      if (!isset($tracking[$key])) {
        continue;
      }
      $timestamp = $this->normalizeTimestamp($tracking[$key]);
      if ($timestamp !== NULL) {
        return $timestamp;
      }
    }

    return NULL;
  }

  /**
   * Normalizes a tracking timestamp to a Unix timestamp.
   */
  private function normalizeTimestamp(mixed $value): ?int {
    if (is_int($value)) {
      return $value;
    }

    if (is_string($value) && ctype_digit($value)) {
      return (int) $value;
    }

    if (is_string($value) && $value !== '') {
      $parsed = strtotime($value);
      return $parsed === FALSE ? NULL : $parsed;
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function applyMergeStrategy(
    EntityInterface $entity,
    array $externalData,
    string $fieldName,
    string $strategy,
  ): bool {
    if ($this->isCatalogueProtected($entity)) {
      $this->logger->debug(
        'Entity @type:@id is catalogue protected - skipping field @field update',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
          '@field' => $fieldName,
        ]
      );
      return FALSE;
    }

    if ($this->isFieldLocked($entity, $fieldName)) {
      $this->logger->debug(
        'Entity @type:@id field @field is locked - skipping update',
        [
          '@type' => $entity->getEntityTypeId(),
          '@id' => $entity->id(),
          '@field' => $fieldName,
        ]
      );
      return FALSE;
    }

    $externalValue = $externalData[$fieldName] ?? NULL;
    if ($externalValue === NULL) {
      return FALSE;
    }

    if ($this->isConfigEntity($entity)) {
      return $this->applyConfigEntityMergeStrategy($entity, $fieldName, $externalValue, $strategy);
    }

    if (!$entity instanceof FieldableEntityInterface || !$entity->hasField($fieldName)) {
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

    switch ($strategy) {
      case 'EXTERNAL_WINS':
        $entity->set($fieldName, $externalValue);
        return TRUE;

      case 'INTERNAL_WINS':
        if ($entity->isNew()) {
          $entity->set($fieldName, $externalValue);
          return TRUE;
        }

        $internalValue = $entity->get($fieldName)->value;
        if ($internalValue === NULL || $internalValue === '') {
          $entity->set($fieldName, $externalValue);
          return TRUE;
        }

        return FALSE;

      case 'MERGE_APPEND':
        $internalValue = $entity->get($fieldName)->getValue();
        $mergedValue = array_unique(
          array_merge((array) $internalValue, (array) $externalValue),
          SORT_REGULAR
        );
        $entity->set($fieldName, $mergedValue);
        return TRUE;

      case 'SKIP':
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
    $trackingProperty = $this->resolveTrackingProperty($entity);
    if ($trackingProperty === NULL) {
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
      $metadata['tracked_at'] = $this->time->getRequestTime();
    }

    $this->writeProperty(
      $entity,
      $trackingProperty,
      json_encode($metadata, JSON_THROW_ON_ERROR),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function storeChecksum(EntityInterface $entity, string $checksum): void {
    $checksumProperty = $this->resolveChecksumProperty($entity);
    if ($checksumProperty === NULL) {
      return;
    }

    $this->writeProperty($entity, $checksumProperty, $checksum);
  }

  /**
   * {@inheritdoc}
   */
  public function getStoredChecksum(EntityInterface $entity): string {
    $checksumProperty = $this->resolveChecksumProperty($entity);
    if ($checksumProperty === NULL) {
      return '';
    }

    return $this->readStringProperty($entity, $checksumProperty);
  }

  /**
   * {@inheritdoc}
   */
  public function computeChecksum(array $data): string {
    $data = $this->sortArrayRecursive($data);
    return hash('sha256', serialize($data));
  }

  /**
   * Applies a merge strategy to a config entity property.
   */
  private function applyConfigEntityMergeStrategy(
    EntityInterface $entity,
    string $fieldName,
    mixed $externalValue,
    string $strategy,
  ): bool {
    switch ($strategy) {
      case 'EXTERNAL_WINS':
        $entity->set($fieldName, $externalValue);
        return TRUE;

      case 'INTERNAL_WINS':
        if ($entity->isNew()) {
          $entity->set($fieldName, $externalValue);
          return TRUE;
        }

        $internalValue = $entity->get($fieldName);
        if ($internalValue === NULL || $internalValue === '') {
          $entity->set($fieldName, $externalValue);
          return TRUE;
        }

        return FALSE;

      case 'MERGE_APPEND':
        $internalValue = $entity->get($fieldName);
        $mergedValue = array_unique(
          array_merge((array) $internalValue, (array) $externalValue),
          SORT_REGULAR
        );
        $entity->set($fieldName, $mergedValue);
        return TRUE;

      case 'SKIP':
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
   * Normalizes a field lock map to booleans keyed by field name.
   *
   * @return array<string, bool>
   *   Field names keyed by themselves with TRUE when locked.
   */
  private function normalizeFieldLocks(mixed $fieldLocks): array {
    if (!is_array($fieldLocks)) {
      return [];
    }

    $normalized = [];
    foreach ($fieldLocks as $fieldName => $locked) {
      if (!is_string($fieldName) || $fieldName === '') {
        continue;
      }
      if ($locked) {
        $normalized[$fieldName] = TRUE;
      }
    }

    return $normalized;
  }

  /**
   * Reads a boolean property from a supported entity.
   */
  private function readBooleanProperty(EntityInterface $entity, string $property): bool {
    return (bool) $this->readRawProperty($entity, $property);
  }

  /**
   * Reads a string property from a supported entity.
   */
  private function readStringProperty(EntityInterface $entity, string $property): string {
    $value = $this->readRawProperty($entity, $property);
    return $value === NULL ? '' : (string) $value;
  }

  /**
   * Reads a property from a fieldable or config entity.
   */
  private function readRawProperty(EntityInterface $entity, string $property): mixed {
    if ($entity instanceof FieldableEntityInterface && $entity->hasField($property)) {
      return $entity->get($property)->value;
    }

    if ($this->isConfigEntity($entity)) {
      return $entity->get($property);
    }

    return NULL;
  }

  /**
   * Writes a property on a fieldable or config entity.
   */
  private function writeProperty(EntityInterface $entity, string $property, mixed $value): void {
    if ($entity instanceof FieldableEntityInterface && $entity->hasField($property)) {
      $entity->set($property, $value);
      return;
    }

    if ($this->isConfigEntity($entity)) {
      $entity->set($property, $value);
    }
  }

  /**
   * Returns whether the entity is a registered config entity.
   */
  private function isConfigEntity(EntityInterface $entity): bool {
    return $this->configEntityProtectionRegistry->supports($entity);
  }

  /**
   * Resolves the internal lock property for an entity.
   */
  private function resolveLockProperty(EntityInterface $entity): ?string {
    if ($entity instanceof FieldableEntityInterface) {
      foreach (['internal_lock', 'field_internal_lock'] as $property) {
        if ($entity->hasField($property)) {
          return $property;
        }
      }
    }

    $definition = $this->configEntityProtectionRegistry->getDefinitionForEntity($entity);
    return $definition?->getLockProperty();
  }

  /**
   * Resolves the checksum property for an entity.
   */
  private function resolveChecksumProperty(EntityInterface $entity): ?string {
    if ($entity instanceof FieldableEntityInterface) {
      foreach (['checksum', 'field_source_checksum'] as $property) {
        if ($entity->hasField($property)) {
          return $property;
        }
      }
    }

    $definition = $this->configEntityProtectionRegistry->getDefinitionForEntity($entity);
    return $definition?->getChecksumProperty();
  }

  /**
   * Resolves the source tracking property for an entity.
   */
  private function resolveTrackingProperty(EntityInterface $entity): ?string {
    if ($entity instanceof FieldableEntityInterface) {
      foreach (['source_tracking', 'field_source_tracking'] as $property) {
        if ($entity->hasField($property)) {
          return $property;
        }
      }
    }

    $definition = $this->configEntityProtectionRegistry->getDefinitionForEntity($entity);
    return $definition?->getTrackingProperty();
  }

}
