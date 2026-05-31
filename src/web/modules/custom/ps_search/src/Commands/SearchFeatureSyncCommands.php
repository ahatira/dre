<?php

declare(strict_types=1);

namespace Drupal\ps_search\Commands;

use Drupal\ps_search\Service\FeatureFilterSyncManager;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for dynamic feature filter synchronization.
 */
final class SearchFeatureSyncCommands extends DrushCommands {

  public function __construct(
    private readonly FeatureFilterSyncManager $syncManager,
  ) {
    parent::__construct();
  }

  /**
   * Syncs feature fields and filters from exposed feature definitions.
   */
  #[CLI\Command(name: 'ps:search:features:sync', aliases: ['ps-sfs'])]
  #[CLI\Option(name: 'prune', description: 'Remove feature fields/filters no longer exposed as filters (1/0).')]
  #[CLI\Usage(name: 'drush ps:search:features:sync', description: 'Sync fields and filters, pruning stale entries.')]
  #[CLI\Usage(name: 'drush ps:search:features:sync --prune=0', description: 'Sync fields and filters without deleting stale entries.')]
  public function sync(array $options = ['prune' => 1]): void {
    $prune = (int) ($options['prune'] ?? 1) === 1;
    $stats = $this->syncManager->sync($prune);

    $this->io()->success(sprintf(
      'Feature filter sync done (added=%d, updated=%d, removed=%d, changed=%s).',
      $stats['added'],
      $stats['updated'],
      $stats['removed'],
      $stats['changed'] ? 'yes' : 'no',
    ));
  }

  /**
   * Syncs features then runs immediate indexing for offers.
   */
  #[CLI\Command(name: 'ps:search:features:sync-index', aliases: ['ps-sfsi'])]
  #[CLI\Option(name: 'rebuild-tracker', description: 'Rebuild tracker before indexing (1/0).')]
  #[CLI\Usage(name: 'drush ps:search:features:sync-index', description: 'Sync and run immediate indexing for offers.')]
  #[CLI\Usage(name: 'drush ps:search:features:sync-index --rebuild-tracker=1', description: 'Sync, rebuild tracker and index immediately.')]
  public function syncIndex(array $options = ['rebuild-tracker' => 0]): void {
    $rebuildTracker = (int) ($options['rebuild-tracker'] ?? 0) === 1;
    $stats = $this->syncManager->syncAndIndex($rebuildTracker);

    $this->io()->success(sprintf(
      'Feature sync+index done (added=%d, updated=%d, removed=%d, changed=%s, indexed=%d).',
      $stats['added'],
      $stats['updated'],
      $stats['removed'],
      $stats['changed'] ? 'yes' : 'no',
      $stats['indexed'] ?? 0,
    ));
  }

}
