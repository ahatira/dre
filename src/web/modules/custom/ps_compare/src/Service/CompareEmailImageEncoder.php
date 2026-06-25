<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\File\FileSystemInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferDefaultImageResolver;
use GuzzleHttp\ClientInterface;

/**
 * Encodes local image files as base64 data URIs for HTML email.
 */
final class CompareEmailImageEncoder {

  private const IMAGE_STYLE = 'ps_compare_column';

  /**
   * Skip oversized payloads (many clients choke above ~200 KB per image).
   */
  private const MAX_BYTES = 120000;

  /**
   * Allowed remote hosts for static map embedding in email.
   */
  private const ALLOWED_REMOTE_HOSTS = [
    'maps.googleapis.com',
    'staticmap.openstreetmap.de',
  ];

  public function __construct(
    private readonly CompareOfferSummaryBuilder $offerSummaryBuilder,
    private readonly FileSystemInterface $fileSystem,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly ClientInterface $httpClient,
    private readonly OfferDefaultImageResolver $defaultImageResolver,
  ) {}

  /**
   * Builds a data URI for the offer primary image (styled derivative).
   */
  public function encodeOfferImage(NodeInterface $offer): ?string {
    $uri = $this->offerSummaryBuilder->resolvePrimaryImageFileUri($offer);
    if ($uri === NULL) {
      $uri = $this->defaultImageResolver->getFileUri();
    }
    if ($uri === NULL) {
      return $this->encodeThemePlaceholder();
    }

    return $this->encodeStyledFileUri($uri);
  }

  /**
   * Encodes gallery images for one offer (limited count).
   *
   * @return list<string>
   */
  public function encodeOfferGallery(NodeInterface $offer, int $limit = 1): array {
    $uris = $this->offerSummaryBuilder->resolveGalleryFileUris($offer);
    $encoded = [];
    foreach (array_slice($uris, 0, max(1, $limit)) as $uri) {
      $dataUri = $this->encodeStyledFileUri($uri);
      if ($dataUri !== NULL) {
        $encoded[] = $dataUri;
      }
    }

    if ($encoded === []) {
      $configuredUri = $this->defaultImageResolver->getFileUri();
      if ($configuredUri !== NULL) {
        $dataUri = $this->encodeStyledFileUri($configuredUri);
        return $dataUri !== NULL ? [$dataUri] : [];
      }

      $placeholder = $this->encodeThemePlaceholder();
      return $placeholder !== NULL ? [$placeholder] : [];
    }

    return $encoded;
  }

  /**
   * Encodes a file URI through the compare column image style.
   */
  public function encodeStyledFileUri(string $sourceUri): ?string {
    $style = ImageStyle::load(self::IMAGE_STYLE);
    if ($style === NULL) {
      return $this->encodeRawFileUri($sourceUri);
    }

    $derivativeUri = $style->buildUri($sourceUri);
    if (!file_exists($derivativeUri)) {
      $style->createDerivative($sourceUri, $derivativeUri);
    }

    return $this->encodeRawFileUri($derivativeUri);
  }

  /**
   * Encodes a readable local file URI as a data URI.
   */
  public function encodeRawFileUri(string $uri): ?string {
    $path = $this->fileSystem->realpath($uri);
    if ($path === FALSE || !is_readable($path)) {
      return NULL;
    }

    $contents = @file_get_contents($path);
    if ($contents === FALSE || $contents === '') {
      return NULL;
    }

    if (strlen($contents) > self::MAX_BYTES) {
      return NULL;
    }

    $mime = $this->guessMimeType($path);
    return 'data:' . $mime . ';base64,' . base64_encode($contents);
  }

  /**
   * Fetches a remote static map image and returns a data URI for email embedding.
   */
  public function encodeRemoteImage(string $url): ?string {
    $host = (string) (parse_url($url, PHP_URL_HOST) ?? '');
    if ($host === '' || !in_array($host, self::ALLOWED_REMOTE_HOSTS, TRUE)) {
      return NULL;
    }

    try {
      $response = $this->httpClient->request('GET', $url, [
        'timeout' => 8,
        'http_errors' => FALSE,
      ]);
    }
    catch (\Throwable) {
      return NULL;
    }

    if ($response->getStatusCode() !== 200) {
      return NULL;
    }

    $contents = (string) $response->getBody();
    if ($contents === '' || strlen($contents) > self::MAX_BYTES) {
      return NULL;
    }

    $mime = trim(explode(';', $response->getHeaderLine('Content-Type'))[0] ?? '');
    if ($mime === '' || !str_starts_with($mime, 'image/')) {
      $mime = 'image/png';
    }

    return 'data:' . $mime . ';base64,' . base64_encode($contents);
  }

  /**
   * Encodes the theme offer placeholder asset when no photo exists.
   */
  public function encodeThemePlaceholder(): ?string {
    try {
      $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    }
    catch (\Throwable) {
      return NULL;
    }

    $path = DRUPAL_ROOT . '/' . $themePath . '/assets/images/offer-placeholder.svg';
    if (!is_readable($path)) {
      return NULL;
    }

    $contents = file_get_contents($path);
    if ($contents === FALSE || $contents === '') {
      return NULL;
    }

    return 'data:image/svg+xml;base64,' . base64_encode($contents);
  }

  /**
   * Guesses the MIME type from a filesystem path extension.
   */
  private function guessMimeType(string $path): string {
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return match ($extension) {
      'jpg', 'jpeg' => 'image/jpeg',
      'png' => 'image/png',
      'gif' => 'image/gif',
      'webp' => 'image/webp',
      'svg' => 'image/svg+xml',
      default => 'application/octet-stream',
    };
  }

}
