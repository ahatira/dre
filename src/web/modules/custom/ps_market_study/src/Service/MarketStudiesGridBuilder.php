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

  public function __construct(
    private readonly ContentMediaResolver $mediaResolver,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly MarketStudyTeaserBuilder $teaserBuilder,
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
    if (is_array($studies) && $studies !== []) {
      return $this->buildFromStudies($studies);
    }

    return $this->buildFromLegacyItems($configuration['items'] ?? []);
  }

  /**
   * @param list<array<string, mixed>> $studies
   */
  private function buildFromStudies(array $studies): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $columns = [];
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
      $columns[] = $this->buildCardColumn($props);
    }

    return $this->wrapColumns($columns, $cacheTags);
  }

  /**
   * @param list<array<string, mixed>> $items
   */
  private function buildFromLegacyItems(array $items): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $columns = [];
    $cacheTags = ['config:block.block'];

    foreach ($items as $item) {
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

      $columns[] = $this->buildCardColumn([
        'image' => $imageUrl,
        'image_alt' => $media->alt !== '' ? $media->alt : $title,
        'image_credit' => $media->credit,
        'category' => trim((string) ($item['category'] ?? '')),
        'title' => $title,
        'date' => $this->formatLegacyDate((string) ($item['date'] ?? ''), $langcode),
        'url' => Url::fromUserInput($url)->toString(),
      ]);
    }

    return $this->wrapColumns($columns, $cacheTags);
  }

  /**
   * @param array<string, string> $props
   *
   * @return array<string, mixed>
   */
  private function buildCardColumn(array $props): array {
    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['col-12', 'col-lg-6']],
      'card' => [
        '#type' => 'component',
        '#component' => 'ps_market_study:market-study-card',
        '#props' => $props,
      ],
    ];
  }

  /**
   * @param list<array<string, mixed>> $columns
   * @param list<string> $cacheTags
   *
   * @return array{
   *   body: array<string, mixed>,
   *   cache: array<string, mixed>,
   *   attached: array<string, mixed>
   * }
   */
  private function wrapColumns(array $columns, array $cacheTags): array {
    if ($columns === []) {
      return [
        'body' => ['#markup' => ''],
        'cache' => [],
        'attached' => [],
      ];
    }

    return [
      'body' => [
        'grid' => [
          '#type' => 'container',
          '#attributes' => ['class' => ['row', 'g-4']],
        ] + $columns,
        '#cache' => [
          'contexts' => ['languages:language_interface'],
          'tags' => array_values(array_unique($cacheTags)),
        ],
      ],
      'attached' => [
        'library' => ['ps_content/media_credit'],
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
    return $this->dateFormatter->format($timestamp, 'medium', [], $langcode);
  }

}
