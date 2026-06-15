<?php

declare(strict_types=1);

namespace Drupal\ps_market_study\Service;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ExtensionPathResolver;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\ps_content\Service\ContentMediaResolver;

/**
 * Builds the market studies grid body render array (§8 Études).
 */
final class MarketStudiesGridBuilder {

  private const DEFAULT_ITEMS = 2;

  public function __construct(
    private readonly ContentMediaResolver $mediaResolver,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly MarketStudyTeaserBuilder $teaserBuilder,
    private readonly MarketStudyDefaultItems $defaultItems,
  ) {}

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  public function build(array $configuration): array {
    $studies = $configuration['studies'] ?? [];
    if (!is_array($studies) || $studies === []) {
      $studies = $this->defaultItems->resolve(self::DEFAULT_ITEMS);
    }

    if ($studies !== []) {
      $result = $this->buildFromStudies($studies);
      if ($result['body'] !== ['#markup' => '']) {
        return $result;
      }
    }

    return $this->buildFromLegacyItems($configuration['items'] ?? []);
  }

  /**
   * @param list<array<string, mixed>> $studies
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  private function buildFromStudies(array $studies): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $items = [];
    $cacheTags = ['config:block.block', 'node_list:market_study'];

    usort($studies, static fn (array $a, array $b): int => ((int) ($a['weight'] ?? 0)) <=> ((int) ($b['weight'] ?? 0)));

    foreach ($studies as $row) {
      $nid = (int) ($row['nid'] ?? 0);
      if ($nid <= 0) {
        continue;
      }
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      if (!$node instanceof NodeInterface || !$node->isPublished() || $node->bundle() !== 'market_study') {
        continue;
      }
      if ($node->hasTranslation($langcode)) {
        $node = $node->getTranslation($langcode);
      }

      $props = $this->teaserBuilder->build($node);
      if ($props['title'] === '' || $props['url'] === '') {
        continue;
      }

      $cacheTags[] = 'node:' . $nid;
      $items[] = $this->buildCardItem($props);
    }

    return $this->wrapItems($items, $cacheTags);
  }

  /**
   * @param list<array<string, mixed>> $legacyItems
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  private function buildFromLegacyItems(array $legacyItems): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $items = [];
    $cacheTags = ['config:block.block'];

    foreach ($legacyItems as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['title'] ?? ''));
      $url = trim((string) ($item['url'] ?? ''));
      if ($title === '' || $url === '') {
        continue;
      }

      $media = $this->mediaResolver->resolve($item['image'] ?? NULL, $langcode);
      $imageUrl = $media->url ?? $this->defaultThemeImageUrl();
      $cacheTags = array_merge($cacheTags, $media->cacheTags);

      $items[] = $this->buildCardItem([
        'image' => $imageUrl,
        'image_alt' => $media->alt !== '' ? $media->alt : $title,
        'category' => trim((string) ($item['category'] ?? '')),
        'category_url' => '',
        'title' => $title,
        'date' => $this->formatLegacyDate((string) ($item['date'] ?? ''), $langcode),
        'url' => Url::fromUserInput($url)->toString(),
        'url_new_tab' => FALSE,
      ]);
    }

    return $this->wrapItems($items, $cacheTags);
  }

  /**
   * @param array<string, mixed> $props
   *
   * @return array<string, mixed>
   */
  private function buildCardItem(array $props): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-homepage-market-studies__item']],
      'card' => [
        '#type' => 'component',
        '#component' => 'ps_market_study:market-study-card',
        '#props' => $props,
      ],
    ];
  }

  /**
   * @param list<array<string, mixed>> $items
   * @param list<string> $cacheTags
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  private function wrapItems(array $items, array $cacheTags): array {
    if ($items === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
      ];
    }

    return [
      'body' => [
        'carousel' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['ps-homepage-market-studies__carousel']],
          'viewport' => [
            '#type' => 'container',
            '#attributes' => ['class' => ['ps-homepage-market-studies__viewport']],
            'track' => [
              '#type' => 'container',
              '#attributes' => ['class' => ['ps-homepage-market-studies__track']],
            ] + $items,
          ],
        ],
      ],
      'attached' => [
        'library' => ['ps_market_study/market_studies_grid'],
      ],
      'cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => array_values(array_unique($cacheTags)),
      ],
    ];
  }

  private function defaultThemeImageUrl(): string {
    $themePath = $this->extensionPathResolver->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/assets/images/hero/hero-profile.png')->toString();
  }

  private function formatLegacyDate(string $date, string $langcode): string {
    if ($date === '') {
      return '';
    }
    $timestamp = strtotime($date);
    if ($timestamp === FALSE) {
      return $date;
    }
    return $this->dateFormatter->format($timestamp, 'homepage_market_study_date', '', NULL, $langcode);
  }

}
