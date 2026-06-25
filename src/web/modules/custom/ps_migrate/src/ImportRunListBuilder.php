<?php

declare(strict_types=1);

namespace Drupal\ps_migrate;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Link;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Url;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Drupal\ps_migrate\Service\ImportPipeline;
use Drupal\ps_migrate\Service\ImportPipelineLock;
use Drupal\ps_migrate\Service\ImportPipelineLockStrategy;
use Drupal\ps_migrate\Service\ImportPipelinePathResolver;
use Drupal\ps_migrate\Service\ImportPipelineRollbackService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * List builder for CRM import runs.
 */
final class ImportRunListBuilder extends EntityListBuilder {

  public function __construct(
    EntityTypeInterface $entity_type,
    EntityStorageInterface $storage,
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly QueueFactory $queueFactory,
    private readonly ImportPipelineLock $pipelineLock,
    private readonly ImportPipelineLockStrategy $lockStrategy,
    private readonly ImportPipelineRollbackService $rollbackService,
  ) {
    parent::__construct($entity_type, $storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type): static {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('ps_migrate.import_pipeline_path_resolver'),
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('queue'),
      $container->get('ps_migrate.import_pipeline_lock'),
      $container->get('ps_migrate.import_pipeline_lock_strategy'),
      $container->get('ps_migrate.import_pipeline_rollback'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity, ?CacheableMetadata $cacheability = NULL): array {
    $cacheability ??= new CacheableMetadata();
    $operations = parent::getOperations($entity, $cacheability);
    unset($operations['clone']);
    if (isset($operations['view'])) {
      $operations['view']['weight'] = 0;
    }
    if ($entity instanceof ImportRunInterface && $this->rollbackService->canRollback($entity)) {
      $operations['rollback'] = [
        'title' => $this->t('Roll back'),
        'weight' => 5,
        'url' => Url::fromRoute('ps_migrate.import_run_rollback', ['import_run' => $entity->id()]),
      ];
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    $build['#empty'] = $this->t('No import runs yet.');

    $build['help'] = $this->buildHelp();
    $build['help']['#weight'] = -100;

    if (isset($build['table'])) {
      $build['table']['#weight'] = 0;
    }

    $build['#attached']['library'][] = 'ps_migrate/import_runs_admin';

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader(): array {
    return [
      'filename' => $this->t('File'),
      'pipeline_status' => $this->t('Status'),
      'import_mode' => $this->t('Mode'),
      'started' => $this->t('Started'),
      'finished' => $this->t('Finished'),
      'duration' => $this->t('Duration'),
    ] + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity): array {
    assert($entity instanceof ImportRunInterface);
    /** @var \Drupal\ps_migrate\Entity\ImportRun $entity */

    $started = (int) $entity->get('started')->value;
    $finished = (int) $entity->get('finished')->value;

    $row['filename'] = Link::fromTextAndUrl(
      $entity->getFilename(),
      Url::fromRoute('entity.import_run.canonical', ['import_run' => $entity->id()])
    );
    $row['pipeline_status'] = [
      'data' => [
        '#markup' => '<span class="ps-migrate-import-runs__status ps-migrate-import-runs__status--' . htmlspecialchars($entity->getPipelineStatus(), ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($entity->getPipelineStatus(), ENT_QUOTES, 'UTF-8') . '</span>',
      ],
    ];
    $row['import_mode'] = $entity->getImportMode() === ImportRunInterface::MODE_DELTA
      ? $this->t('Delta')
      : $this->t('Full');
    $row['started'] = $started > 0 ? $this->dateFormatter->format($started, 'short') : $this->t('N/A');
    $row['finished'] = $finished > 0 ? $this->dateFormatter->format($finished, 'short') : $this->t('N/A');
    $row['duration'] = $this->formatDurationCell($entity, $started, $finished);
    return $row + parent::buildRow($entity);
  }

  /**
   * Builds contextual help for the import runs listing.
   */
  private function buildHelp(): array {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    $pendingCount = count($this->pathResolver->listIncomingXmlFiles());
    $queueDepth = $this->queueFactory->get(ImportPipeline::QUEUE_NAME)->numberOfItems();
    $failedCount = (int) $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->condition('pipeline_status', ImportRunInterface::STATUS_FAILED)
      ->count()
      ->execute();
    $totalRuns = (int) $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->count()
      ->execute();
    $lastRun = $this->loadLastRun();
    $cronEnabled = (bool) $config->get('cron_enabled');
    $queueEnabled = (bool) $config->get('queue_enabled');
    $postRunSolr = (bool) $config->get('post_run_index_solr');
    $lockActive = $this->pipelineLock->isLocked();
    $lockStrategy = $this->lockStrategy->getDefaultStrategy();

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

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-migrate-import-runs__help']],
      'intro' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['ps-migrate-import-runs__intro']],
        'lead' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Track CRM XML imports processed by the pipeline. External systems deposit files in the incoming folder; each run copies the XML to staging, executes Migrate, then archives or moves failures.'),
        ],
      ],
      'stats' => [
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
          $queueEnabled
            ? ($lockActive ? $this->t('Worker lock active') : $this->t('Async processing enabled'))
            : $this->t('Sync mode (queue disabled)'),
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
          $this->t('History below'),
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
            ? $this->t('Offers index after success')
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
      ],
      'workflow' => [
        '#type' => 'details',
        '#title' => $this->t('How the pipeline works'),
        '#open' => FALSE,
        'steps' => [
          '#theme' => 'item_list',
          '#items' => [
            $this->t('<strong>1. Deposit XML</strong> — CRM drops <code>.xml</code> files in @incoming, or upload via @upload.', [
              '@incoming' => $config->get('paths.incoming') ?: 'incoming/',
              '@upload' => Link::fromTextAndUrl($this->t('Upload CRM XML'), Url::fromRoute('ps_migrate.import_upload'))->toString(),
            ]),
            $this->t('<strong>2. Pick up</strong> — Drush, cron (if enabled), or immediate processing moves the file to processing/, then stages it at @staging.', [
              '@staging' => $config->get('staging_uri') ?: 'public://crm/offers.xml',
            ]),
            $this->t('<strong>3. Migrate</strong> — Full mode runs all CRM migrations; delta mode updates offers and translations only.'),
            $this->t('<strong>4. Archive</strong> — Successful files go to archive/; failures go to failed/ with details in the run record. Check @rejections for publication rejections.', [
              '@rejections' => Link::fromTextAndUrl($this->t('Import rejections'), Url::fromRoute('ps_migrate.post_import_report'))->toString(),
            ]),
          ],
        ],
      ],
      'commands' => [
        '#type' => 'details',
        '#title' => $this->t('Run manually (Drush)'),
        '#open' => FALSE,
        'intro' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Process pending files from the command line:'),
        ],
        'cli' => [
          '#type' => 'html_tag',
          '#tag' => 'code',
          '#value' => "drush ps:import:enqueue\ndrush ps:import:queue-process --count=1\ndrush ps:import:queue-status",
        ],
        'settings' => [
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => $this->t('Configure folders, batch limit and cron in @settings.', [
            '@settings' => Link::fromTextAndUrl($this->t('Pipeline settings'), Url::fromRoute('ps_migrate.import_pipeline_settings'))->toString(),
          ]),
        ],
      ],
      '#cache' => [
        'tags' => $config->getCacheTags(),
        'contexts' => ['user.permissions'],
      ],
    ];
  }

  /**
   * Builds a summary stat card render element.
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
   * Formats duration for a list table cell.
   */
  private function formatDurationCell(ImportRunInterface $entity, int $started, int $finished): string {
    $durationMs = $entity->getDurationMs();
    if ($durationMs > 0) {
      $interval = (string) $this->dateFormatter->formatInterval((int) round($durationMs / 1000));
      $stats = $entity->getStats();
      if (!empty($stats['sla_breached'])) {
        return $interval . ' (!)';
      }
      return $interval;
    }
    if ($started > 0 && $finished > $started) {
      return (string) $this->dateFormatter->formatInterval($finished - $started);
    }
    return (string) $this->t('N/A');
  }

  /**
   * Loads the most recently finished import run, if any.
   */
  private function loadLastRun(): ?ImportRunInterface {
    $ids = $this->getStorage()->getQuery()
      ->accessCheck(TRUE)
      ->sort('started', 'DESC')
      ->range(0, 1)
      ->execute();
    if ($ids === []) {
      return NULL;
    }
    $entity = $this->getStorage()->load(reset($ids));
    return $entity instanceof ImportRunInterface ? $entity : NULL;
  }

}
