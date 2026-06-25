<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\search_api\Entity\Index;
use Psr\Log\LoggerInterface;

/**
 * Runs optional Search API Solr indexing after a successful CRM import.
 */
final class ImportPipelinePostRunIndexer {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Whether post-run Solr indexing is enabled in pipeline settings.
   */
  public function isEnabled(): bool {
    return (bool) $this->configFactory
      ->get('ps_migrate.import_pipeline_settings')
      ->get('post_run_index_solr');
  }

  /**
   * Indexes pending offer items in the Search API offers index.
   *
   * @return array<string, mixed>
   *   Stats payload (enabled, indexed, error).
   */
  public function indexOffers(): array {
    if (!$this->isEnabled()) {
      return [
        'enabled' => FALSE,
        'indexed' => 0,
      ];
    }

    if (!class_exists(Index::class)) {
      $this->logger->warning('Post-run Solr indexing skipped: Search API is unavailable.');
      return [
        'enabled' => TRUE,
        'indexed' => 0,
        'error' => 'search_api_unavailable',
      ];
    }

    $index = Index::load('offers');
    if ($index === NULL) {
      $this->logger->warning('Post-run Solr indexing skipped: offers index is missing.');
      return [
        'enabled' => TRUE,
        'indexed' => 0,
        'error' => 'offers_index_missing',
      ];
    }

    try {
      $indexed = (int) $index->indexItems();
      $this->logger->info('Post-run Solr indexing completed: @count item(s) indexed.', [
        '@count' => $indexed,
      ]);
      return [
        'enabled' => TRUE,
        'indexed' => $indexed,
      ];
    }
    catch (\Throwable $exception) {
      $this->logger->error('Post-run Solr indexing failed: @message', [
        '@message' => $exception->getMessage(),
      ]);
      return [
        'enabled' => TRUE,
        'indexed' => 0,
        'error' => $exception->getMessage(),
      ];
    }
  }

}
