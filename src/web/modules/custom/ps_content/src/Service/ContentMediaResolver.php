<?php

declare(strict_types=1);

namespace Drupal\ps_content\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\ps_content\Utility\ContentMediaReference;

/**
 * Resolves media entity references for content block image fields.
 */
final class ContentMediaResolver {

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  public function resolve(mixed $reference, ?string $langcode = NULL): ContentMediaReference {
    $mid = (int) $reference;
    if ($mid <= 0) {
      return ContentMediaReference::empty();
    }

    $media = $this->entityTypeManager->getStorage('media')->load($mid);
    if (!$media instanceof MediaInterface) {
      return ContentMediaReference::empty();
    }

    if ($langcode !== NULL && $media->hasTranslation($langcode)) {
      $media = $media->getTranslation($langcode);
    }

    $uri = $this->resolveMediaUri($media);
    $url = $uri !== NULL
      ? $this->fileUrlGenerator->generateAbsoluteString($uri)
      : NULL;

    $alt = $this->resolveAlt($media);
    $credit = $this->resolveCredit($media);

    $cacheTags = ['media:' . $media->id()];
    if ($uri !== NULL) {
      $file = $this->entityTypeManager->getStorage('file')->loadByProperties(['uri' => $uri]);
      $file = reset($file);
      if ($file instanceof FileInterface) {
        $cacheTags[] = 'file:' . $file->id();
      }
    }

    return new ContentMediaReference($url, $alt, $credit, $cacheTags);
  }

  public function resolveUrl(mixed $reference, ?string $langcode = NULL): ?string {
    return $this->resolve($reference, $langcode)->url;
  }

  private function resolveCredit(MediaInterface $media): string {
    if (!$media->hasField('field_credit') || $media->get('field_credit')->isEmpty()) {
      return '';
    }

    return trim((string) $media->get('field_credit')->value);
  }

  private function resolveAlt(MediaInterface $media): string {
    $candidates = match ($media->bundle()) {
      'image', 'visite_guided' => ['field_media_image'],
      'gallery' => ['field_media_gallery_image'],
      default => ['field_media_image', 'thumbnail'],
    };

    foreach ($candidates as $fieldName) {
      if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
        continue;
      }
      $item = $media->get($fieldName)->first();
      if ($item === NULL) {
        continue;
      }
      $alt = trim((string) ($item->alt ?? ''));
      if ($alt !== '') {
        return $alt;
      }
    }

    return trim($media->label() ?? '');
  }

  private function resolveMediaUri(MediaInterface $media): ?string {
    $candidates = match ($media->bundle()) {
      'image', 'visite_guided' => ['field_media_image'],
      'gallery' => ['field_media_gallery_image'],
      default => ['thumbnail', 'field_media_image'],
    };

    foreach ($candidates as $fieldName) {
      if (!$media->hasField($fieldName) || $media->get($fieldName)->isEmpty()) {
        continue;
      }
      $file = $media->get($fieldName)->entity;
      if ($file instanceof FileInterface) {
        return $file->getFileUri();
      }
    }

    return NULL;
  }

}
