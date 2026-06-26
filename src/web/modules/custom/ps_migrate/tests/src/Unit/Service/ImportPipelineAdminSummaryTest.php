<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\ps_migrate\Service\ImportPipeline;
use Drupal\ps_migrate\Service\ImportPipelineAdminSummary;
use Drupal\ps_migrate\Service\ImportPipelineLock;
use Drupal\ps_migrate\Service\ImportPipelineLockStrategy;
use Drupal\ps_migrate\Service\ImportPipelinePathResolver;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_migrate\Service\ImportPipelineAdminSummary
 */
#[CoversClass(ImportPipelineAdminSummary::class)]
#[Group('ps_migrate')]
final class ImportPipelineAdminSummaryTest extends UnitTestCase {

  /**
   * @covers ::buildPipelineFlowRenderArray
   */
  public function testBuildPipelineFlowRenderArrayIncludesConfiguredUris(): void {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')
      ->willReturnMap([
        ['paths.incoming', ImportPipelineAdminSummary::DEFAULT_PATH_INCOMING],
        ['paths.processing', ImportPipelineAdminSummary::DEFAULT_PATH_PROCESSING],
        ['paths.archive', ImportPipelineAdminSummary::DEFAULT_PATH_ARCHIVE],
        ['paths.failed', ImportPipelineAdminSummary::DEFAULT_PATH_FAILED],
        ['staging_uri', ImportPipelineAdminSummary::DEFAULT_STAGING_URI],
        ['queue_enabled', TRUE],
        ['cron_enabled', FALSE],
        ['post_run_index_solr', TRUE],
      ]);

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_migrate.import_pipeline_settings')->willReturn($config);

    $pathResolver = $this->createMock(ImportPipelinePathResolver::class);
    $importPipeline = $this->createMock(ImportPipeline::class);
    $importPipeline->method('getQueueStatus')->willReturn(['queue_depth' => 0, 'lock_stale' => FALSE]);

    $query = $this->createMock(QueryInterface::class);
    $query->method('accessCheck')->willReturnSelf();
    $query->method('condition')->willReturnSelf();
    $query->method('sort')->willReturnSelf();
    $query->method('range')->willReturnSelf();
    $query->method('count')->willReturnSelf();
    $query->method('execute')->willReturn([]);

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('getQuery')->willReturn($query);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('import_run')->willReturn($storage);

    $summary = new ImportPipelineAdminSummary(
      $pathResolver,
      $configFactory,
      $this->createMock(DateFormatterInterface::class),
      $this->createMock(ImportPipelineLock::class),
      $this->createMock(ImportPipelineLockStrategy::class),
      $importPipeline,
      $entityTypeManager,
    );

    $build = $summary->buildPipelineFlowRenderArray();

    self::assertSame('details', $build['#type']);
    self::assertArrayHasKey('diagram', $build);
    self::assertArrayHasKey('main_0', $build['diagram']);
    self::assertSame(
      'deposit',
      $build['diagram']['main_0']['#attributes']['data-flow-step'],
    );
    self::assertSame(
      ImportPipelineAdminSummary::DEFAULT_PATH_INCOMING,
      $build['diagram']['main_0']['uri']['#value'],
    );
    self::assertArrayHasKey('outcome', $build['diagram']);

    $stagingUri = NULL;
    foreach ($build['diagram'] as $key => $element) {
      if (!is_string($key) || !str_starts_with($key, 'main_')) {
        continue;
      }
      if (($element['#attributes']['data-flow-step'] ?? '') === 'staging') {
        $stagingUri = $element['uri']['#value'] ?? NULL;
        break;
      }
    }
    self::assertSame(ImportPipelineAdminSummary::DEFAULT_STAGING_URI, $stagingUri);
    self::assertSame(
      ImportPipelineAdminSummary::DEFAULT_PATH_ARCHIVE,
      $build['diagram']['outcome']['branches']['success']['lane']['lane_0']['uri']['#value'],
    );
    self::assertSame(
      ImportPipelineAdminSummary::DEFAULT_PATH_FAILED,
      $build['diagram']['outcome']['branches']['failure']['lane']['lane_0']['uri']['#value'],
    );
  }

}
