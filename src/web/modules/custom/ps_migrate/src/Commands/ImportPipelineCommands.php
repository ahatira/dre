<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_migrate\Service\ImportPipeline;
use Drupal\ps_migrate\Service\ImportPipelineRollbackService;
use Drush\Attributes as CLI;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for the CRM XML import pipeline.
 */
final class ImportPipelineCommands extends DrushCommands {

  public function __construct(
    private readonly ImportPipeline $importPipeline,
    private readonly ImportPipelineRollbackService $rollbackService,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {
    parent::__construct();
  }

  /**
   * Rolls back a CRM import run using its stored snapshot.
   */
  #[CLI\Command(name: 'ps:import:rollback', aliases: ['ps-import-rollback'])]
  #[CLI\Option(name: 'run-id', description: 'Import run entity ID to roll back.')]
  #[CLI\Option(name: 'force', description: 'Skip newer-run guard (1/0).')]
  #[CLI\Usage(name: 'drush ps:import:rollback --run-id=42', description: 'Roll back import run 42.')]
  public function rollback(array $options = ['run-id' => NULL, 'force' => NULL]): void {
    $runId = (int) ($options['run-id'] ?? 0);
    if ($runId <= 0) {
      throw new \InvalidArgumentException('Missing or invalid --run-id.');
    }

    /** @var \Drupal\ps_migrate\Entity\ImportRunInterface|null $run */
    $run = $this->entityTypeManager->getStorage('import_run')->load($runId);
    if ($run === NULL) {
      throw new \InvalidArgumentException(sprintf('Import run %d not found.', $runId));
    }

    $force = (int) ($options['force'] ?? 0) === 1;
    $summary = $this->rollbackService->rollback($run, $force);

    if (!empty($summary['blocked'])) {
      $this->io()->error('Rollback blocked: a newer successful import run exists. Use --force=1 to override.');
      throw new \RuntimeException('Rollback blocked.');
    }

    foreach ($summary['warnings'] ?? [] as $warning) {
      $this->logger()->warning($warning);
    }
    foreach ($summary['errors'] ?? [] as $error) {
      $this->io()->error($error);
    }

    if (($summary['status'] ?? '') === 'failed') {
      throw new \RuntimeException('Import run rollback failed.');
    }

    $this->io()->success(sprintf(
      'Rollback run %d: unpublished=%d restored=%d skipped_locked=%d',
      $runId,
      $summary['offers_unpublished'] ?? 0,
      $summary['offers_restored'] ?? 0,
      $summary['offers_skipped_locked'] ?? 0,
    ));
  }

  /**
   * Runs the CRM XML import pipeline (enqueue by default, or sync processing).
   */
  #[CLI\Command(name: 'ps:import:run', aliases: ['ps-import'])]
  #[CLI\Option(name: 'limit', description: 'Max XML files to process (0 = config default).')]
  #[CLI\Option(name: 'mode', description: 'Import mode: full or delta.')]
  #[CLI\Option(name: 'seed-sample', description: 'Seed dev sample XML into incoming/ when empty (1/0).')]
  #[CLI\Option(name: 'sync', description: 'Process files synchronously instead of enqueueing (1/0).')]
  #[CLI\Usage(name: 'drush ps:import:run', description: 'Enqueue pending XML files (default).')]
  #[CLI\Usage(name: 'drush ps:import:run --sync=1 --limit=1 --mode=full', description: 'Process one file synchronously.')]
  public function run(array $options = ['limit' => NULL, 'mode' => NULL, 'seed-sample' => NULL, 'sync' => NULL]): void {
    if ((int) ($options['seed-sample'] ?? 0) === 1) {
      $sample = dirname(DRUPAL_ROOT, 2) . '/data/xml/bnppre_sample_100_per_type.xml';
      if ($this->importPipeline->seedSampleIfEmpty($sample)) {
        $this->logger()->notice('Seeded sample XML into incoming/.');
      }
    }

    $limit = isset($options['limit']) && $options['limit'] !== NULL
      ? (int) $options['limit']
      : NULL;
    $mode = isset($options['mode']) && $options['mode'] !== NULL && $options['mode'] !== ''
      ? (string) $options['mode']
      : NULL;
    $sync = (int) ($options['sync'] ?? 0) === 1;

    $summary = $this->importPipeline->run($limit, $mode, $sync);
    $this->renderSummary($summary, $sync);
  }

  /**
   * Scans incoming/ and enqueues new CRM XML files.
   */
  #[CLI\Command(name: 'ps:import:enqueue', aliases: ['ps-import-enqueue'])]
  #[CLI\Option(name: 'limit', description: 'Max XML files to enqueue (0 = config default).')]
  #[CLI\Option(name: 'mode', description: 'Import mode: full or delta.')]
  public function enqueue(array $options = ['limit' => NULL, 'mode' => NULL]): void {
    $limit = isset($options['limit']) && $options['limit'] !== NULL ? (int) $options['limit'] : NULL;
    $mode = isset($options['mode']) && $options['mode'] !== NULL && $options['mode'] !== ''
      ? (string) $options['mode']
      : NULL;

    $summary = $this->importPipeline->enqueueIncoming($limit, $mode);
    $this->renderSummary($summary, FALSE);
  }

  /**
   * Processes one or more items from the CRM import queue.
   */
  #[CLI\Command(name: 'ps:import:queue-process', aliases: ['ps-import-queue-process'])]
  #[CLI\Option(name: 'count', description: 'Number of queue items to process.')]
  #[CLI\Option(name: 'mode', description: 'Optional import mode override: full or delta.')]
  public function queueProcess(array $options = ['count' => 1, 'mode' => NULL]): void {
    $count = max(1, (int) ($options['count'] ?? 1));
    $mode = isset($options['mode']) && $options['mode'] !== NULL && $options['mode'] !== ''
      ? (string) $options['mode']
      : NULL;

    $summary = $this->importPipeline->processQueue($count, $mode);
    $this->renderSummary($summary, TRUE);
  }

  /**
   * Shows CRM import queue depth and lock status.
   */
  #[CLI\Command(name: 'ps:import:queue-status', aliases: ['ps-import-queue-status'])]
  public function queueStatus(): void {
    $status = $this->importPipeline->getQueueStatus();
    $this->io()->title('CRM import queue status');
    $this->io()->writeln(sprintf('Queue: %s', $status['queue_name']));
    $this->io()->writeln(sprintf('Depth: %d', $status['queue_depth']));
    $this->io()->writeln(sprintf('Lock active: %s', $status['lock_active'] ? 'yes' : 'no'));
    $this->io()->writeln(sprintf('Lock stale: %s', $status['lock_stale'] ? 'yes' : 'no'));
    if (is_array($status['lock'])) {
      $this->io()->writeln(sprintf('Lock file: %s', $status['lock']['filename'] ?? 'n/a'));
    }
  }

  /**
   * Recovers stale import locks and requeues files stuck in processing/.
   */
  #[CLI\Command(name: 'ps:import:recover-stale', aliases: ['ps-import-recover-stale'])]
  public function recoverStale(): void {
    $summary = $this->importPipeline->recoverStale();
    $this->io()->writeln(sprintf('Lock released: %s', !empty($summary['lock_released']) ? 'yes' : 'no'));
    $requeued = $summary['requeued'] ?? [];
    if ($requeued === []) {
      $this->io()->success('No processing files to recover.');
      return;
    }
    $this->io()->success(sprintf('Requeued %d file(s): %s', count($requeued), implode(', ', $requeued)));
  }

  /**
   * @param array<string, mixed> $summary
   */
  private function renderSummary(array $summary, bool $expectRuns): void {
    $action = (string) ($summary['action'] ?? '');

    if ($action === 'enqueue') {
      if (($summary['processed'] ?? 0) === 0) {
        $this->io()->warning($summary['message'] ?? 'No files to enqueue.');
        return;
      }
      $this->io()->writeln(sprintf(
        'Enqueue: processed=%d enqueued=%d skipped=%d',
        $summary['processed'],
        $summary['enqueued'],
        $summary['skipped'],
      ));
      return;
    }

    if (($summary['processed'] ?? 0) === 0 && !empty($summary['message'])) {
      $this->io()->warning($summary['message']);
      return;
    }

    foreach ($summary['runs'] ?? [] as $run) {
      if (($run['status'] ?? '') === 'success') {
        $duration = isset($run['duration_ms']) ? sprintf(' (%d ms)', $run['duration_ms']) : '';
        $this->io()->success(sprintf('Imported %s%s', $run['filename'] ?? 'file', $duration));
      }
      else {
        $this->io()->error(sprintf('Failed %s: %s', $run['filename'] ?? 'file', $run['error'] ?? 'unknown'));
      }
    }

    if ($expectRuns || isset($summary['success'])) {
      $this->io()->writeln(sprintf(
        'Done: processed=%d success=%d failed=%d',
        $summary['processed'] ?? 0,
        $summary['success'] ?? 0,
        $summary['failed'] ?? 0,
      ));
    }

    if (($summary['failed'] ?? 0) > 0) {
      throw new \RuntimeException('Import pipeline completed with failures.');
    }
  }

}
