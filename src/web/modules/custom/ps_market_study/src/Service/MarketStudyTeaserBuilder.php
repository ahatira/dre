<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Service;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
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
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @return array<string, string>
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

    return [
      'image' => $imageUrl,
      'image_alt' => $media->alt !== '' ? $media->alt : $title,
      'image_credit' => $media->credit,
      'category' => $this->resolveCategoryLabel($node),
      'title' => $title,
      'date' => $this->formatDisplayDate($node, $langcode),
      'url' => $node->toUrl()->toString(),
    ];
  }

  private function resolveCategoryLabel(NodeInterface $node): string {
    if (!$node->hasField('field_study_category') || $node->get('field_study_category')->isEmpty()) {
      return '';
    }
    $term = $node->get('field_study_category')->entity;
    return $term instanceof TermInterface ? $term->label() ?? '' : '';
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
    return $this->dateFormatter->format($timestamp, 'medium', '', NULL, $langcode);
  }

  private function defaultThemeImageUrl(): string {
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/assets/images/hero/hero-profile.png')->toString();
  }

}
