<?php

declare(strict_types=1);

namespace Drupal\ps_news\Service;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\media\MediaInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Builds props for the news-teaser-card SDC from an article node.
 */
final class NewsTeaserBuilder {

  private const IMAGE_STYLE = 'large';

  private const EXCERPT_LENGTH = 160;

  public function __construct(
    private readonly DateFormatterInterface $dateFormatter,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  /**
   * @return array<string, mixed>
   *   Props keyed for ps_theme:news-teaser-card.
   */
  public function build(NodeInterface $node): array {
    $title = $node->label() ?? '';
    $image = $this->resolveTeaserImage($node);
    $categories = $this->resolveCategories($node);
    $dateMeta = $this->resolveDisplayDateMeta($node);

    return [
      'title' => $title,
      'url' => $node->toUrl()->toString(),
      'image' => $image['url'] ?? '',
      'image_alt' => $image['alt'] ?? $title,
      'categories' => $categories,
      'category' => $categories[0] ?? NULL,
      'date' => $dateMeta['formatted'],
      'date_iso' => $dateMeta['iso'],
      'excerpt' => $this->buildExcerpt($node),
    ];
  }

  /**
   * @return array{url: string, alt: string}
   */
  private function resolveTeaserImage(NodeInterface $node): array {
    if (!$node->hasField('field_teaser_image') || $node->get('field_teaser_image')->isEmpty()) {
      return ['url' => '', 'alt' => ''];
    }

    $media = $node->get('field_teaser_image')->entity;
    if (!$media instanceof MediaInterface) {
      return ['url' => '', 'alt' => ''];
    }

    $file = $media->get('field_media_image')->entity ?? NULL;
    if (!$file instanceof FileInterface) {
      return ['url' => '', 'alt' => ''];
    }

    $style = ImageStyle::load(self::IMAGE_STYLE);
    $url = $style?->buildUrl($file->getFileUri()) ?? $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
    $alt = (string) ($media->get('field_media_image')->alt ?? $node->label() ?? '');

    return [
      'url' => $url,
      'alt' => $alt,
    ];
  }

  /**
   * @return list<string>
   */
  private function resolveCategories(NodeInterface $node): array {
    if (!$node->hasField('field_news_category') || $node->get('field_news_category')->isEmpty()) {
      return [];
    }

    $labels = [];
    foreach ($node->get('field_news_category')->referencedEntities() as $term) {
      if (!$term instanceof TermInterface) {
        continue;
      }

      $term = $this->entityRepository->getTranslationFromContext($term);
      $label = trim($term->label() ?? '');
      if ($label !== '') {
        $labels[] = $label;
      }
    }

    return $labels;
  }

  /**
   * @return array{formatted: string, iso: string}
   */
  private function resolveDisplayDateMeta(NodeInterface $node): array {
    $timestamp = NULL;

    if ($node->hasField('field_display_date') && !$node->get('field_display_date')->isEmpty()) {
      $value = $node->get('field_display_date')->value;
      if (is_string($value) && $value !== '') {
        $timestamp = strtotime($value) ?: NULL;
      }
    }

    $timestamp ??= (int) $node->getCreatedTime();
    if ($timestamp <= 0) {
      return ['formatted' => '', 'iso' => ''];
    }

    return [
      'formatted' => $this->dateFormatter->format($timestamp, 'homepage_news_date', $node->language()->getId()),
      'iso' => gmdate('Y-m-d', $timestamp),
    ];
  }

  private function buildExcerpt(NodeInterface $node): string {
    if (!$node->hasField('body') || $node->get('body')->isEmpty()) {
      return '';
    }

    $summary = trim((string) $node->get('body')->summary);
    $text = $summary !== '' ? $summary : trim(strip_tags((string) $node->get('body')->value));
    if ($text === '') {
      return '';
    }

    if (mb_strlen($text) <= self::EXCERPT_LENGTH) {
      return $text;
    }

    $trimmed = mb_substr($text, 0, self::EXCERPT_LENGTH);
    $lastSpace = mb_strrpos($trimmed, ' ');
    if ($lastSpace !== FALSE && $lastSpace > (int) (self::EXCERPT_LENGTH * 0.6)) {
      $trimmed = mb_substr($trimmed, 0, $lastSpace);
    }

    return rtrim($trimmed, '.,;:!?') . '…';
  }

}
