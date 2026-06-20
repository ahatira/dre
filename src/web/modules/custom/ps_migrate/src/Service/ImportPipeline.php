<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\ps_migrate\Entity\ImportRunInterface;
use Psr\Log\LoggerInterface;

/**
 * CRM XML import pipeline: incoming → processing → migrate → archive/failed.
 */
final class ImportPipeline {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FileSystemInterface $fileSystem,
    private readonly ImportPipelinePathResolver $pathResolver,
    private readonly ImportPipelineMigrateRunner $migrateRunner,
    private readonly AccountProxyInterface $currentUser,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Processes pending XML files from incoming/ (FIFO).
   *
   * @return array<string, mixed>
   *   Summary of the run.
   */
  public function run(?int $limit = NULL, ?string $mode = NULL): array {
    $config = $this->configFactory->get('ps_migrate.import_pipeline_settings');
    $batchLimit = $limit ?? (int) $config->get('batch_limit');
    $importMode = $mode ?? (string) $config->get('mode');
    if (!in_array($importMode, [ImportRunInterface::MODE_FULL, ImportRunInterface::MODE_DELTA], TRUE)) {
      $importMode = ImportRunInterface::MODE_FULL;
    }

    $this->pathResolver->ensureConfiguredDirectories();
    $files = $this->pathResolver->listIncomingXmlFiles();
    if ($files === []) {
      return [
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
      'processed' => 0,
      'success' => 0,
      'failed' => 0,
      'runs' => [],
    ];

    foreach ($files as $absolutePath) {
      $runSummary = $this->processFile($absolutePath, $importMode);
      $summary['processed']++;
      $summary['runs'][] = $runSummary;
      if ($runSummary['status'] === ImportRunInterface::STATUS_SUCCESS) {
        $summary['success']++;
      }
      else {
        $summary['failed']++;
      }
    }

    return $summary;
  }

  /**
   * Saves an uploaded XML into incoming/ and optionally processes it.
   *
   * @return array<string, mixed>
   *   Deposit result.
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
    ];

    if ($processNow) {
      $absolute = $this->fileSystem->realpath($targetUri);
      if ($absolute === FALSE) {
        throw new \RuntimeException(sprintf('Could not resolve path for %s', $targetUri));
      }
      $importMode = $mode ?? (string) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get('mode');
      $run = $this->processFile($absolute, $importMode);
      $result['processed'] = TRUE;
      $result['run'] = $run;
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
   * Processes one XML file through the pipeline.
   *
   * @return array<string, mixed>
   *   Per-file run summary.
   */
  private function processFile(string $absoluteIncomingPath, string $mode): array {
    $filename = basename($absoluteIncomingPath);
    $started = time();

    /** @var \Drupal\ps_migrate\Entity\ImportRun $run */
    $run = $this->entityTypeManager->getStorage('import_run')->create([
      'filename' => $filename,
      'pipeline_status' => ImportRunInterface::STATUS_PROCESSING,
      'import_mode' => $mode,
      'source_uri' => $this->pathResolver->absoluteToUri($absoluteIncomingPath),
      'started' => $started,
      'uid' => (int) $this->currentUser->id(),
    ]);
    $run->save();

    $processingUri = $this->pathResolver->buildUri('processing', $filename);
    try {
      $this->moveAbsoluteFile($absoluteIncomingPath, $processingUri);
      $this->stageForMigrate($processingUri);
      $migrateStats = $this->migrateRunner->run($mode, TRUE);

      if (!empty($migrateStats['failed'])) {
        throw new \RuntimeException((string) ($migrateStats['error'] ?: 'Migration failed.'));
      }

      $archiveUri = $this->resolveArchiveUri($filename);
      $this->fileSystem->move($processingUri, $archiveUri, FileSystemInterface::EXISTS_REPLACE);

      $run->set('pipeline_status', ImportRunInterface::STATUS_SUCCESS);
      $run->set('file_uri', $archiveUri);
      $run->set('finished', time());
      $run->set('stats', json_encode($migrateStats, JSON_THROW_ON_ERROR));
      $run->save();

      return [
        'id' => (int) $run->id(),
        'filename' => $filename,
        'status' => ImportRunInterface::STATUS_SUCCESS,
        'file_uri' => $archiveUri,
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

      $run->set('pipeline_status', ImportRunInterface::STATUS_FAILED);
      $run->set('file_uri', $failedUri);
      $run->set('finished', time());
      $run->set('messages', $exception->getMessage());
      $run->save();

      $this->logger->error('Import pipeline failed for @file: @message', [
        '@file' => $filename,
        '@message' => $exception->getMessage(),
      ]);

      return [
        'id' => (int) $run->id(),
        'filename' => $filename,
        'status' => ImportRunInterface::STATUS_FAILED,
        'file_uri' => $failedUri,
        'error' => $exception->getMessage(),
      ];
    }
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
