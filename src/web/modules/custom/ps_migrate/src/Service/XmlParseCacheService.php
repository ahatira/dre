<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\File\FileSystemInterface;

/**
 * Scoped XML parse cache for a single CRM import pipeline run.
 *
 * Parses the staging offers.xml once and reuses the result for migrations
 * and feature loaders during the same processFile() cycle.
 */
final class XmlParseCacheService {

  private ?string $stagingUri = NULL;

  private ?string $stagingRealpath = NULL;

  private ?string $rawContent = NULL;

  private ?\SimpleXMLElement $document = NULL;

  private ?string $tempFilePath = NULL;

  public function __construct(
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * Loads and parses the staging XML for the current pipeline run.
   */
  public function beginRun(string $stagingUri): void {
    $this->clearRun();

    $absolutePath = $this->resolveReadablePath($stagingUri);
    if ($absolutePath === NULL) {
      throw new \RuntimeException(sprintf('Unable to read staging XML: %s', $stagingUri));
    }

    $contents = file_get_contents($absolutePath);
    if ($contents === FALSE || $contents === '') {
      throw new \RuntimeException(sprintf('Staging XML is empty or unreadable: %s', $stagingUri));
    }

    $previous = libxml_use_internal_errors(TRUE);
    libxml_clear_errors();

    $document = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
    if ($document === FALSE) {
      $errors = array_map(static fn(\LibXMLError $error): string => trim($error->message), libxml_get_errors());
      libxml_clear_errors();
      libxml_use_internal_errors($previous);
      throw new \RuntimeException(sprintf(
        'Unable to parse staging XML %s. %s',
        $stagingUri,
        implode(' ', $errors),
      ));
    }

    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $this->stagingUri = $stagingUri;
    $this->stagingRealpath = $absolutePath;
    $this->rawContent = $contents;
    $this->document = $document;

    $this->tempFilePath = $this->fileSystem->tempnam('temporary://', 'ps_migrate_xml_');
    if (file_put_contents($this->tempFilePath, $contents) === FALSE) {
      throw new \RuntimeException('Unable to create temporary XML file for parse cache.');
    }
  }

  /**
   * Clears the cache for the current pipeline run.
   */
  public function clearRun(): void {
    if ($this->tempFilePath !== NULL && is_file($this->tempFilePath)) {
      @unlink($this->tempFilePath);
    }

    $this->stagingUri = NULL;
    $this->stagingRealpath = NULL;
    $this->rawContent = NULL;
    $this->document = NULL;
    $this->tempFilePath = NULL;
  }

  /**
   * Whether a parse cache is active for the current run.
   */
  public function isActive(): bool {
    return $this->document !== NULL && $this->rawContent !== NULL;
  }

  /**
   * Whether the given URL or path refers to the cached staging XML.
   */
  public function matchesUrl(string $url): bool {
    if (!$this->isActive()) {
      return FALSE;
    }

    if ($this->stagingUri !== NULL && $url === $this->stagingUri) {
      return TRUE;
    }

    $resolved = $this->resolveReadablePath($url);
    return $resolved !== NULL
      && $this->stagingRealpath !== NULL
      && $resolved === $this->stagingRealpath;
  }

  /**
   * Returns cached raw XML content when the URL matches the staging file.
   */
  public function getRawContent(string $url): string {
    if (!$this->matchesUrl($url) || $this->rawContent === NULL) {
      throw new \RuntimeException(sprintf('No cached XML content for URL: %s', $url));
    }

    return $this->rawContent;
  }

  /**
   * Returns the cached parsed XML document.
   */
  public function getDocument(): \SimpleXMLElement {
    if (!$this->isActive() || $this->document === NULL) {
      throw new \RuntimeException('XML parse cache is not active.');
    }

    return $this->document;
  }

  /**
   * Returns offer nodes from the cached document.
   *
   * @return \SimpleXMLElement[]
   */
  public function getOffers(): array {
    return $this->getDocument()->xpath('/OFFERS_LIST/OFFER') ?: [];
  }

  /**
   * Returns the temp file path used by streaming XML parsers.
   */
  public function getTempFilePath(): string {
    if ($this->tempFilePath === NULL || !is_file($this->tempFilePath)) {
      throw new \RuntimeException('XML parse cache temp file is unavailable.');
    }

    return $this->tempFilePath;
  }

  /**
   * Resolves a stream URI or filesystem path to a readable absolute path.
   */
  private function resolveReadablePath(string $uriOrPath): ?string {
    $uriOrPath = trim($uriOrPath);
    if ($uriOrPath === '') {
      return NULL;
    }

    if (is_file($uriOrPath) && is_readable($uriOrPath)) {
      $realpath = realpath($uriOrPath);
      return $realpath !== FALSE ? $realpath : $uriOrPath;
    }

    $realpath = $this->fileSystem->realpath($uriOrPath);
    if ($realpath !== FALSE && is_readable($realpath)) {
      return $realpath;
    }

    return NULL;
  }

}
