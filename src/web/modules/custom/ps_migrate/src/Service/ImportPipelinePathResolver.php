<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;

/**
 * Resolves and prepares CRM import pipeline folder URIs.
 */
final class ImportPipelinePathResolver {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FileSystemInterface $fileSystem,
    private readonly StreamWrapperManagerInterface $streamWrapperManager,
  ) {}

  /**
   * Gets a configured pipeline path URI.
   */
  public function getPath(string $key): string {
    $uri = trim((string) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get("paths.{$key}"));
    if ($uri === '') {
      throw new \InvalidArgumentException(sprintf('Missing pipeline path config: paths.%s', $key));
    }
    if ($this->streamWrapperManager->getScheme($uri) === FALSE) {
      throw new \InvalidArgumentException(sprintf('Invalid stream wrapper URI: %s', $uri));
    }
    return $uri;
  }

  /**
   * Gets the migrate staging URI (active XML copy).
   */
  public function getStagingUri(): string {
    $uri = trim((string) $this->configFactory->get('ps_migrate.import_pipeline_settings')->get('staging_uri'));
    if ($uri === '') {
      throw new \InvalidArgumentException('Missing pipeline staging_uri config.');
    }
    return $uri;
  }

  /**
   * Ensures all configured pipeline directories exist.
   */
  public function ensureConfiguredDirectories(): void {
    foreach (['incoming', 'processing', 'archive', 'failed'] as $key) {
      $this->prepareDirectory($this->getPath($key));
    }
    $staging = $this->getStagingUri();
    $stagingDir = $this->fileSystem->dirname($staging);
    if ($stagingDir !== '') {
      $this->prepareDirectory($stagingDir);
    }
  }

  /**
   * Builds a URI inside a pipeline folder.
   */
  public function buildUri(string $folderKey, string $filename): string {
    $base = rtrim($this->getPath($folderKey), '/');
    return $base . '/' . $this->sanitizeFilename($filename);
  }

  /**
   * Lists XML files in incoming/, oldest first (FIFO).
   *
   * @return list<string>
   *   Absolute realpaths.
   */
  public function listIncomingXmlFiles(): array {
    $incoming = $this->getPath('incoming');
    $this->prepareDirectory($incoming);
    $realpath = $this->fileSystem->realpath($incoming);
    if ($realpath === FALSE || !is_dir($realpath)) {
      return [];
    }

    $files = glob($realpath . '/*.xml') ?: [];
    usort($files, static fn(string $a, string $b): int => filemtime($a) <=> filemtime($b));
    return array_values($files);
  }

  /**
   * Converts an absolute path to a stream URI when possible.
   */
  public function absoluteToUri(string $absolutePath): string {
    foreach (['private', 'public'] as $scheme) {
      $wrapper = $this->streamWrapperManager->getViaScheme($scheme);
      if ($wrapper === FALSE) {
        continue;
      }
      $base = $wrapper->realpath();
      if ($base !== FALSE && str_starts_with($absolutePath, $base)) {
        $relative = ltrim(substr($absolutePath, strlen($base)), '/\\');
        return $scheme . '://' . str_replace('\\', '/', $relative);
      }
    }
    return $absolutePath;
  }

  /**
   * Sanitizes a filename for safe storage.
   */
  public function sanitizeFilename(string $filename): string {
    $filename = basename($filename);
    $filename = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $filename) ?? 'import.xml';
    if (!str_ends_with(strtolower($filename), '.xml')) {
      $filename .= '.xml';
    }
    return $filename;
  }

  /**
   * Prepares a directory for read/write.
   */
  public function prepareDirectory(string $uri): void {
    $this->fileSystem->prepareDirectory($uri, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
  }

}
