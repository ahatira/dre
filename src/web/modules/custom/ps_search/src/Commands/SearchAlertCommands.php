<?php

declare(strict_types=1);

namespace Drupal\ps_search\Commands;

use Drupal\ps_search\Hook\SearchAlertCronHooks;
use Drupal\ps_search\Service\SearchAlertMatcher;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for search alert processing.
 */
final class SearchAlertCommands extends DrushCommands {

  public function __construct(
    private readonly SearchAlertCronHooks $cronHooks,
    private readonly SearchAlertMatcher $matcher,
  ) {
    parent::__construct();
  }

  /**
   * Processes due search alerts and sends digest emails.
   */
  #[CLI\Command(name: 'ps:search:alerts:process', aliases: ['ps-sap'])]
  #[CLI\Option(name: 'purge', description: 'Purge expired anonymous alerts after processing (1/0).')]
  #[CLI\Usage(name: 'drush ps:search:alerts:process', description: 'Process due search alerts and send digests.')]
  public function process(array $options = ['purge' => 1]): void {
    $sent = $this->cronHooks->processAlerts();
    $purged = (int) ($options['purge'] ?? 1) === 1 ? $this->matcher->purgeExpiredAnonymous() : 0;

    $this->io()->success(sprintf('Search alerts processed (sent=%d, purged=%d).', $sent, $purged));
  }

}
