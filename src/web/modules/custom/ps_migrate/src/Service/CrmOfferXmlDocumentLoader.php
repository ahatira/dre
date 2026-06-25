<?php

declare(strict_types=1);

namespace Drupal\ps_migrate\Service;

use Drupal\Core\File\FileSystemInterface;

/**
 * Loads CRM offer XML documents, preferring the pipeline parse cache.
 */
final class CrmOfferXmlDocumentLoader {

  public function __construct(
    private readonly XmlParseCacheService $xmlParseCache,
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * Loads a parsed XML document for the given URI or path.
   */
  public function loadDocument(string $uri): \SimpleXMLElement {
    if ($this->xmlParseCache->isActive() && $this->xmlParseCache->matchesUrl($uri)) {
      return $this->xmlParseCache->getDocument();
    }

    $absolutePath = $this->resolveReadablePath($uri);
    if ($absolutePath === NULL) {
      throw new \RuntimeException(sprintf('Unable to read CRM XML: %s', $uri));
    }

    $contents = file_get_contents($absolutePath);
    if ($contents === FALSE || $contents === '') {
      throw new \RuntimeException(sprintf('CRM XML is empty or unreadable: %s', $uri));
    }

    $previous = libxml_use_internal_errors(TRUE);
    libxml_clear_errors();

    $document = simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOCDATA);
    if ($document === FALSE) {
      $errors = array_map(static fn(\LibXMLError $error): string => trim($error->message), libxml_get_errors());
      libxml_clear_errors();
      libxml_use_internal_errors($previous);
      throw new \RuntimeException(sprintf(
        'Unable to parse CRM XML %s. %s',
        $uri,
        implode(' ', $errors),
      ));
    }

    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    return $document;
  }

  /**
   * Selects item nodes from a CRM XML document.
   *
   * @param array<string, string> $namespaces
   *
   * @return \SimpleXMLElement[]
   */
  public function selectItems(string $uri, string $itemSelector, array $namespaces = []): array {
    $document = $this->loadDocument($uri);
    foreach ($namespaces as $prefix => $namespace) {
      $document->registerXPathNamespace((string) $prefix, (string) $namespace);
    }

    return $document->xpath($itemSelector) ?: [];
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
