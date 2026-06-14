<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Builds the search shortcuts grid body (§5 Sélection recherches).
 */
final class SearchShortcutsBuilder {

  public function __construct(
    private readonly SearchPresetQueryBuilder $presetQueryBuilder,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array<string, mixed>
   */
  public function build(array $configuration): array {
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();

    $columns = [];
    foreach ($configuration['items'] ?? [] as $item) {
      if (!is_array($item)) {
        continue;
      }

      $title = trim((string) ($item['title'] ?? ''));
      if ($title === '') {
        continue;
      }

      $linkLabel = trim((string) ($item['link_label'] ?? ''));
      $linkUrl = $this->resolveShortcutUrl($item, $langcode);
      if ($linkLabel === '' || $linkUrl === '') {
        continue;
      }

      $iconParts = IconIdUtility::resolveParts($item['icon'] ?? '', 'bnp_custom', 'offices');
      $columns[] = [
        '#type' => 'container',
        '#attributes' => ['class' => ['col-12', 'col-md-6', 'col-lg-3']],
        'card' => [
          '#type' => 'component',
          '#component' => 'ps_theme:search-shortcut-card',
          '#props' => [
            'icon_pack' => $iconParts['pack'],
            'icon_id' => $iconParts['id'],
            'title' => $title,
            'link_label' => $linkLabel,
            'link_url' => $linkUrl,
          ],
        ],
      ];
    }

    if ($columns === []) {
      return ['#markup' => ''];
    }

    return [
      'grid' => [
        '#type' => 'container',
        '#attributes' => ['class' => ['row', 'g-4', 'ps-homepage-shortcuts__grid']],
      ] + $columns,
      '#cache' => [
        'contexts' => ['languages:language_interface'],
        'tags' => ['config:block.block'],
      ],
    ];
  }

  /**
   * @param array<string, mixed> $item
   */
  private function resolveShortcutUrl(array $item, string $langcode): string {
    $linkType = (string) ($item['link_type'] ?? 'search_preset');
    if ($linkType === 'custom_url') {
      $url = trim((string) ($item['url'] ?? ''));
      return $url !== '' ? Url::fromUserInput($url)->toString() : '';
    }

    return $this->presetQueryBuilder->buildUrl(
      isset($item['preset_operation']) ? (string) $item['preset_operation'] : NULL,
      isset($item['preset_asset']) ? (string) $item['preset_asset'] : NULL,
      isset($item['preset_locality']) ? (string) $item['preset_locality'] : NULL,
      $langcode,
    );
  }

}
