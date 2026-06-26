<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_core\Service\AdminFlowchartBuilder;
use Drupal\ps_migrate\Entity\ImportRunInterface;

/**
 * Builds dashboard summaries for CRM import admin pages.
 */
final class ImportPipelineAdminSummary {

  use StringTranslationTrait;

  /**
   * Default pipeline folder and staging URIs (install config + migration CMI placeholder).
   */
  public const DEFAULT_PATH_INCOMING = 'private://import/incoming';

  public const DEFAULT_PATH_PROCESSING = 'private://import/processing';

  public const DEFAULT_PATH_ARCHIVE = 'private://import/archive';

  public const DEFAULT_PATH_FAILED = 'private://import/failed';

  public const DEFAULT_STAGING_URI = 'private://import/staging/offers.xml';

  /**
   * @deprecated Use DEFAULT_STAGING_URI. Kept for settings form CMI parity check.
   */
  public const DEFAULT_CMI_STAGING_URI = self::DEFAULT_STAGING_URI;

  public function __construct(
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly ImportPipelineLock $pipelineLock,
    private readonly ImportPipelineLockStrategy $lockStrategy,
    private readonly ImportPipeline $importPipeline,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly AdminFlowchartBuilder $flowchartBuilder,
  ) {}

  /**
   * Returns import pipeline settings config.
   */
  public function getPipelineConfig(): ImmutableConfig {
    return $this->configFactory->get('ps_migrate.import_pipeline_settings');
  }

  /**
   * Loads the most recently started import run, if any.
   */
  public function loadLastRun(): ?ImportRunInterface {
    $ids = $this->getImportRunStorage()->getQuery()
      ->accessCheck(TRUE)
      ->sort('started', 'DESC')
      ->range(0, 1)
      ->execute();
    if ($ids === []) {
      return NULL;
    }

    $entity = $this->getImportRunStorage()->load(reset($ids));
    return $entity instanceof ImportRunInterface ? $entity : NULL;
  }

  /**
   * Builds stat cards for pipeline health and recent activity.
   *
   * @return array<string, mixed>
   *   Render array for the stats grid.
   */
  public function buildStatsRenderArray(): array {
    $config = $this->getPipelineConfig();
    $pendingCount = count($this->pathResolver->listIncomingXmlFiles());
    $queueStatus = $this->importPipeline->getQueueStatus();
    $queueDepth = (int) ($queueStatus['queue_depth'] ?? 0);
    $failedCount = (int) $this->getImportRunStorage()->getQuery()
      ->accessCheck(TRUE)
      ->condition('pipeline_status', ImportRunInterface::STATUS_FAILED)
      ->count()
      ->execute();
    $totalRuns = (int) $this->getImportRunStorage()->getQuery()
      ->accessCheck(TRUE)
      ->count()
      ->execute();
    $lastRun = $this->loadLastRun();
    $cronEnabled = (bool) $config->get('cron_enabled');
    $queueEnabled = (bool) $config->get('queue_enabled');
    $postRunSolr = (bool) $config->get('post_run_index_solr');
    $lockActive = $this->pipelineLock->isLocked();
    $lockStrategy = $this->lockStrategy->getDefaultStrategy();
    $lockStale = !empty($queueStatus['lock_stale']);

    $lastRunMeta = $this->t('No runs recorded yet.');
    if ($lastRun instanceof ImportRunInterface) {
      $finished = (int) $lastRun->get('finished')->value;
      $stats = $lastRun->getStats();
      $slaSuffix = !empty($stats['sla_breached']) ? ' — SLA breached' : '';
      $lastRunMeta = $this->t('@status — @file (@time)@sla', [
        '@status' => $lastRun->getPipelineStatus(),
        '@file' => $lastRun->getFilename(),
        '@time' => $finished > 0 ? $this->dateFormatter->format($finished, 'short') : $this->t('in progress'),
        '@sla' => $slaSuffix,
      ]);
    }

    $queueMeta = $queueEnabled
      ? ($lockActive
        ? ($lockStale ? $this->t('Worker lock stale — recover if stuck') : $this->t('Worker lock active'))
        : $this->t('Async processing enabled'))
      : $this->t('Sync mode (queue disabled)');

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-runs__stats']],
      'pending' => $this->buildStatCard(
        (string) $pendingCount,
        (string) $this->t('Pending in incoming'),
        $pendingCount > 0
          ? $this->t('Waiting for pipeline')
          : $this->t('Folder empty'),
      ),
      'queue' => $this->buildStatCard(
        (string) $queueDepth,
        (string) $this->t('Queue depth'),
        $queueMeta,
      ),
      'failed' => $this->buildStatCard(
        (string) $failedCount,
        (string) $this->t('Failed runs'),
        $failedCount > 0
          ? $this->t('Review run details')
          : $this->t('No failures recorded'),
      ),
      'runs' => $this->buildStatCard(
        (string) $totalRuns,
        (string) $this->t('Recorded runs'),
        $this->t('Full history on Import runs tab'),
      ),
      'cron' => $this->buildStatCard(
        $cronEnabled ? (string) $this->t('On') : (string) $this->t('Off'),
        (string) $this->t('Cron polling'),
        $cronEnabled
          ? $this->t('Automatic pickup enabled')
          : $this->t('Manual or Drush only'),
      ),
      'solr' => $this->buildStatCard(
        $postRunSolr ? (string) $this->t('On') : (string) $this->t('Off'),
        (string) $this->t('Post-run Solr'),
        $postRunSolr
          ? $this->t('Index @index after success', [
            '@index' => $config->get('post_run_search_api_index') ?: 'offers',
          ])
          : $this->t('Manual index required'),
      ),
      'lock' => $this->buildStatCard(
        $lockStrategy,
        (string) $this->t('Lock strategy'),
        $lockActive
          ? $this->t('Import currently locked')
          : $this->t('No active import lock'),
      ),
      'last' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-migrate-import-runs__stat']],
        'value' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $lastRun instanceof ImportRunInterface ? $lastRun->getPipelineStatus() : '—',
          '#attributes' => ['class' => ['ps-migrate-import-runs__stat-value']],
        ],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $this->t('Last run'),
          '#attributes' => ['class' => ['ps-migrate-import-runs__stat-label']],
        ],
        'meta' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $lastRunMeta,
          '#attributes' => ['class' => ['ps-migrate-import-runs__stat-meta']],
        ],
      ],
    ];
  }

  /**
   * Builds a pipeline flow diagram for the import overview page.
   *
   * @return array<string, mixed>
   *   Render array for the flow section.
   */
  public function buildPipelineFlowRenderArray(): array {
    $config = $this->getPipelineConfig();
    $queueEnabled = (bool) $config->get('queue_enabled');
    $cronEnabled = (bool) $config->get('cron_enabled');
    $postRunSolr = (bool) $config->get('post_run_index_solr');

    $mainLane = [
      $this->flowchartBuilder->buildNode(
        'deposit',
        (string) $this->t('Deposit'),
        (string) $this->t('External CRM (SFTP) or BO upload drops one XML file.'),
        (string) $config->get('paths.incoming'),
      ),
    ];

    if ($queueEnabled) {
      $mainLane[] = $this->flowchartBuilder->buildConnector(TRUE);
      $mainLane[] = $this->flowchartBuilder->buildNode(
        'queue',
        (string) $this->t('Queue'),
        (string) $this->t('Async job per file (Drush queue or cron).'),
        NULL,
        ['optional' => TRUE],
      );
    }

    $mainLane[] = $this->flowchartBuilder->buildConnector();
    $mainLane[] = $this->flowchartBuilder->buildNode(
      'pickup',
      (string) $this->t('Pickup'),
      $cronEnabled
        ? (string) $this->t('Drush sync, cron polling, or queue worker starts one run.')
        : (string) $this->t('Drush sync or manual run starts one import.'),
    );
    $mainLane[] = $this->flowchartBuilder->buildConnector();
    $mainLane[] = $this->flowchartBuilder->buildNode(
      'processing',
      (string) $this->t('Processing'),
      (string) $this->t('File moved here with its original name while the run is active.'),
      (string) $config->get('paths.processing'),
    );
    $mainLane[] = $this->flowchartBuilder->buildConnector();
    $mainLane[] = $this->flowchartBuilder->buildNode(
      'staging',
      (string) $this->t('Staging'),
      (string) $this->t('Fixed copy for Migrate (same path every run; not the deposit folder).'),
      (string) $config->get('staging_uri'),
    );
    $mainLane[] = $this->flowchartBuilder->buildConnector();
    $mainLane[] = $this->flowchartBuilder->buildNode(
      'migrate',
      (string) $this->t('Migrate'),
      (string) $this->t('Ordered CRM migrations (agents, features, media, offers…).'),
    );

    $successBranch = [
      $this->flowchartBuilder->buildNode(
        'archive',
        (string) $this->t('Archive'),
        (string) $this->t('Successful run — file kept for audit.'),
        (string) $config->get('paths.archive'),
        ['variant' => 'success'],
      ),
    ];
    if ($postRunSolr) {
      $successBranch[] = $this->flowchartBuilder->buildConnector(TRUE);
      $successBranch[] = $this->flowchartBuilder->buildNode(
        'postrun',
        (string) $this->t('Post-run'),
        (string) $this->t('Search index, alerts, import_run stats and snapshot.'),
        NULL,
        ['optional' => TRUE],
      );
    }

    $diagramChildren = [];
    foreach ($mainLane as $index => $element) {
      $diagramChildren['main_' . $index] = $element;
    }

    $diagramChildren['outcome'] = $this->flowchartBuilder->buildFork(
      (string) $this->t('Outcome'),
      [
        'success' => [
          'label' => (string) $this->t('Success'),
          'lane' => $successBranch,
        ],
        'failure' => [
          'label' => (string) $this->t('Failure'),
          'lane' => [
            $this->flowchartBuilder->buildNode(
              'failed',
              (string) $this->t('Failed'),
              (string) $this->t('Run stopped — file kept for investigation.'),
              (string) $config->get('paths.failed'),
              ['variant' => 'failure'],
            ),
          ],
        ],
      ],
    );

    return $this->flowchartBuilder->buildDetailsSection(
      (string) $this->t('How the import pipeline works'),
      (string) $this->t('One XML file per run. Processing keeps the original filename; staging is a stable copy read by Migrate.'),
      $diagramChildren,
      (string) $this->t('Import pipeline flowchart from deposit to archive or failed folder.'),
      'ps-migrate-import-admin__flow',
    );
  }

  /**
   * Whether configured staging URI differs from shipped migration CMI default.
   */
  public function stagingUriDiffersFromCmiDefault(): bool {
    $configured = trim((string) $this->getPipelineConfig()->get('staging_uri'));
    return $configured !== '' && $configured !== self::DEFAULT_CMI_STAGING_URI;
  }

  /**
   * Builds one dashboard stat card render element.
   *
   * @return array<string, mixed>
   *   Stat card render array.
   */
  private function buildStatCard(string $value, string $label, \Stringable|string $meta): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-runs__stat']],
      'value' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $value,
        '#attributes' => ['class' => ['ps-migrate-import-runs__stat-value']],
      ],
      'label' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $label,
        '#attributes' => ['class' => ['ps-migrate-import-runs__stat-label']],
      ],
      'meta' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $meta,
        '#attributes' => ['class' => ['ps-migrate-import-runs__stat-meta']],
      ],
    ];
  }

  /**
   * Returns the import_run entity storage.
   */
  private function getImportRunStorage(): EntityStorageInterface {
    return $this->entityTypeManager->getStorage('import_run');
  }

}
