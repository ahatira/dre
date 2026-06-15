<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Service;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\file\FileInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_content\Service\ContentMediaResolver;
use Drupal\taxonomy\TermInterface;

/**
 * Builds props for the market-study-card SDC from a market_study node.
 */
final class MarketStudyTeaserBuilder {

  public function __construct(
    private readonly ContentMediaResolver $mediaResolver,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly EntityRepositoryInterface $entityRepository,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Builds card props for the market-study-card SDC.
   *
   * @return array<string, mixed>
   *   Card props.
   */
  public function build(NodeInterface $node): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $node = $this->entityRepository->getTranslationFromContext($node, $langcode);
    $title = $node->label() ?? '';

    $mediaMid = NULL;
    if ($node->hasField('field_teaser_image') && !$node->get('field_teaser_image')->isEmpty()) {
      $mediaMid = (int) $node->get('field_teaser_image')->target_id;
    }
    $media = $this->mediaResolver->resolve($mediaMid, $langcode);
    $imageUrl = $media->url ?? $this->defaultThemeImageUrl();
    $link = $this->resolveCardLink($node);

    return [
      'image' => $imageUrl,
      'image_alt' => $media->alt !== '' ? $media->alt : $title,
      'category' => $this->resolveCategoryLabel($node),
      'category_url' => $this->resolveCategoryUrl($node),
      'title' => $title,
      'date' => $this->formatDisplayDate($node, $langcode),
      'url' => $link['url'],
      'url_new_tab' => $link['new_tab'],
    ];
  }

  /**
   * @return array{url: string, new_tab: bool}
   */
  private function resolveCardLink(NodeInterface $node): array {
    $documentUrl = $this->resolveDocumentUrl($node);
    if ($documentUrl !== '') {
      return [
        'url' => $documentUrl,
        'new_tab' => TRUE,
      ];
    }

    return [
      'url' => $node->toUrl()->toString(),
      'new_tab' => FALSE,
    ];
  }

  private function resolveDocumentUrl(NodeInterface $node): string {
    if (!$node->hasField('field_study_document') || $node->get('field_study_document')->isEmpty()) {
      return '';
    }

    $file = $node->get('field_study_document')->entity;
    if (!$file instanceof FileInterface) {
      return '';
    }

    return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
  }

  private function resolveCategoryLabel(NodeInterface $node): string {
    if (!$node->hasField('field_study_category') || $node->get('field_study_category')->isEmpty()) {
      return '';
    }
    $term = $node->get('field_study_category')->entity;
    return $term instanceof TermInterface ? $term->label() ?? '' : '';
  }

  private function resolveCategoryUrl(NodeInterface $node): string {
    if (!$node->hasField('field_study_category') || $node->get('field_study_category')->isEmpty()) {
      return '';
    }
    $term = $node->get('field_study_category')->entity;
    if (!$term instanceof TermInterface) {
      return '';
    }

    return $term->toUrl()->toString();
  }

  private function formatDisplayDate(NodeInterface $node, string $langcode): string {
    if (!$node->hasField('field_display_date') || $node->get('field_display_date')->isEmpty()) {
      return '';
    }
    $value = (string) $node->get('field_display_date')->value;
    $timestamp = strtotime($value);
    if ($timestamp === FALSE) {
      return $value;
    }

    return $this->dateFormatter->format($timestamp, 'homepage_market_study_date', '', NULL, $langcode);
  }

  private function defaultThemeImageUrl(): string {
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/assets/images/hero/hero-profile.png')->toString();
  }

}
