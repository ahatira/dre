<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\ps_search\Service\FeatureFilterSyncManager;
use Drupal\search_api\Entity\Index;
use Psr\Log\LoggerInterface;

/**
 * Cron hooks for feature filter synchronization.
 */
final class FeatureFilterCronHooks {

  private const SETTINGS_CONFIG = 'ps_search.feature_filter_sync';

  public function __construct(
    private readonly FeatureFilterSyncManager $syncManager,
    private readonly ConfigFactoryInterface $configFactory,
    LoggerChannelFactoryInterface $loggerFactory,
  ) {
    $this->logger = $loggerFactory->get('ps_search');
  }

  /**
   * Module logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private LoggerInterface $logger;

  /**
   * Keeps dynamic feature filters in sync during Drupal cron.
   */
  #[Hook('cron')]
  public function cron(): void {
    try {
      $settings = $this->configFactory->get(self::SETTINGS_CONFIG);
      $runImmediateIndex = (bool) $settings->get('cron_run_immediate_index');
      $indexOnlyOnChange = (bool) $settings->get('cron_index_only_on_change');

      $stats = $this->syncManager->sync(TRUE);

      if (!empty($stats['changed'])) {
        $this->logger->notice(
          'Feature filter sync updated configuration (added: @added, updated: @updated, removed: @removed).',
          [
            '@added' => (int) ($stats['added'] ?? 0),
            '@updated' => (int) ($stats['updated'] ?? 0),
            '@removed' => (int) ($stats['removed'] ?? 0),
          ]
        );
      }

      if ($runImmediateIndex) {
        $shouldIndex = !$indexOnlyOnChange || !empty($stats['changed']);
        if ($shouldIndex) {
          $index = Index::load('offers');
          if ($index) {
            $indexed = $index->indexItems('-1');
            $this->logger->notice('Feature filter cron indexed @count item(s).', [
              '@count' => $indexed,
            ]);
          }
        }
      }
    }
    catch (\Throwable $e) {
      $this->logger->error('Feature filter cron sync failed: @message', ['@message' => $e->getMessage()]);
    }
  }

}
