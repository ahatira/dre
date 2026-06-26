<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
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
      $this->buildFlowchartNode(
        'deposit',
        (string) $this->t('Deposit'),
        (string) $this->t('External CRM (SFTP) or BO upload drops one XML file.'),
        (string) $config->get('paths.incoming'),
      ),
    ];

    if ($queueEnabled) {
      $mainLane[] = $this->buildFlowchartConnector(TRUE);
      $mainLane[] = $this->buildFlowchartNode(
        'queue',
        (string) $this->t('Queue'),
        (string) $this->t('Async job per file (Drush queue or cron).'),
        NULL,
        ['optional' => TRUE],
      );
    }

    $mainLane[] = $this->buildFlowchartConnector();
    $mainLane[] = $this->buildFlowchartNode(
      'pickup',
      (string) $this->t('Pickup'),
      $cronEnabled
        ? (string) $this->t('Drush sync, cron polling, or queue worker starts one run.')
        : (string) $this->t('Drush sync or manual run starts one import.'),
    );
    $mainLane[] = $this->buildFlowchartConnector();
    $mainLane[] = $this->buildFlowchartNode(
      'processing',
      (string) $this->t('Processing'),
      (string) $this->t('File moved here with its original name while the run is active.'),
      (string) $config->get('paths.processing'),
    );
    $mainLane[] = $this->buildFlowchartConnector();
    $mainLane[] = $this->buildFlowchartNode(
      'staging',
      (string) $this->t('Staging'),
      (string) $this->t('Fixed copy for Migrate (same path every run; not the deposit folder).'),
      (string) $config->get('staging_uri'),
    );
    $mainLane[] = $this->buildFlowchartConnector();
    $mainLane[] = $this->buildFlowchartNode(
      'migrate',
      (string) $this->t('Migrate'),
      (string) $this->t('Ordered CRM migrations (agents, features, media, offers…).'),
    );

    $successBranch = [
      $this->buildFlowchartNode(
        'archive',
        (string) $this->t('Archive'),
        (string) $this->t('Successful run — file kept for audit.'),
        (string) $config->get('paths.archive'),
        ['variant' => 'success'],
      ),
    ];
    if ($postRunSolr) {
      $successBranch[] = $this->buildFlowchartConnector(TRUE);
      $successBranch[] = $this->buildFlowchartNode(
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

    $diagramChildren['outcome'] = $this->buildFlowchartFork(
      (string) $this->t('Outcome'),
      [
        'success' => [
          'label' => (string) $this->t('Success'),
          'lane' => $successBranch,
        ],
        'failure' => [
          'label' => (string) $this->t('Failure'),
          'lane' => [
            $this->buildFlowchartNode(
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

    return [
      '#type' => 'details',
      '#title' => $this->t('How the import pipeline works'),
      '#open' => TRUE,
      '#attributes' => ['class' => ['ps-migrate-import-admin__flow']],
      'intro' => [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('One XML file per run. Processing keeps the original filename; staging is a stable copy read by Migrate.'),
        '#attributes' => ['class' => ['ps-migrate-import-admin__flow-intro']],
      ],
      'diagram' => [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['ps-migrate-import-admin__flowchart'],
          'role' => 'img',
          'aria-label' => (string) $this->t('Import pipeline flowchart from deposit to archive or failed folder.'),
        ],
      ] + $diagramChildren,
    ];
  }

  /**
   * Whether configured staging URI differs from shipped migration CMI default.
   */
  public function stagingUriDiffersFromCmiDefault(): bool {
    $configured = trim((string) $this->getPipelineConfig()->get('staging_uri'));
    return $configured !== '' && $configured !== self::DEFAULT_CMI_STAGING_URI;
  }

  /**
   * Builds one flowchart node.
   *
   * @param array{optional?: bool, variant?: string} $options
   *   Optional styling flags.
   *
   * @return array<string, mixed>
   *   Flowchart node render array.
   */
  private function buildFlowchartNode(
    string $key,
    string $title,
    string $description,
    ?string $uri = NULL,
    array $options = [],
  ): array {
    $classes = ['ps-migrate-import-admin__flowchart-node'];
    if (!empty($options['optional'])) {
      $classes[] = 'ps-migrate-import-admin__flowchart-node--optional';
    }
    if (!empty($options['variant'])) {
      $classes[] = 'ps-migrate-import-admin__flowchart-node--' . $options['variant'];
    }

    $node = [
      '#type' => 'container',
      '#attributes' => [
        'class' => $classes,
        'data-flow-step' => $key,
      ],
      'title' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $title,
        '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-node-title']],
      ],
      'description' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $description,
        '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-node-description']],
      ],
    ];

    if (!empty($options['optional'])) {
      $node['badge'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => $this->t('Optional'),
        '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-node-badge']],
      ];
    }

    if ($uri !== NULL && $uri !== '') {
      $node['uri'] = [
        '#type' => 'html_tag',
        '#tag' => 'code',
        '#value' => $uri,
        '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-node-uri']],
      ];
    }

    return $node;
  }

  /**
   * Builds a vertical connector between flowchart nodes.
   *
   * @return array<string, mixed>
   *   Connector render array.
   */
  private function buildFlowchartConnector(bool $optional = FALSE): array {
    $classes = ['ps-migrate-import-admin__flowchart-connector'];
    if ($optional) {
      $classes[] = 'ps-migrate-import-admin__flowchart-connector--optional';
    }

    return [
      '#type' => 'container',
      '#attributes' => [
        'class' => $classes,
        'aria-hidden' => 'true',
      ],
    ];
  }

  /**
   * Builds a fork after Migrate with success and failure branches.
   *
   * @param array<string, array{label: string, lane: list<array<string, mixed>>}> $branches
   *   Branch metadata keyed by branch id.
   *
   * @return array<string, mixed>
   *   Fork render array.
   */
  private function buildFlowchartFork(string $title, array $branches): array {
    $build = [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-fork']],
      'connector' => $this->buildFlowchartConnector(),
      'hub' => [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $title,
        '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-fork-hub']],
      ],
      'branches' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-fork-branches']],
      ],
    ];

    foreach ($branches as $branchKey => $branch) {
      $laneChildren = [];
      foreach ($branch['lane'] as $index => $element) {
        $laneChildren['lane_' . $index] = $element;
      }

      $build['branches'][$branchKey] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'ps-migrate-import-admin__flowchart-branch',
            'ps-migrate-import-admin__flowchart-branch--' . $branchKey,
          ],
        ],
        'label' => [
          '#type' => 'html_tag',
          '#tag' => 'div',
          '#value' => $branch['label'],
          '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-branch-label']],
        ],
        'lane' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-migrate-import-admin__flowchart-branch-lane']],
        ] + $laneChildren,
      ];
    }

    return $build;
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
