<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_search\Service\FeatureFilterSyncManager;
use Psr\Log\LoggerInterface;

/**
 * Reacts to feature definition CRUD to keep search index and filters in sync.
 */
final class FeatureFilterEntityHooks {

  private const SETTINGS_CONFIG = 'ps_search.feature_filter_sync';

  private LoggerInterface $logger;

  public function __construct(
    private readonly FeatureFilterSyncManager $syncManager,
    private readonly ConfigFactoryInterface $configFactory,
    LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->logger = $loggerFactory->get('ps_search');
  }

  /**
   * Syncs search configuration when a feature definition is created.
   */
  #[Hook('entity_insert')]
  public function entityInsert(EntityInterface $entity): void {
    $this->handleDefinitionChange($entity);
  }

  /**
   * Syncs search configuration when a feature definition is updated.
   */
  #[Hook('entity_update')]
  public function entityUpdate(EntityInterface $entity): void {
    $this->handleDefinitionChange($entity);
  }

  /**
   * Syncs search configuration when a feature definition is deleted.
   */
  #[Hook('entity_delete')]
  public function entityDelete(EntityInterface $entity): void {
    $this->handleDefinitionChange($entity);
  }

  private function handleDefinitionChange(EntityInterface $entity): void {
    if ($entity->getEntityTypeId() !== 'fb_feature_definition') {
      return;
    }

    if (!$this->shouldAutoSyncOnDefinitionChange()) {
      return;
    }

    if (!$entity instanceof FeatureDefinition) {
      return;
    }

    $definitionId = (string) $entity->id();
    if ($definitionId === '') {
      return;
    }

    try {
      $stats = $this->syncManager->handleFeatureDefinitionLifecycle($definitionId);

      if (!empty($stats['changed'])) {
        $this->logger->notice(
          'Feature definition @id triggered search sync (added: @added, updated: @updated, removed: @removed, indexed: @indexed).',
          [
            '@id' => $definitionId,
            '@added' => (int) ($stats['added'] ?? 0),
            '@updated' => (int) ($stats['updated'] ?? 0),
            '@removed' => (int) ($stats['removed'] ?? 0),
            '@indexed' => (int) ($stats['indexed'] ?? 0),
          ]
        );
      }
      elseif ((int) ($stats['indexed'] ?? 0) > 0) {
        $this->logger->notice(
          'Feature definition @id change reindexed @count offer(s).',
          [
            '@id' => $definitionId,
            '@count' => (int) $stats['indexed'],
          ]
        );
      }
    }
    catch (\Throwable $e) {
      $this->logger->error(
        'Feature definition @id search sync failed: @message',
        [
          '@id' => $definitionId,
          '@message' => $e->getMessage(),
        ]
      );
    }
  }

  private function shouldAutoSyncOnDefinitionChange(): bool {
    return (bool) ($this->configFactory->get(self::SETTINGS_CONFIG)->get('auto_sync_on_definition_change') ?? TRUE);
  }

}
