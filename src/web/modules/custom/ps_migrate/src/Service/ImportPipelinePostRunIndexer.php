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

  /**
   * Default Search API index used for offer post-run indexing.
   */
  public const DEFAULT_INDEX_ID = 'offers';

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
   * Returns the configured Search API index ID for post-run indexing.
   */
  public function getIndexId(): string {
    $indexId = trim((string) $this->configFactory
      ->get('ps_migrate.import_pipeline_settings')
      ->get('post_run_search_api_index'));

    return $indexId !== '' ? $indexId : self::DEFAULT_INDEX_ID;
  }

  /**
   * Indexes pending items in the configured Search API index.
   *
   * @return array<string, mixed>
   *   Stats payload (enabled, indexed, index_id, error).
   */
  public function indexOffers(): array {
    if (!$this->isEnabled()) {
      return [
        'enabled' => FALSE,
        'indexed' => 0,
      ];
    }

    $indexId = $this->getIndexId();

    if (!class_exists(Index::class)) {
      $this->logger->warning('Post-run Solr indexing skipped: Search API is unavailable.');
      return [
        'enabled' => TRUE,
        'index_id' => $indexId,
        'indexed' => 0,
        'error' => 'search_api_unavailable',
      ];
    }

    $index = Index::load($indexId);
    if ($index === NULL) {
      $this->logger->warning('Post-run Solr indexing skipped: Search API index @index is missing.', [
        '@index' => $indexId,
      ]);
      return [
        'enabled' => TRUE,
        'index_id' => $indexId,
        'indexed' => 0,
        'error' => 'search_api_index_missing',
      ];
    }

    try {
      $indexed = (int) $index->indexItems();
      $this->logger->info('Post-run Solr indexing completed on @index: @count item(s) indexed.', [
        '@index' => $indexId,
        '@count' => $indexed,
      ]);
      return [
        'enabled' => TRUE,
        'index_id' => $indexId,
        'indexed' => $indexed,
      ];
    }
    catch (\Throwable $exception) {
      $this->logger->error('Post-run Solr indexing failed on @index: @message', [
        '@index' => $indexId,
        '@message' => $exception->getMessage(),
      ]);
      return [
        'enabled' => TRUE,
        'index_id' => $indexId,
        'indexed' => 0,
        'error' => $exception->getMessage(),
      ];
    }
  }

}
