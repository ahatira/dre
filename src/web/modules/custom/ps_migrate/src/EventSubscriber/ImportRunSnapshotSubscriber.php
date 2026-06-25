<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePostRowSaveEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\ps_migrate\Service\ImportRunSnapshotCollector;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Captures rollback snapshot data during CRM offer migrations.
 */
final class ImportRunSnapshotSubscriber implements EventSubscriberInterface {

  /**
   * Offer-related migrations tracked for rollback snapshots.
   */
  private const OFFER_MIGRATIONS = [
    'ps_offer_from_xml',
    'ps_offer_translations_from_xml',
  ];

  public function __construct(
    private readonly ImportRunSnapshotCollector $snapshotCollector,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::PRE_ROW_SAVE => ['onPreRowSave', 50],
      MigrateEvents::POST_ROW_SAVE => ['onPostRowSave', 50],
    ];
  }

  /**
   * Captures the revision ID before an existing offer is updated.
   */
  public function onPreRowSave(MigratePreRowSaveEvent $event): void {
    if (!$this->snapshotCollector->isActive()) {
      return;
    }

    $migration = $event->getMigration();
    if (!in_array($migration->id(), self::OFFER_MIGRATIONS, TRUE)) {
      return;
    }

    if ($this->resolveEntityType($migration) !== 'node') {
      return;
    }

    $row = $event->getRow();
    $entityId = $this->resolveExistingEntityId($migration, $row);
    if ($entityId === NULL) {
      return;
    }

    $entity = $this->entityTypeManager->getStorage('node')->load($entityId);
    if (!$entity instanceof FieldableEntityInterface || $entity->bundle() !== 'offer') {
      return;
    }

    $businessId = trim((string) ($row->getSourceProperty('business_id') ?? ''));
    if ($businessId === '' && $entity->hasField('field_business_id')) {
      $businessId = trim((string) $entity->get('field_business_id')->value);
    }

    $backupRevisionId = $this->createBackupRevision($entity);
    if ($backupRevisionId === NULL) {
      return;
    }

    $this->snapshotCollector->stageOfferUpdate(
      (int) $entity->id(),
      $backupRevisionId,
      $businessId,
    );
  }

  /**
   * Records offers created during the import run.
   */
  public function onPostRowSave(MigratePostRowSaveEvent $event): void {
    if (!$this->snapshotCollector->isActive()) {
      return;
    }

    $migration = $event->getMigration();
    if ($migration->id() !== 'ps_offer_from_xml') {
      return;
    }

    $destinationIds = $event->getDestinationIdValues();
    if ($destinationIds === []) {
      return;
    }

    $nid = (int) (is_array($destinationIds) ? reset($destinationIds) : $destinationIds);
    if ($nid <= 0 || $this->snapshotCollector->hasStagedOfferUpdate($nid)) {
      return;
    }

    $row = $event->getRow();
    $businessId = trim((string) ($row->getSourceProperty('business_id') ?? ''));
    $this->snapshotCollector->recordOfferCreated($nid, $businessId);
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
   * Creates a non-default revision backup before migrate overwrites the offer.
   */
  private function createBackupRevision(FieldableEntityInterface $entity): ?int {
    if (!$entity instanceof \Drupal\Core\Entity\RevisionableInterface) {
      return NULL;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $backup = $storage->createRevision($entity, FALSE);
    $backup->setNewRevision(TRUE);
    $backup->isDefaultRevision(FALSE);
    if ($backup->hasField('revision_log')) {
      $backup->set('revision_log', 'CRM import rollback backup.');
    }
    $storage->save($backup);

    return (int) $backup->getRevisionId();
  }

}
