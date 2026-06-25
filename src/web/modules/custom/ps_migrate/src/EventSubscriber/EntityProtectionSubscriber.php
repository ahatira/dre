<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
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
      if (!$entity instanceof EntityInterface || !$entity instanceof FieldableEntityInterface) {
        return;
      }

      if ($this->protectionManager->isProtected($entity)) {
        $strategy = $this->lockStrategy->getDefaultStrategy();
        if ($this->lockStrategy->shouldSkipRow()) {
          throw new MigrateSkipRowException(sprintf(
            'Entity %s:%s is protected (lock strategy: %s).',
            $entityType,
            $entity->id(),
            $strategy,
          ));
        }

        if ($this->lockStrategy->shouldPreserveInternalFields()) {
          $this->preserveInternalFieldValues($entity, $row);
          $this->logger->info(
            'Migration @migration: Entity @type:@id is protected — preserving internal field values (skip_field).',
            [
              '@migration' => $migration->id(),
              '@type' => $entityType,
              '@id' => $entity->id(),
            ]
          );
        }
        else {
          $this->logger->warning(
            'Migration @migration: Entity @type:@id is protected — CRM data will overwrite (log_only).',
            [
              '@migration' => $migration->id(),
              '@type' => $entityType,
              '@id' => $entity->id(),
            ]
          );
        }
        $row->setDestinationProperty('_is_protected', TRUE);
      }

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
    if (empty($destinationIds)) {
      return;
    }

    $migration = $event->getMigration();
    $entityType = $this->resolveEntityType($migration);
    if ($entityType === NULL) {
      return;
    }

    try {
      $entityId = is_array($destinationIds) ? reset($destinationIds) : $destinationIds;
      $entity = $this->entityTypeManager->getStorage($entityType)->load($entityId);
      if (!$entity instanceof EntityInterface || !$entity instanceof FieldableEntityInterface) {
        return;
      }

      $trackingField = $this->resolveTrackingField($entity);
      $checksumField = $this->resolveChecksumField($entity);
      if ($trackingField === NULL && $checksumField === NULL) {
        return;
      }

      $sourceIds = $row->getSourceIdValues();
      $sourceId = is_array($sourceIds) ? implode(':', $sourceIds) : (string) $sourceIds;
      $sourceConfig = $migration->getSourceConfiguration();
      $sourceData = [
        'source_system' => 'CRM_XML',
        'source_file' => $sourceConfig['urls'][0] ?? $sourceConfig['files'][0] ?? 'unknown',
        'source_id' => $sourceId,
        'import_timestamp' => $this->time->getCurrentTime(),
        'migration_id' => $migration->id(),
      ];

      $checksum = $this->protectionManager->computeChecksum($row->getSource());
      $needsSave = FALSE;

      if ($trackingField !== NULL) {
        $entity->set($trackingField, json_encode($sourceData, JSON_THROW_ON_ERROR));
        $needsSave = TRUE;
      }

      if ($checksumField !== NULL) {
        $entity->set($checksumField, $checksum);
        $needsSave = TRUE;
      }

      if ($needsSave) {
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
    }
    catch (\Exception $e) {
      $this->logger->error('Error tracking entity source: @message', [
        '@message' => $e->getMessage(),
      ]);
    }
  }

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

  private function resolveExistingEntityId(MigrationInterface $migration, Row $row): ?int {
    foreach (['entity_id', 'nid', 'id'] as $property) {
      $value = $row->getDestinationProperty($property);
      if ($value === NULL || $value === '') {
        continue;
      }
      return (int) (is_array($value) ? reset($value) : $value);
    }

    $destinationIds = $migration->getIdMap()->lookupDestinationIds($row->getSourceIdValues());
    if ($destinationIds !== []) {
      $first = reset($destinationIds);
      if (is_array($first)) {
        return (int) reset($first);
      }
    }

    return NULL;
  }

  /**
   * Preserves non-empty internal field values on the migration row.
   */
  private function preserveInternalFieldValues(FieldableEntityInterface $entity, Row $row): void {
    foreach ($row->getDestination() as $property => $value) {
      if (!is_string($property) || !$entity->hasField($property)) {
        continue;
      }
      if ($this->lockStrategy->getFieldStrategy($property) === ImportPipelineLockStrategy::STRATEGY_SKIP_ROW) {
        continue;
      }
      $internal = $entity->get($property)->getValue();
      if ($internal === [] || $internal === NULL) {
        continue;
      }
      $row->setDestinationProperty($property, $internal);
    }
  }

  private function resolveTrackingField(EntityInterface $entity): ?string {
    foreach (['source_tracking', 'field_source_tracking'] as $field) {
      if ($entity->hasField($field)) {
        return $field;
      }
    }
    return NULL;
  }

  private function resolveChecksumField(EntityInterface $entity): ?string {
    foreach (['checksum', 'field_source_checksum'] as $field) {
      if ($entity->hasField($field)) {
        return $field;
      }
    }
    return NULL;
  }

}
