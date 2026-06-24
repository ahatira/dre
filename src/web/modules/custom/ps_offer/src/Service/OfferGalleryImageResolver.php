<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;

/**
 * Resolves image media URLs from an offer gallery field.
 */
final class OfferGalleryImageResolver {

  /**
   * Gallery bundles treated as offer photos (excludes video, 3D visit, plans…).
   */
  private const IMAGE_BUNDLES = ['image', 'gallery'];

  public function __construct(
    private readonly OfferDefaultImageResolver $defaultImageResolver,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  /**
   * Styled gallery photo URLs for the given offer node.
   *
   * @return list<string>
   *   Styled image URLs from gallery photo media only.
   */
  public function resolveGalleryImageUrls(NodeInterface $node, ?string $imageStyle = NULL, bool $relative = TRUE): array {
    return array_map(
      fn (string $uri): string => $this->buildStyledUrl($uri, $imageStyle, $relative),
      $this->resolveImageUris($node),
    );
  }

  /**
   * Raw gallery photo file URIs for the given offer node.
   *
   * @return list<string>
   *   Raw file URIs from gallery photo media only.
   */
  public function resolveImageUris(NodeInterface $node): array {
    if (!$node->hasField('field_media_gallery') || $node->get('field_media_gallery')->isEmpty()) {
      return [];
    }

    $uris = [];
    foreach ($node->get('field_media_gallery')->referencedEntities() as $media) {
      if (!$media instanceof MediaInterface || !$this->isImageMedia($media)) {
        continue;
      }
      $uri = $this->resolveMediaUri($media);
      if ($uri !== NULL) {
        $uris[] = $uri;
      }
    }

    return $uris;
  }

  /**
   * Returns the first gallery photo URL, without fallback.
   */
  public function resolvePrimaryImageUrl(NodeInterface $node, ?string $imageStyle = NULL, bool $relative = TRUE): ?string {
    foreach ($this->resolveImageUris($node) as $uri) {
      return $this->buildStyledUrl($uri, $imageStyle, $relative);
    }

    return NULL;
  }

  /**
   * Returns the first gallery photo URI, without fallback.
   */
  public function resolvePrimaryImageUri(NodeInterface $node): ?string {
    foreach ($this->resolveImageUris($node) as $uri) {
      return $uri;
    }

    return NULL;
  }

  /**
   * Returns the first gallery photo URL or the configured default image.
   */
  public function resolvePrimaryImageUrlWithFallback(NodeInterface $node, ?string $imageStyle = NULL, bool $relative = TRUE): string {
    $url = $this->resolvePrimaryImageUrl($node, $imageStyle, $relative);
    if ($url !== NULL) {
      return $url;
    }

    return $this->defaultImageResolver->buildUrl($imageStyle, $relative);
  }

  /**
   * Returns gallery photo URL or configured default, excluding theme placeholder.
   */
  public function resolvePrimaryImageUrlOrConfiguredDefault(NodeInterface $node, ?string $imageStyle = NULL, bool $relative = FALSE): ?string {
    $url = $this->resolvePrimaryImageUrl($node, $imageStyle, $relative);
    if ($url !== NULL) {
      return $url;
    }

    if (!$this->defaultImageResolver->hasConfiguredImage()) {
      return NULL;
    }

    return $this->defaultImageResolver->buildUrl($imageStyle, $relative);
  }

  /**
   * Gallery photo URLs, or a single configured default image when empty.
   *
   * @return list<string>
   *   Gallery photo URLs, or a single default image URL when empty.
   */
  public function resolveGalleryImageUrlsWithFallback(
    NodeInterface $node,
    ?string $imageStyle = NULL,
    bool $relative = TRUE,
  ): array {
    $urls = $this->resolveGalleryImageUrls($node, $imageStyle, $relative);
    if ($urls !== []) {
      return $urls;
    }

    return [$this->defaultImageResolver->buildUrl($imageStyle, $relative)];
  }

  /**
   * Returns the configured default image alt text.
   */
  public function getDefaultImageAlt(): string {
    return $this->defaultImageResolver->getAlt();
  }

  /**
   * Cache tags for the configured default image settings.
   *
   * @return list<string>
   *   Config cache tags.
   */
  public function getDefaultImageCacheTags(): array {
    return $this->defaultImageResolver->getCacheTags();
  }

  /**
   * Checks whether the media item is a gallery photo bundle.
   */
  private function isImageMedia(MediaInterface $media): bool {
    return in_array($media->bundle(), self::IMAGE_BUNDLES, TRUE);
  }

  /**
   * Resolves the preview file URI from a media entity.
   */
  private function resolveMediaUri(MediaInterface $media): ?string {
    $fieldName = $media->bundle() === 'gallery'
      ? 'field_media_gallery_image'
      : 'field_media_image';

    if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
      return NULL;
    }

    $file = $media->get($fieldName)->entity;
    return $file instanceof FileInterface ? $file->getFileUri() : NULL;
  }

  /**
   * Builds a styled or raw URL for a file URI.
   */
  private function buildStyledUrl(string $uri, ?string $imageStyle, bool $relative = TRUE): string {
    if ($imageStyle !== NULL && $imageStyle !== '') {
      $style = ImageStyle::load($imageStyle);
      if ($style !== NULL) {
        $url = $style->buildUrl($uri);
        return $relative ? $this->fileUrlGenerator->transformRelative($url) : $url;
      }
    }

    return $relative
      ? $this->fileUrlGenerator->generateString($uri)
      : $this->fileUrlGenerator->generateAbsoluteString($uri);
  }

}
