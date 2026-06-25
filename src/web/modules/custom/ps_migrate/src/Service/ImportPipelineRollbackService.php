<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Psr\Log\LoggerInterface;

/**
 * Rolls back a successful CRM import run using its stored snapshot.
 */
final class ImportPipelineRollbackService {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly EntityProtectionManagerInterface $protectionManager,
    private readonly ImportPipelinePostRunIndexer $postRunIndexer,
    private readonly AccountProxyInterface $currentUser,
    private readonly TimeInterface $time,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Executes rollback for the given import run.
   *
   * @return array<string, mixed>
   *   Rollback summary counters and messages.
   */
  public function rollback(ImportRunInterface $run, bool $force = FALSE): array {
    $summary = [
      'run_id' => (int) $run->id(),
      'status' => 'failed',
      'blocked' => FALSE,
      'offers_unpublished' => 0,
      'offers_restored' => 0,
      'offers_skipped_locked' => 0,
      'offers_missing' => 0,
      'features_groups_restored' => 0,
      'features_definitions_restored' => 0,
      'warnings' => [],
      'errors' => [],
    ];

    if ($run->getPipelineStatus() !== ImportRunInterface::STATUS_SUCCESS) {
      $summary['errors'][] = 'Rollback is only available for successful import runs.';
      return $summary;
    }

    if ($run->getRollbackStatus() === ImportRunInterface::ROLLBACK_ROLLED_BACK) {
      $summary['errors'][] = 'This import run has already been rolled back.';
      return $summary;
    }

    $snapshot = $run->getSnapshot();
    if ($snapshot === []) {
      $summary['errors'][] = 'No rollback snapshot is stored for this import run.';
      return $summary;
    }

    if (!$force && $this->hasNewerSuccessfulRun($run)) {
      $summary['blocked'] = TRUE;
      $summary['errors'][] = 'A newer successful import run exists. Rollback blocked.';
      return $summary;
    }

    $offers = $snapshot['offers'] ?? [];
    if (is_array($offers)) {
      foreach ($offers['created'] ?? [] as $entry) {
        if (!is_array($entry)) {
          continue;
        }
        $this->rollbackCreatedOffer($entry, $summary);
      }

      foreach ($offers['updated'] ?? [] as $entry) {
        if (!is_array($entry)) {
          continue;
        }
        $this->rollbackUpdatedOffer($entry, $summary);
      }
    }

    $features = $snapshot['features'] ?? [];
    if (is_array($features)) {
      $this->rollbackFeatureEntities('fb_feature_group', $features['groups_deactivated'] ?? [], TRUE, $summary, 'features_groups_restored');
      $this->rollbackFeatureEntities('fb_feature_group', $features['groups_reactivated'] ?? [], FALSE, $summary, 'features_groups_restored');
      $this->rollbackFeatureEntities('fb_feature_definition', $features['definitions_deactivated'] ?? [], TRUE, $summary, 'features_definitions_restored');
      $this->rollbackFeatureEntities('fb_feature_definition', $features['definitions_reactivated'] ?? [], FALSE, $summary, 'features_definitions_restored');
    }

    $hasErrors = ($summary['errors'] ?? []) !== [];
    $hasWarnings = ($summary['warnings'] ?? []) !== [];
    $partial = $hasWarnings || ($summary['offers_missing'] ?? 0) > 0 || ($summary['offers_skipped_locked'] ?? 0) > 0;

    $status = $hasErrors
      ? ImportRunInterface::ROLLBACK_UNAVAILABLE
      : ($partial ? ImportRunInterface::ROLLBACK_PARTIAL : ImportRunInterface::ROLLBACK_ROLLED_BACK);
    if ($run->hasField('rollback_status')) {
      $run->set('rollback_status', $status);
    }
    $run->save();

    if (!$hasErrors) {
      $summary['status'] = $partial ? 'partial' : 'success';
      $summary['solr'] = $this->postRunIndexer->indexOffers();
      $this->logger->info('Rollback completed for import run @id.', ['@id' => $run->id()]);
    }

    return $summary;
  }

  /**
   * Whether rollback can be attempted for the given run.
   */
  public function canRollback(ImportRunInterface $run, bool $force = FALSE): bool {
    if ($run->getPipelineStatus() !== ImportRunInterface::STATUS_SUCCESS) {
      return FALSE;
    }
    if ($run->getRollbackStatus() === ImportRunInterface::ROLLBACK_ROLLED_BACK) {
      return FALSE;
    }
    if ($run->getSnapshot() === []) {
      return FALSE;
    }
    if (!$force && $this->hasNewerSuccessfulRun($run)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * @param array<string, mixed> $entry
   * @param array<string, mixed> $summary
   */
  private function rollbackCreatedOffer(array $entry, array &$summary): void {
    $nid = (int) ($entry['nid'] ?? 0);
    if ($nid <= 0) {
      return;
    }

    $node = $this->entityTypeManager->getStorage('node')->load($nid);
    if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
      $summary['offers_missing']++;
      $summary['warnings'][] = sprintf('Created offer node %d no longer exists.', $nid);
      return;
    }

    if ($this->protectionManager->isProtected($node)) {
      $summary['offers_skipped_locked']++;
      $summary['warnings'][] = sprintf('Offer node %d is protected and was not unpublished.', $nid);
      return;
    }

    if (!$node->isPublished()) {
      return;
    }

    $node->setUnpublished();
    $node->save();
    $summary['offers_unpublished']++;
  }

  /**
   * @param array<string, mixed> $entry
   * @param array<string, mixed> $summary
   */
  private function rollbackUpdatedOffer(array $entry, array &$summary): void {
    $nid = (int) ($entry['nid'] ?? 0);
    $revisionId = (int) ($entry['revision_id'] ?? 0);
    if ($nid <= 0 || $revisionId <= 0) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $current = $storage->load($nid);
    if (!$current instanceof NodeInterface || $current->bundle() !== 'offer') {
      $summary['offers_missing']++;
      $summary['warnings'][] = sprintf('Updated offer node %d no longer exists.', $nid);
      return;
    }

    if ($this->protectionManager->isProtected($current)) {
      $summary['offers_skipped_locked']++;
      $summary['warnings'][] = sprintf('Offer node %d is protected and was not restored.', $nid);
      return;
    }

    $revision = $storage->loadRevision($revisionId);
    if (!$revision instanceof NodeInterface) {
      $summary['offers_missing']++;
      $summary['warnings'][] = sprintf('Revision %d for offer node %d is unavailable.', $revisionId, $nid);
      return;
    }

    if ((int) $revision->getRevisionId() === (int) $current->getRevisionId()) {
      return;
    }

    $revision->setNewRevision(TRUE);
    $revision->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime($this->time->getCurrentTime());
    $revision->setRevisionUserId((int) $this->currentUser->id());
    if ($revision->hasField('revision_log')) {
      $revision->set('revision_log', sprintf('Rollback of import run %d.', $summary['run_id']));
    }
    $storage->save($revision);
    $summary['offers_restored']++;
  }

  /**
   * @param array<string, mixed> $summary
   */
  private function rollbackFeatureEntities(
    string $entityType,
    mixed $ids,
    bool $activate,
    array &$summary,
    string $counterKey,
  ): void {
    if (!is_array($ids) || $ids === []) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage($entityType);
    foreach ($ids as $id) {
      $id = trim((string) $id);
      if ($id === '') {
        continue;
      }

      $entity = $storage->load($id);
      if ($entity === NULL) {
        $summary['warnings'][] = sprintf('%s %s no longer exists.', $entityType, $id);
        continue;
      }

      if ((bool) $entity->get('status')->value === $activate) {
        continue;
      }

      $entity->set('status', $activate ? 1 : 0);
      $entity->save();
      $summary[$counterKey]++;
    }
  }

  private function hasNewerSuccessfulRun(ImportRunInterface $run): bool {
    $started = (int) $run->get('started')->value;
    if ($started <= 0) {
      return FALSE;
    }

    $ids = $this->entityTypeManager->getStorage('import_run')->getQuery()
      ->accessCheck(FALSE)
      ->condition('pipeline_status', ImportRunInterface::STATUS_SUCCESS)
      ->condition('started', $started, '>')
      ->condition('id', $run->id(), '<>')
      ->range(0, 1)
      ->execute();

    return $ids !== [];
  }

}
