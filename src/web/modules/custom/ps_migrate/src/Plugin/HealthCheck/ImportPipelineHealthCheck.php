<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Plugin\HealthCheck;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;
use Drupal\ps_core\HealthCheck\HealthCheckStatus;
use Drupal\ps_core\Plugin\HealthCheck\HealthCheckBase;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Drupal\ps_migrate\Service\ImportPipeline;
use Drupal\ps_migrate\Service\ImportPipelinePathResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks CRM import pipeline queue, locks and recent run activity.
 *
 * @HealthCheck(
 *   id = "import_pipeline",
 *   label = @Translation("CRM import pipeline"),
 *   group = "import",
 *   weight = 0,
 * )
 */
final class ImportPipelineHealthCheck extends HealthCheckBase implements ContainerFactoryPluginInterface {

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly ImportPipeline $importPipeline,
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FileSystemInterface $fileSystem,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('ps_migrate.import_pipeline'),
      $container->get('ps_migrate.import_pipeline_path_resolver'),
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('file_system'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function run(): HealthCheckResult {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    $queueEnabled = (bool) $config->get('queue_enabled');
    $cronEnabled = (bool) $config->get('cron_enabled');
    $queueStatus = $this->importPipeline->getQueueStatus();
    $queueDepth = (int) ($queueStatus['queue_depth'] ?? 0);
    $lockStale = !empty($queueStatus['lock_stale']);
    $lockActive = !empty($queueStatus['lock_active']);
    $pendingFiles = $this->countIncomingXmlFiles();
    $failedRuns = $this->countRunsByStatus(ImportRunInterface::STATUS_FAILED);

    $status = HealthCheckStatus::OK;
    $issues = [];

    if ($lockStale) {
      $status = HealthCheckStatus::FAIL;
      $issues[] = (string) $this->t('Worker lock is stale.');
    }
    elseif ($lockActive && $queueDepth > 0) {
      $status = HealthCheckStatus::WARNING;
      $issues[] = (string) $this->t('Worker lock active with @depth queued item(s).', [
        '@depth' => $queueDepth,
      ]);
    }

    if ($pendingFiles > 0 && !$queueEnabled) {
      $status = $this->escalate($status, HealthCheckStatus::WARNING);
      $issues[] = (string) $this->t('@count XML file(s) waiting in incoming/ but async queue is disabled.', [
        '@count' => $pendingFiles,
      ]);
    }

    if ($failedRuns > 0) {
      $status = $this->escalate($status, HealthCheckStatus::WARNING);
      $issues[] = (string) $this->t('@count failed import run(s) recorded.', [
        '@count' => $failedRuns,
      ]);
    }

    if (!$cronEnabled && $queueEnabled) {
      $status = $this->escalate($status, HealthCheckStatus::WARNING);
      $issues[] = (string) $this->t('Pipeline cron is disabled while async queue is enabled.');
    }

    $message = $issues === []
      ? (string) $this->t('Pipeline idle — @pending pending file(s), queue depth @depth.', [
        '@pending' => $pendingFiles,
        '@depth' => $queueDepth,
      ])
      : implode(' ', $issues);

    $detail = (string) $this->t('Cron @cron · Queue @queue · Lock @lock.', [
      '@cron' => $cronEnabled ? 'on' : 'off',
      '@queue' => $queueEnabled ? 'on' : 'off',
      '@lock' => $lockActive ? ($lockStale ? 'stale' : 'active') : 'free',
    ]);

    return new HealthCheckResult(
      $status,
      $message,
      [
        [
          'title' => (string) $this->t('Import hub'),
          'route' => 'ps_migrate.admin_overview',
        ],
        [
          'title' => (string) $this->t('Import runs'),
          'route' => 'entity.import_run.collection',
        ],
        [
          'title' => (string) $this->t('Pipeline settings'),
          'route' => 'ps_migrate.import_pipeline_settings',
        ],
      ],
      [
        'cd src && vendor/bin/drush @ps.fr ps:import:queue-status',
        'cd src && vendor/bin/drush @ps.fr ps:import:recover-stale',
        'make drush fr core:cron',
      ],
      $detail,
    );
  }

  private function countIncomingXmlFiles(): int {
    try {
      $incoming = $this->pathResolver->getPath('incoming');
      $realpath = $this->fileSystem->realpath($incoming);
      if ($realpath === FALSE || !is_dir($realpath)) {
        return 0;
      }
      return count(glob($realpath . '/*.xml') ?: []);
    }
    catch (\Throwable) {
      return 0;
    }
  }

  private function countRunsByStatus(string $pipelineStatus): int {
    try {
      return (int) $this->entityTypeManager->getStorage('import_run')->getQuery()
        ->accessCheck(FALSE)
        ->condition('pipeline_status', $pipelineStatus)
        ->count()
        ->execute();
    }
    catch (\Throwable) {
      return 0;
    }
  }

  private function escalate(string $current, string $candidate): string {
    $order = [
      HealthCheckStatus::OK => 0,
      HealthCheckStatus::INFO => 1,
      HealthCheckStatus::WARNING => 2,
      HealthCheckStatus::FAIL => 3,
    ];
    return ($order[$candidate] ?? 0) > ($order[$current] ?? 0) ? $candidate : $current;
  }

}
