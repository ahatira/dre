<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_migrate\Service\ImportPipelineLockStrategy;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Integrates entity protection with Migrate imports.
 */
final class EntityProtectionSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly EntityProtectionManagerInterface $protectionManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly LoggerInterface $logger,
    private readonly TimeInterface $time,
    private readonly ImportPipelineLockStrategy $lockStrategy,
    private readonly ImportGovernanceRegistry $governanceRegistry,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::PRE_ROW_SAVE => ['onPreRowSave', 100],
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave', -100],
    ];
  }

  /**
   * Before entity save: check protection and conflicts.
   */
  public function onPreRowSave(MigratePreRowSaveEvent $event): void {
    $row = $event->getRow();
    $migration = $event->getMigration();
    $entityType = $this->resolveEntityType($migration);
    if ($entityType === NULL) {
      return;
    }

    $entityId = $this->resolveExistingEntityId($migration, $row);
    if ($entityId === NULL) {
      return;
    }

    try {
      $entity = $this->entityTypeManager->getStorage($entityType)->load($entityId);
      if (!$entity instanceof EntityInterface || !$this->protectionManager->supports($entity)) {
        return;
      }

      if ($this->protectionManager->isCatalogueProtected($entity)) {
        if ($this->shouldSkipProtectedRow($entity)) {
          throw new MigrateSkipRowException(sprintf(
            'Entity %s:%s is catalogue protected (lock strategy: %s).',
            $entityType,
            $entity->id(),
            $this->resolveLockStrategy($entity),
          ));
        }

        if ($this->shouldPreserveProtectedFields($entity)) {
          $this->preserveInternalPropertyValues($entity, $row);
          $this->logger->info(
            'Migration @migration: Entity @type:@id is catalogue protected — preserving internal field values (skip_field).',
            [
              '@migration' => $migration->id(),
              '@type' => $entityType,
              '@id' => $entity->id(),
            ]
          );
        }
        else {
          $this->logger->warning(
            'Migration @migration: Entity @type:@id is catalogue protected — CRM data will overwrite (log_only).',
            [
              '@migration' => $migration->id(),
              '@type' => $entityType,
              '@id' => $entity->id(),
            ]
          );
        }
        $row->setDestinationProperty('_is_protected', TRUE);
      }

      $this->preserveLockedPropertyValues($entity, $row);
      $this->preservePolicyPropertyValues($entity, $row);

      $externalChecksum = $this->protectionManager->computeChecksum($row->getSource());
      if ($this->protectionManager->hasConflict($entity, ['checksum' => $externalChecksum])) {
        $this->logger->warning(
          'Migration @migration: Conflict detected for @type:@id',
          [
            '@migration' => $migration->id(),
            '@type' => $entityType,
            '@id' => $entity->id(),
          ]
        );
        $row->setDestinationProperty('_has_conflict', TRUE);
      }
    }
    catch (MigrateSkipRowException $exception) {
      throw $exception;
    }
    catch (\Exception $e) {
      $this->logger->error('Error checking entity protection: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
  }

  /**
   * After entity save: track source and compute checksum.
   */
  public function onPostRowSave(MigratePostRowSaveEvent $event): void {
    $row = $event->getRow();
    $destinationIds = $event->getDestinationIdValues();
    if ($destinationIds === [] || $destinationIds === NULL) {
      return;
    }

    $migration = $event->getMigration();
    $entityType = $this->resolveEntityType($migration);
    if ($entityType === NULL) {
      return;
    }

    try {
      $entityId = $this->normalizeEntityId(is_array($destinationIds) ? reset($destinationIds) : $destinationIds);
      if ($entityId === NULL) {
        return;
      }

      $entity = $this->entityTypeManager->getStorage($entityType)->load($entityId);
      if (!$entity instanceof EntityInterface || !$this->protectionManager->supports($entity)) {
        return;
      }

      $sourceIds = $row->getSourceIdValues();
      $sourceId = is_array($sourceIds) ? implode(':', $sourceIds) : (string) $sourceIds;
      $sourceConfig = $migration->getSourceConfiguration();

      $this->protectionManager->trackSource($entity, [
        'source_system' => 'CRM_XML',
        'source_file' => $sourceConfig['urls'][0] ?? $sourceConfig['files'][0] ?? 'unknown',
        'source_id' => $sourceId,
        'import_timestamp' => $this->time->getCurrentTime(),
        'migration_id' => $migration->id(),
      ]);

      $checksum = $this->protectionManager->computeChecksum($row->getSource());
      $this->protectionManager->storeChecksum($entity, $checksum);
      $entity->save();

      $this->logger->info(
        'Migration @migration: Protection tracking saved for @type:@id (checksum: @checksum)',
        [
          '@migration' => $migration->id(),
          '@type' => $entityType,
          '@id' => $entity->id(),
          '@checksum' => substr($checksum, 0, 8),
        ]
      );
    }
    catch (\Exception $e) {
      $this->logger->error('Error tracking entity source: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
  }

  /**
   * Resolves the destination entity type from a migration plugin.
   */
  private function resolveEntityType(MigrationInterface $migration): ?string {
    $destinationConfig = $migration->getDestinationConfiguration();
    if (!empty($destinationConfig['entity_type'])) {
      return (string) $destinationConfig['entity_type'];
    }

    $plugin = (string) ($destinationConfig['plugin'] ?? '');
    if (str_starts_with($plugin, 'entity:')) {
      return substr($plugin, strlen('entity:'));
    }

    return NULL;
  }

  /**
   * Resolves an existing destination entity ID from the migration row.
   */
  private function resolveExistingEntityId(MigrationInterface $migration, Row $row): ?string {
    foreach (['entity_id', 'nid', 'id'] as $property) {
      $value = $row->getDestinationProperty($property);
      if ($value === NULL || $value === '') {
        continue;
      }
      return $this->normalizeEntityId(is_array($value) ? reset($value) : $value);
    }

    $destinationIds = $migration->getIdMap()->lookupDestinationIds($row->getSourceIdValues());
    if ($destinationIds !== []) {
      $first = reset($destinationIds);
      if (is_array($first)) {
        return $this->normalizeEntityId(reset($first));
      }
      return $this->normalizeEntityId($first);
    }

    return NULL;
  }

  /**
   * Normalizes destination IDs for fieldable and config entities.
   */
  private function normalizeEntityId(mixed $value): ?string {
    if ($value === NULL) {
      return NULL;
    }

    if (is_int($value)) {
      return (string) $value;
    }

    if (!is_string($value)) {
      return NULL;
    }

    $value = trim($value);
    if ($value === '') {
      return NULL;
    }

    return $value;
  }

  /**
   * Whether a protected feature catalogue row should be skipped entirely.
   */
  private function shouldSkipProtectedRow(EntityInterface $entity): bool {
    $policy = $this->governanceRegistry->getPolicyForEntity($entity);
    if ($policy !== NULL) {
      return $policy->shouldSkipProtectedRow($entity);
    }

    return $this->lockStrategy->shouldSkipRow();
  }

  /**
   * Whether protected entities should preserve internal field values.
   */
  private function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    $policy = $this->governanceRegistry->getPolicyForEntity($entity);
    if ($policy !== NULL) {
      return $policy->shouldPreserveProtectedFields($entity);
    }

    return $this->lockStrategy->shouldPreserveInternalFields();
  }

  /**
   * Resolves the effective lock strategy label for logging.
   */
  private function resolveLockStrategy(EntityInterface $entity): string {
    $policy = $this->governanceRegistry->getPolicyForEntity($entity);
    if ($policy !== NULL) {
      return $policy->resolveEffectiveLockStrategy($entity->getEntityTypeId());
    }

    return $this->lockStrategy->getDefaultStrategy();
  }

  /**
   * Preserves non-empty internal property values on the migration row.
   */
  private function preserveInternalPropertyValues(EntityInterface $entity, Row $row): void {
    foreach ($row->getDestination() as $property => $value) {
      if (!is_string($property) || !$this->entityHasProperty($entity, $property)) {
        continue;
      }
      if ($this->lockStrategy->getFieldStrategy($property) === ImportPipelineLockStrategy::STRATEGY_SKIP_ROW) {
        continue;
      }

      $internal = $this->readPropertyValue($entity, $property);
      if ($internal === NULL || $internal === '' || $internal === []) {
        continue;
      }

      $row->setDestinationProperty($property, $internal);
    }
  }

  /**
   * Preserves individually locked property values on the migration row.
   */
  private function preserveLockedPropertyValues(EntityInterface $entity, Row $row): void {
    foreach ($row->getDestination() as $property => $value) {
      if (!is_string($property) || !$this->entityHasProperty($entity, $property)) {
        continue;
      }
      if (!$this->protectionManager->isFieldLocked($entity, $property)) {
        continue;
      }

      $row->setDestinationProperty($property, $this->readPropertyValue($entity, $property));
    }
  }

  /**
   * Preserves domain-specific property values defined by governance policies.
   */
  private function preservePolicyPropertyValues(EntityInterface $entity, Row $row): void {
    $policy = $this->governanceRegistry->getPolicyForEntity($entity);
    if ($policy === NULL) {
      return;
    }

    foreach ($policy->getAdditionalPreservedProperties($entity) as $property) {
      if (!$this->entityHasProperty($entity, $property)) {
        continue;
      }

      $row->setDestinationProperty($property, $this->readPropertyValue($entity, $property));
    }
  }

  /**
   * Checks whether an entity exposes a readable property.
   */
  private function entityHasProperty(EntityInterface $entity, string $property): bool {
    if (str_contains($property, '/')) {
      [$fieldName] = explode('/', $property, 2);
      if ($entity instanceof FieldableEntityInterface) {
        return $entity->hasField($fieldName);
      }

      return FALSE;
    }

    if ($entity instanceof FieldableEntityInterface) {
      return $entity->hasField($property);
    }

    return array_key_exists($property, $entity->toArray());
  }

  /**
   * Reads a scalar or structured property from a supported entity.
   */
  private function readPropertyValue(EntityInterface $entity, string $property): mixed {
    if (str_contains($property, '/')) {
      [$fieldName, $subProperty] = explode('/', $property, 2);
      if ($entity instanceof FieldableEntityInterface && $entity->hasField($fieldName)) {
        $item = $entity->get($fieldName)->first();
        if ($item === NULL) {
          return NULL;
        }

        return $item->get($subProperty)->getValue();
      }

      return NULL;
    }

    if ($entity instanceof FieldableEntityInterface && $entity->hasField($property)) {
      return $entity->get($property)->getValue();
    }

    return $entity->get($property);
  }

}
