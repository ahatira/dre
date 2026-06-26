<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\ps_migrate\Service\ImportPipelinePostRunIndexer;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use Psr\Log\LoggerInterface;

/**
 * Tests Search API post-run indexer configuration.
 */
#[Group('ps_migrate')]
final class ImportPipelinePostRunIndexerTest extends UnitTestCase {

  /**
   * Ensures the default offers index ID is used when config is empty.
   */
  public function testDefaultIndexIdWhenConfigEmpty(): void {
    $indexer = new ImportPipelinePostRunIndexer(
      $this->createConfigFactory(''),
      $this->createMock(LoggerInterface::class),
    );

    self::assertSame('offers', $indexer->getIndexId());
  }

  /**
   * Ensures a configured Search API index ID is returned.
   */
  public function testConfiguredIndexId(): void {
    $indexer = new ImportPipelinePostRunIndexer(
      $this->createConfigFactory('custom_offers'),
      $this->createMock(LoggerInterface::class),
    );

    self::assertSame('custom_offers', $indexer->getIndexId());
  }

  /**
   * Ensures post-run indexing can be disabled via config.
   */
  public function testIndexingDisabledReturnsEarlyPayload(): void {
    $indexer = new ImportPipelinePostRunIndexer(
      $this->createConfigFactory('offers', enabled: FALSE),
      $this->createMock(LoggerInterface::class),
    );

    self::assertFalse($indexer->isEnabled());
    self::assertSame(['enabled' => FALSE, 'indexed' => 0], $indexer->indexOffers());
  }

  /**
   * Builds a config factory mock for pipeline settings.
   */
  private function createConfigFactory(string $indexId, bool $enabled = TRUE): ConfigFactoryInterface {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['post_run_index_solr', $enabled],
      ['post_run_search_api_index', $indexId],
    ]);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_migrate.import_pipeline_settings')->willReturn($config);

    return $configFactory;
  }

}
