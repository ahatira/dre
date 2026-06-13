<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Builds badge image render arrays for certification label terms.
 */
final class CertificationLabelBadgeBuilder {

  /**
   * Builds the badge image render array for a certification label term.
   *
   * @return array<string, mixed>
   *   Image or image_style render array, or empty array when no badge.
   */
  public function build(TermInterface $term, string $image_style = 'certification_label_badge'): array {
    if (!$term->hasField('field_badge') || $term->get('field_badge')->isEmpty()) {
      return [];
    }

    $media = $term->get('field_badge')->entity;
    if (!$media instanceof MediaInterface || !$media->hasField('field_media_image') || $media->get('field_media_image')->isEmpty()) {
      return [];
    }

    $file = $media->get('field_media_image')->entity;
    if (!$file instanceof FileInterface) {
      return [];
    }

    $style = trim($image_style);
    if ($style === '') {
      return [
        '#theme' => 'image',
        '#uri' => $file->getFileUri(),
        '#alt' => $term->label(),
        '#title' => $term->label(),
        '#cache' => [
          'tags' => array_merge($media->getCacheTags(), $file->getCacheTags()),
        ],
      ];
    }

    return [
      '#theme' => 'image_style',
      '#style_name' => $style,
      '#uri' => $file->getFileUri(),
      '#alt' => $term->label(),
      '#title' => $term->label(),
      '#cache' => [
        'tags' => array_merge($media->getCacheTags(), $file->getCacheTags()),
      ],
    ];
  }

}
