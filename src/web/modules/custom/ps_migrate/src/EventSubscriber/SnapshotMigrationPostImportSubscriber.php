<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\EventSubscriber;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\ImportGovernance\ImportGovernanceSnapshotEntityKey;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_core\Service\ImportGovernanceSnapshotSynchronizer;
use Drupal\ps_migrate\Service\CrmOfferXmlMode;
use Drupal\ps_migrate\Service\CrmXmlSnapshotBuilder;
use Drupal\ps_migrate\Service\CrmXmlSnapshotMigrationProjector;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Synchronizes offers and agents against the CRM XML snapshot after import.
 */
final class SnapshotMigrationPostImportSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly CrmXmlSnapshotBuilder $snapshotBuilder,
    private readonly CrmXmlSnapshotMigrationProjector $snapshotProjector,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ImportGovernanceRegistry $governanceRegistry,
    private readonly ImportGovernanceSnapshotSynchronizer $snapshotSynchronizer,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      MigrateEvents::POST_IMPORT => ['onPostImport', 0],
    ];
  }

  /**
   * Applies snapshot governance rules after supported migrations complete.
   */
  public function onPostImport(MigrateImportEvent $event): void {
    $migration = $event->getMigration();
    $policy = $this->governanceRegistry->getSnapshotPostImportPolicyForMigration($migration->id());
    if ($policy === NULL) {
      return;
    }

    $files = $this->normalizeFiles($migration->getSourceConfiguration());
    if ($files === []) {
      return;
    }

    $this->dispatchSnapshotSync($migration, $files, $policy);
  }

  /**
   * Dispatches snapshot synchronization based on the governance policy scope.
   *
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   Completed migration plugin.
   * @param string[] $files
   *   Source XML file paths.
   * @param \Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface $policy
   *   Snapshot governance policy for the migration.
   */
  private function dispatchSnapshotSync(
    MigrationInterface $migration,
    array $files,
    ImportGovernanceSnapshotPostImportPolicyInterface $policy,
  ): void {
    if (!$policy instanceof ImportGovernancePolicyInterface) {
      return;
    }

    $entityTypes = $policy->getEntityTypeIds();
    $bundles = $policy->getBundleIds();

    if (in_array('node', $entityTypes, TRUE) && in_array('offer', $bundles, TRUE)) {
      $this->synchronizeOffers($migration, $files, $policy);
      return;
    }

    if (in_array('ps_agent', $entityTypes, TRUE)) {
      $this->synchronizeAgents($migration, $files, $policy);
      return;
    }

    if (in_array('media', $entityTypes, TRUE)) {
      $this->synchronizeMedia($migration, $files, $policy);
    }
  }

  /**
   * Synchronizes offer publication status against the XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   */
  private function synchronizeOffers(
    MigrationInterface $migration,
    array $files,
    ImportGovernanceSnapshotPostImportPolicyInterface $policy,
  ): void {
    $activeBusinessIds = $this->snapshotBuilder->buildOfferBusinessIds($files);
    $fieldSnapshots = $this->snapshotProjector->buildFieldSnapshots($migration);
    $entityKey = ImportGovernanceSnapshotEntityKey::encode('node', 'offer');
    $storage = $this->entityTypeManager->getStorage('node');
    $nids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', 'offer')
      ->exists('field_business_id')
      ->execute();

    foreach ($storage->loadMultiple($nids) as $offer) {
      if (!$offer instanceof NodeInterface) {
        continue;
      }

      $businessId = trim((string) $offer->get('field_business_id')->value);
      if ($businessId === '') {
        continue;
      }

      $shouldBeActive = isset($activeBusinessIds[$businessId]);
      $this->applySnapshotTransition($offer, $shouldBeActive, $policy, 'offer', $businessId);
      if ($shouldBeActive) {
        $this->syncSnapshotFields($offer, $fieldSnapshots[$businessId] ?? [], $policy, $entityKey);
      }
    }
  }

  /**
   * Synchronizes agent status against the XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   */
  private function synchronizeAgents(
    MigrationInterface $migration,
    array $files,
    ImportGovernanceSnapshotPostImportPolicyInterface $policy,
  ): void {
    $activeUids = $this->snapshotBuilder->buildAgentUids($files);
    $fieldSnapshots = $this->snapshotProjector->buildFieldSnapshots($migration);
    $entityKey = ImportGovernanceSnapshotEntityKey::encode('ps_agent');
    $storage = $this->entityTypeManager->getStorage('ps_agent');
    $ids = $storage->getQuery()->accessCheck(FALSE)->execute();
    $idMap = $migration->getIdMap();

    foreach ($storage->loadMultiple($ids) as $agent) {
      if (!$agent instanceof ContentEntityInterface) {
        continue;
      }

      $sourceIds = $idMap->lookupSourceId(['id' => $agent->id()]);
      $uid = trim((string) ($sourceIds['uid'] ?? ''));
      if ($uid === '') {
        continue;
      }

      $shouldBeActive = isset($activeUids[$uid]);
      $this->applySnapshotTransition($agent, $shouldBeActive, $policy, 'agent', $uid);
      if ($shouldBeActive) {
        $this->syncSnapshotFields($agent, $fieldSnapshots[$uid] ?? [], $policy, $entityKey);
      }
    }
  }

  /**
   * Synchronizes media publication status against the XML snapshot.
   *
   * @param string[] $files
   *   Source XML files.
   */
  private function synchronizeMedia(
    MigrationInterface $migration,
    array $files,
    ImportGovernanceSnapshotPostImportPolicyInterface $policy,
  ): void {
    $mode = (string) ($migration->getSourceConfiguration()['mode'] ?? '');
    $isVirtualTour = $mode === CrmOfferXmlMode::MEDIA_VIS;
    $activeKeys = $isVirtualTour
      ? $this->snapshotBuilder->buildMediaVisCompositeKeys($files)
      : $this->snapshotBuilder->buildMediaExtCompositeKeys($files);
    $fieldSnapshots = $this->snapshotProjector->buildFieldSnapshots($migration);
    $entityKey = ImportGovernanceSnapshotEntityKey::encode(
      'media',
      $isVirtualTour ? 'visite_guided' : 'image',
    );

    $storage = $this->entityTypeManager->getStorage('media');
    $ids = $storage->getQuery()->accessCheck(FALSE)->execute();
    $idMap = $migration->getIdMap();

    foreach ($storage->loadMultiple($ids) as $media) {
      if (!$media instanceof ContentEntityInterface) {
        continue;
      }

      $sourceIds = $idMap->lookupSourceId(['mid' => $media->id()]);
      $businessId = trim((string) ($sourceIds['business_id_parent'] ?? ''));
      $order = (int) ($sourceIds['order'] ?? 0);
      if ($businessId === '' || $order <= 0) {
        continue;
      }

      $snapshotKey = $businessId . ':' . $order;
      $shouldBeActive = isset($activeKeys[$snapshotKey]);
      $this->applySnapshotTransition($media, $shouldBeActive, $policy, 'media', $snapshotKey);
      if ($shouldBeActive) {
        $this->syncSnapshotFields($media, $fieldSnapshots[$snapshotKey] ?? [], $policy, $entityKey);
      }
    }
  }

  /**
   * Applies configured snapshot field values for a present entity.
   *
   * @param array<string, mixed> $snapshotRow
   *   Snapshot field values keyed by field machine name.
   */
  private function syncSnapshotFields(
    ContentEntityInterface $entity,
    array $snapshotRow,
    ImportGovernanceSnapshotPostImportPolicyInterface $policy,
    string $entityKey,
  ): void {
    if ($snapshotRow === []) {
      return;
    }
    if ($entity->hasField('field_internal_lock') && (bool) $entity->get('field_internal_lock')->value) {
      return;
    }

    $fields = $policy->getSnapshotFieldSyncFields($entityKey);
    if ($fields === []) {
      return;
    }

    if ($this->snapshotSynchronizer->synchronizeFields($entity, $snapshotRow, $fields)) {
      $entity->save();
    }
  }

  /**
   * Applies reactivation or deactivation for a snapshot-managed entity.
   */
  private function applySnapshotTransition(
    ContentEntityInterface $entity,
    bool $shouldBeActive,
    ImportGovernanceSnapshotPostImportPolicyInterface $policy,
    string $label,
    string $sourceId,
  ): void {
    $isActive = $this->isEntityActive($entity);

    if ($policy->shouldReactivatePresentInSnapshot() && $shouldBeActive && !$isActive) {
      $this->setEntityActive($entity, TRUE);
      $entity->save();
      $this->logger->info('Reactivated @label @id from CRM XML snapshot.', [
        '@label' => $label,
        '@id' => $sourceId,
      ]);
      return;
    }

    if ($policy->shouldDeactivateMissingEntity($entity, $shouldBeActive)) {
      $this->setEntityActive($entity, FALSE);
      $entity->save();
      $this->logger->warning('Deactivated @label @id because it disappeared from CRM XML snapshot.', [
        '@label' => $label,
        '@id' => $sourceId,
      ]);
    }
  }

  /**
   * Whether the entity is currently active/published.
   */
  private function isEntityActive(ContentEntityInterface $entity): bool {
    if ($entity instanceof NodeInterface) {
      return $entity->isPublished();
    }

    return (bool) $entity->get('status')->value;
  }

  /**
   * Sets the entity active/published flag.
   */
  private function setEntityActive(ContentEntityInterface $entity, bool $active): void {
    if ($entity instanceof NodeInterface) {
      $entity->setPublished($active);
      return;
    }

    $entity->set('status', $active);
  }

  /**
   * Normalizes source file paths from a migration plugin configuration.
   *
   * @param array<string, mixed> $sourceConfig
   *   Migration source configuration.
   *
   * @return string[]
   *   Non-empty trimmed file paths.
   */
  private function normalizeFiles(array $sourceConfig): array {
    $files = $sourceConfig['files'] ?? ($sourceConfig['urls'] ?? []);
    if (!is_array($files)) {
      $files = [$files];
    }

    $files = array_values(array_filter(array_map(
      static fn(mixed $file): string => trim((string) $file),
      $files,
    ), static fn(string $file): bool => $file !== ''));

    return $files;
  }

}
