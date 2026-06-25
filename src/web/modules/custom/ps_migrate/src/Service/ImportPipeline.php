<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\State\StateInterface;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Psr\Log\LoggerInterface;

/**
 * CRM XML import pipeline: incoming → processing → migrate → archive/failed.
 */
final class ImportPipeline {

  public const QUEUE_NAME = 'ps_migrate.import_file';

  private const STATE_ENQUEUED = 'ps_migrate.import_pipeline.enqueued_checksums';

  private const SLA_SECONDS = 3600;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FileSystemInterface $fileSystem,
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly ImportPipelineMigrateRunner $migrateRunner,
    private readonly AccountProxyInterface $currentUser,
    private readonly LoggerInterface $logger,
    private readonly QueueFactory $queueFactory,
    private readonly StateInterface $state,
    private readonly ImportPipelineLock $lock,
    private readonly ImportPipelineAlertNotifier $alertNotifier,
    private readonly ImportPipelinePostRunIndexer $postRunIndexer,
    private readonly XmlParseCacheService $xmlParseCache,
    private readonly ImportPipelineRunContext $runContext,
    private readonly TimeInterface $time,
  ) {}

  /**
   * Processes pending XML files (sync) or enqueues them (async default).
   *
   * @return array<string, mixed>
   *   Summary of the run.
   */
  public function run(?int $limit = NULL, ?string $mode = NULL, bool $sync = FALSE): array {
    if ($sync || !$this->isQueueEnabled()) {
      return $this->runSynchronously($limit, $mode);
    }

    return $this->enqueueIncoming($limit, $mode);
  }

  /**
   * Scans incoming/ and enqueues new XML files.
   *
   * @return array<string, mixed>
   */
  public function enqueueIncoming(?int $limit = NULL, ?string $mode = NULL): array {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    $batchLimit = $limit ?? (int) $config->get('batch_limit');
    $importMode = $this->normalizeMode($mode ?? (string) $config->get('mode'));

    $this->pathResolver->ensureConfiguredDirectories();
    $files = $this->pathResolver->listIncomingXmlFiles();
    if ($batchLimit > 0) {
      $files = array_slice($files, 0, $batchLimit);
    }

    $summary = [
      'action' => 'enqueue',
      'processed' => 0,
      'enqueued' => 0,
      'skipped' => 0,
      'files' => [],
      'message' => '',
    ];

    if ($files === []) {
      $summary['message'] = 'No XML files in incoming folder.';
      return $summary;
    }

    foreach ($files as $absolutePath) {
      $summary['processed']++;
      $result = $this->enqueueFile($absolutePath, $importMode);
      $summary['files'][] = $result;
      if (!empty($result['enqueued'])) {
        $summary['enqueued']++;
      }
      else {
        $summary['skipped']++;
      }
    }

    return $summary;
  }

  /**
   * Claims and processes items from the import queue.
   *
   * @return array<string, mixed>
   */
  public function processQueue(int $count = 1, ?string $mode = NULL): array {
    $queue = $this->queueFactory->get(self::QUEUE_NAME);
    $summary = [
      'action' => 'process_queue',
      'requested' => max(1, $count),
      'processed' => 0,
      'success' => 0,
      'failed' => 0,
      'runs' => [],
    ];

    for ($i = 0; $i < $summary['requested']; $i++) {
      $item = $queue->claimItem(300);
      if ($item === FALSE) {
        break;
      }

      $summary['processed']++;
      try {
        $data = is_array($item->data) ? $item->data : [];
        if ($mode !== NULL) {
          $data['import_mode'] = $this->normalizeMode($mode);
        }
        $runSummary = $this->processQueueItem($data);
        $queue->deleteItem($item);
        $summary['runs'][] = $runSummary;
        if (($runSummary['status'] ?? '') === ImportRunInterface::STATUS_SUCCESS) {
          $summary['success']++;
        }
        else {
          $summary['failed']++;
        }
      }
      catch (\Throwable $exception) {
        $queue->releaseItem($item);
        $summary['failed']++;
        $summary['runs'][] = [
          'status' => ImportRunInterface::STATUS_FAILED,
          'error' => $exception->getMessage(),
        ];
        $this->logger->error('Queue item processing failed: @message', [
          '@message' => $exception->getMessage(),
        ]);
        break;
      }
    }

    if ($summary['processed'] === 0) {
      $summary['message'] = 'No pending items in import queue.';
    }

    return $summary;
  }

  /**
   * Processes a single queue item payload.
   *
   * @param array<string, mixed> $data
   *
   * @return array<string, mixed>
   */
  public function processQueueItem(array $data): array {
    $filename = (string) ($data['filename'] ?? '');
    $sourceUri = (string) ($data['source_uri'] ?? '');
    $importMode = $this->normalizeMode((string) ($data['import_mode'] ?? ImportRunInterface::MODE_FULL));
    $checksum = (string) ($data['checksum'] ?? '');

    if ($filename === '' && $sourceUri !== '') {
      $filename = basename($sourceUri);
    }

    $absolutePath = $sourceUri !== ''
      ? ($this->fileSystem->realpath($sourceUri) ?: '')
      : '';

    if ($absolutePath === '' || !is_readable($absolutePath)) {
      $incomingUri = $this->pathResolver->buildUri('incoming', $filename);
      $absolutePath = $this->fileSystem->realpath($incomingUri) ?: '';
    }

    if ($absolutePath === '' || !is_readable($absolutePath)) {
      throw new \RuntimeException(sprintf('Queued import file not readable: %s', $filename));
    }

    try {
      return $this->processFile($absolutePath, $importMode, $checksum);
    }
    finally {
      if ($checksum !== '') {
        $this->removeEnqueuedChecksum($checksum);
      }
    }
  }

  /**
   * Returns queue depth and lock status.
   *
   * @return array<string, mixed>
   */
  public function getQueueStatus(): array {
    $queue = $this->queueFactory->get(self::QUEUE_NAME);
    $lock = $this->lock->getLock();

    return [
      'queue_name' => self::QUEUE_NAME,
      'queue_depth' => $queue->numberOfItems(),
      'lock_active' => $this->lock->isLocked(),
      'lock_stale' => $this->lock->isStale(),
      'lock' => $lock,
      'enqueued_checksums' => count($this->getEnqueuedChecksums()),
    ];
  }

  /**
   * Recovers stale locks and requeues files stuck in processing/.
   *
   * @return array<string, mixed>
   */
  public function recoverStale(): array {
    $summary = [
      'lock_released' => FALSE,
      'requeued' => [],
    ];

    if ($this->lock->isStale()) {
      $this->lock->forceRelease();
      $summary['lock_released'] = TRUE;
    }

    $processingPath = $this->pathResolver->getPath('processing');
    $realpath = $this->fileSystem->realpath($processingPath);
    if ($realpath === FALSE || !is_dir($realpath)) {
      return $summary;
    }

    $files = glob($realpath . '/*.xml') ?: [];
    foreach ($files as $absolutePath) {
      $filename = basename($absolutePath);
      $incomingUri = $this->pathResolver->buildUri('incoming', $filename);
      if ($this->fileSystem->move($absolutePath, $incomingUri, FileSystemInterface::EXISTS_REPLACE)) {
        $summary['requeued'][] = $filename;
      }
    }

    return $summary;
  }

  /**
   * Saves an uploaded XML into incoming/ and optionally processes it.
   *
   * @return array<string, mixed>
   */
  public function depositUploadedFile(string $temporaryPath, string $originalFilename, bool $processNow = FALSE, ?string $mode = NULL): array {
    $this->pathResolver->ensureConfiguredDirectories();
    $filename = $this->pathResolver->sanitizeFilename($originalFilename);
    $targetUri = $this->pathResolver->buildUri('incoming', $filename);

    $existing = $this->fileSystem->realpath($targetUri);
    if ($existing !== FALSE && file_exists($existing)) {
      throw new \RuntimeException(sprintf('File already exists in incoming/: %s', $filename));
    }

    if (!$this->fileSystem->move($temporaryPath, $targetUri, FileSystemInterface::EXISTS_ERROR)) {
      throw new \RuntimeException(sprintf('Could not save uploaded file to %s', $targetUri));
    }

    $result = [
      'filename' => $filename,
      'uri' => $targetUri,
      'processed' => FALSE,
      'enqueued' => FALSE,
    ];

    $importMode = $this->normalizeMode($mode ?? (string) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get('mode'));
    $absolute = $this->fileSystem->realpath($targetUri);
    if ($absolute === FALSE) {
      throw new \RuntimeException(sprintf('Could not resolve path for %s', $targetUri));
    }

    if ($processNow) {
      if ($this->isQueueEnabled()) {
        $enqueue = $this->enqueueFile($absolute, $importMode);
        $result['enqueued'] = !empty($enqueue['enqueued']);
        $result['queue'] = $enqueue;
        if ($result['enqueued']) {
          $processSummary = $this->processQueue(1, $importMode);
          $result['processed'] = ($processSummary['processed'] ?? 0) > 0;
          $result['run'] = $processSummary['runs'][0] ?? NULL;
        }
      }
      else {
        $run = $this->processFile($absolute, $importMode);
        $result['processed'] = TRUE;
        $result['run'] = $run;
      }
    }
    elseif ($this->isQueueEnabled()) {
      $enqueue = $this->enqueueFile($absolute, $importMode);
      $result['enqueued'] = !empty($enqueue['enqueued']);
      $result['queue'] = $enqueue;
    }

    return $result;
  }

  /**
   * Seeds a sample XML into incoming/ when the folder is empty.
   */
  public function seedSampleIfEmpty(string $sampleAbsolutePath): bool {
    if ($this->pathResolver->listIncomingXmlFiles() !== []) {
      return FALSE;
    }
    if (!is_readable($sampleAbsolutePath)) {
      throw new \RuntimeException(sprintf('Sample XML not readable: %s', $sampleAbsolutePath));
    }

    $filename = basename($sampleAbsolutePath);
    $targetUri = $this->pathResolver->buildUri('incoming', $filename);
    $this->pathResolver->prepareDirectory($this->pathResolver->getPath('incoming'));
    if (!$this->fileSystem->copy($sampleAbsolutePath, $targetUri, FileSystemInterface::EXISTS_REPLACE)) {
      throw new \RuntimeException(sprintf('Could not seed sample to %s', $targetUri));
    }
    $this->logger->info('Seeded sample XML to incoming/: @uri', ['@uri' => $targetUri]);
    return TRUE;
  }

  /**
   * @return array<string, mixed>
   */
  private function runSynchronously(?int $limit, ?string $mode): array {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    $batchLimit = $limit ?? (int) $config->get('batch_limit');
    $importMode = $this->normalizeMode($mode ?? (string) $config->get('mode'));

    $this->pathResolver->ensureConfiguredDirectories();
    $files = $this->pathResolver->listIncomingXmlFiles();
    if ($files === []) {
      return [
        'action' => 'sync',
        'processed' => 0,
        'success' => 0,
        'failed' => 0,
        'runs' => [],
        'message' => 'No XML files in incoming folder.',
      ];
    }

    if ($batchLimit > 0) {
      $files = array_slice($files, 0, $batchLimit);
    }

    $summary = [
      'action' => 'sync',
      'processed' => 0,
      'success' => 0,
      'failed' => 0,
      'runs' => [],
    ];

    foreach ($files as $absolutePath) {
      $checksum = $this->computeFileChecksum($absolutePath);
      $summary['processed']++;
      try {
        $runSummary = $this->processFile($absolutePath, $importMode, $checksum);
        $summary['runs'][] = $runSummary;
        if ($runSummary['status'] === ImportRunInterface::STATUS_SUCCESS) {
          $summary['success']++;
        }
        else {
          $summary['failed']++;
        }
      }
      finally {
        if ($checksum !== '') {
          $this->removeEnqueuedChecksum($checksum);
        }
      }
    }

    return $summary;
  }

  /**
   * @return array<string, mixed>
   */
  private function enqueueFile(string $absolutePath, string $importMode): array {
    $filename = basename($absolutePath);
    $checksum = $this->computeFileChecksum($absolutePath);
    $sourceUri = $this->pathResolver->absoluteToUri($absolutePath);

    if ($this->isChecksumEnqueued($checksum)) {
      return [
        'filename' => $filename,
        'enqueued' => FALSE,
        'reason' => 'already_enqueued',
        'checksum' => $checksum,
      ];
    }

    $queue = $this->queueFactory->get(self::QUEUE_NAME);
    $queue->createItem([
      'filename' => $filename,
      'source_uri' => $sourceUri,
      'import_mode' => $importMode,
      'checksum' => $checksum,
      'enqueued_at' => $this->time->getRequestTime(),
      'uid' => (int) $this->currentUser->id(),
    ]);

    $this->markChecksumEnqueued($checksum);

    return [
      'filename' => $filename,
      'enqueued' => TRUE,
      'checksum' => $checksum,
      'source_uri' => $sourceUri,
    ];
  }

  /**
   * Processes one XML file through the pipeline.
   *
   * @return array<string, mixed>
   */
  private function processFile(string $absoluteIncomingPath, string $mode, string $checksum = ''): array {
    $filename = basename($absoluteIncomingPath);
    if (!$this->lock->acquire($filename)) {
      throw new \RuntimeException('Another CRM import is already in progress for this site.');
    }

    $started = $this->time->getCurrentTime();
    $startedHr = hrtime(TRUE);
    if ($checksum === '') {
      $checksum = $this->computeFileChecksum($absoluteIncomingPath);
    }

    /** @var \Drupal\ps_migrate\Entity\ImportRun $run */
    $run = $this->entityTypeManager->getStorage('import_run')->create([
      'filename' => $filename,
      'pipeline_status' => ImportRunInterface::STATUS_PROCESSING,
      'import_mode' => $mode,
      'source_uri' => $this->pathResolver->absoluteToUri($absoluteIncomingPath),
      'source_checksum' => $checksum,
      'started' => $started,
      'uid' => (int) $this->currentUser->id(),
    ]);
    $run->save();
    $this->lock->attachImportRunId((int) $run->id());

    $processingUri = $this->pathResolver->buildUri('processing', $filename);
    try {
      $this->moveAbsoluteFile($absoluteIncomingPath, $processingUri);
      $this->stageForMigrate($processingUri);

      $stagingUri = $this->pathResolver->getStagingUri();
      $this->xmlParseCache->beginRun($stagingUri);
      $this->runContext->begin($mode);

      $migrateStats = $this->migrateRunner->run($mode, TRUE);

      if (!empty($migrateStats['failed'])) {
        throw new \RuntimeException((string) ($migrateStats['error'] ?: 'Migration failed.'));
      }

      $archiveUri = $this->resolveArchiveUri($filename);
      $this->fileSystem->move($processingUri, $archiveUri, FileSystemInterface::EXISTS_REPLACE);

      $finished = $this->time->getCurrentTime();
      $durationMs = (int) round((hrtime(TRUE) - $startedHr) / 1_000_000);
      $migrateStats = $this->enrichStats($migrateStats, $durationMs);
      $migrateStats['solr'] = $this->postRunIndexer->indexOffers();

      $run->set('pipeline_status', ImportRunInterface::STATUS_SUCCESS);
      $run->set('file_uri', $archiveUri);
      $run->set('finished', $finished);
      $run->set('duration_ms', $durationMs);
      $run->set('stats', json_encode($migrateStats, JSON_THROW_ON_ERROR));
      $run->save();

      return [
        'id' => (int) $run->id(),
        'filename' => $filename,
        'status' => ImportRunInterface::STATUS_SUCCESS,
        'file_uri' => $archiveUri,
        'duration_ms' => $durationMs,
        'stats' => $migrateStats,
      ];
    }
    catch (\Throwable $exception) {
      $failedUri = $this->pathResolver->buildUri('failed', $filename);
      $processingReal = $this->fileSystem->realpath($processingUri);
      if ($processingReal !== FALSE && file_exists($processingReal)) {
        @$this->fileSystem->move($processingUri, $failedUri, FileSystemInterface::EXISTS_REPLACE);
      }
      elseif (is_readable($absoluteIncomingPath)) {
        @$this->fileSystem->move($absoluteIncomingPath, $failedUri, FileSystemInterface::EXISTS_REPLACE);
      }

      $finished = $this->time->getCurrentTime();
      $durationMs = (int) round((hrtime(TRUE) - $startedHr) / 1_000_000);

      $run->set('pipeline_status', ImportRunInterface::STATUS_FAILED);
      $run->set('file_uri', $failedUri);
      $run->set('finished', $finished);
      $run->set('duration_ms', $durationMs);
      $run->set('messages', $exception->getMessage());
      $run->save();

      $this->alertNotifier->notifyFailure($run, $exception->getMessage());

      $this->logger->error('Import pipeline failed for @file: @message', [
        '@file' => $filename,
        '@message' => $exception->getMessage(),
      ]);

      return [
        'id' => (int) $run->id(),
        'filename' => $filename,
        'status' => ImportRunInterface::STATUS_FAILED,
        'file_uri' => $failedUri,
        'duration_ms' => $durationMs,
        'error' => $exception->getMessage(),
      ];
    }
    finally {
      $this->xmlParseCache->clearRun();
      $this->runContext->clear();
      $this->lock->release();
    }
  }

  /**
   * @param array<string, mixed> $stats
   *
   * @return array<string, mixed>
   */
  private function enrichStats(array $stats, int $durationMs): array {
    $stats['duration_ms'] = $durationMs;
    $stats['sla_seconds'] = self::SLA_SECONDS;
    $stats['sla_breached'] = $durationMs > (self::SLA_SECONDS * 1000);
    return $stats;
  }

  private function isQueueEnabled(): bool {
    return (bool) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get('queue_enabled');
  }

  private function normalizeMode(?string $mode): string {
    $mode = (string) $mode;
    if (!in_array($mode, [ImportRunInterface::MODE_FULL, ImportRunInterface::MODE_DELTA], TRUE)) {
      return ImportRunInterface::MODE_FULL;
    }
    return $mode;
  }

  private function computeFileChecksum(string $absolutePath): string {
    $hash = @hash_file('sha256', $absolutePath);
    return is_string($hash) ? $hash : '';
  }

  /**
   * @return array<string, int>
   */
  private function getEnqueuedChecksums(): array {
    $checksums = $this->state->get(self::STATE_ENQUEUED);
    return is_array($checksums) ? $checksums : [];
  }

  private function isChecksumEnqueued(string $checksum): bool {
    return $checksum !== '' && isset($this->getEnqueuedChecksums()[$checksum]);
  }

  private function markChecksumEnqueued(string $checksum): void {
    if ($checksum === '') {
      return;
    }
    $checksums = $this->getEnqueuedChecksums();
    $checksums[$checksum] = $this->time->getRequestTime();
    $this->state->set(self::STATE_ENQUEUED, $checksums);
  }

  private function removeEnqueuedChecksum(string $checksum): void {
    if ($checksum === '') {
      return;
    }
    $checksums = $this->getEnqueuedChecksums();
    unset($checksums[$checksum]);
    $this->state->set(self::STATE_ENQUEUED, $checksums);
  }

  /**
   * Copies the active XML to the migrate staging URI.
   */
  private function stageForMigrate(string $processingUri): void {
    $stagingUri = $this->pathResolver->getStagingUri();
    $stagingDir = $this->fileSystem->dirname($stagingUri);
    if ($stagingDir !== '') {
      $this->pathResolver->prepareDirectory($stagingDir);
    }
    if (!$this->fileSystem->copy($processingUri, $stagingUri, FileSystemInterface::EXISTS_REPLACE)) {
      throw new \RuntimeException(sprintf('Could not stage XML to %s', $stagingUri));
    }
  }

  /**
   * Resolves archive URI, keeping original name (collision → timestamp suffix).
   */
  private function resolveArchiveUri(string $filename): string {
    $uri = $this->pathResolver->buildUri('archive', $filename);
    $realpath = $this->fileSystem->realpath($uri);
    if ($realpath !== FALSE && file_exists($realpath)) {
      $base = pathinfo($filename, PATHINFO_FILENAME);
      $filename = $base . '_' . date('YmdHis') . '.xml';
      $uri = $this->pathResolver->buildUri('archive', $filename);
    }
    return $uri;
  }

  /**
   * Moves an absolute filesystem path to a stream URI.
   */
  private function moveAbsoluteFile(string $absolutePath, string $targetUri): void {
    $this->pathResolver->prepareDirectory($this->fileSystem->dirname($targetUri));
    if (!$this->fileSystem->move($absolutePath, $targetUri, FileSystemInterface::EXISTS_REPLACE)) {
      throw new \RuntimeException(sprintf('Could not move %s to %s', $absolutePath, $targetUri));
    }
  }

}
