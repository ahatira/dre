<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;

/**
 * Resolves social image URLs for token replacements.
 */
final class SocialImageTokenResolver {

  /**
   * Fallback share image path, relative to web root.
   */
  private const FALLBACK_SHARE_IMAGE = '/share-image.png';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly StreamWrapperManagerInterface $streamWrapperManager,
  ) {}

  /**
   * Resolves a social image URL for a media entity.
   */
  public function resolveMediaUrl(MediaInterface $media, string $style): string {
    $uri = $this->resolveMediaImageUri($media);
    if ($uri === NULL) {
      return $this->buildFallbackAbsoluteUrl();
    }

    return $this->buildImageUrl($uri, $style);
  }

  /**
   * Resolves a social image URL for a node entity.
   */
  public function resolveNodeUrl(NodeInterface $node, string $style, ?string $field_name = NULL): string {
    $media = NULL;

    if ($field_name !== NULL && $field_name !== '' && $node->hasField($field_name)) {
      $target = $node->get($field_name)->entity;
      if ($target instanceof MediaInterface) {
        $media = $target;
      }
    }

    if ($media === NULL) {
      $candidate_fields = ['field_media_photos', 'field_media_image', 'field_media_cover_image'];
      foreach ($candidate_fields as $candidate_field) {
        if (!$node->hasField($candidate_field) || $node->get($candidate_field)->isEmpty()) {
          continue;
        }
        $target = $node->get($candidate_field)->entity;
        if ($target instanceof MediaInterface) {
          $media = $target;
          break;
        }
      }
    }

    if ($media instanceof MediaInterface) {
      return $this->resolveMediaUrl($media, $style);
    }

    return $this->buildFallbackAbsoluteUrl();
  }

  /**
   * Builds a URL for an image URI and style fallback.
   */
  private function buildImageUrl(string $uri, string $style): string {
    $style_entity = $this->entityTypeManager->getStorage('image_style')->load($style);
    if ($style_entity) {
      return $style_entity->buildUrl($uri);
    }

    return $this->streamWrapperManager->getViaUri($uri)
      ? file_create_url($uri)
      : $this->buildFallbackAbsoluteUrl();
  }

  /**
   * Resolves image URI from known media image fields.
   */
  private function resolveMediaImageUri(MediaInterface $media): ?string {
    $candidate_fields = ['field_media_image', 'field_media_cover_image', 'thumbnail'];
    foreach ($candidate_fields as $field_name) {
      if (!$media->hasField($field_name) || $media->get($field_name)->isEmpty()) {
        continue;
      }

      $file = $media->get($field_name)->entity;
      if ($file instanceof FileInterface) {
        return $file->getFileUri();
      }
    }

    return NULL;
  }

  /**
   * Builds an absolute fallback URL.
   */
  private function buildFallbackAbsoluteUrl(): string {
    $base_url = \Drupal::request()->getSchemeAndHttpHost();
    return rtrim($base_url, '/') . self::FALLBACK_SHARE_IMAGE;
  }

}
