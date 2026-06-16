<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Reads and migrates monolingual homepage LB block configuration.
 */
final class HomepageBlockConfiguration {

  /**
   * Homepage block plugin IDs managed by this utility.
   *
   * @var list<string>
   */
  public const HOMEPAGE_PLUGIN_IDS = [
    'ps_homepage_search_hero_block',
    'ps_homepage_section_header_block',
    'ps_homepage_section_footer_block',
  ];

  /**
   * Domain body blocks placed in homepage S-D section layouts.
   *
   * @var list<string>
   */
  public const DOMAIN_BODY_PLUGIN_IDS = [
    'ps_content_services_grid_block',
    'ps_content_outils_accordion_block',
    'ps_offer_offers_carousel_block',
    'ps_search_search_shortcuts_block',
    'ps_content_experts_accompagnement_block',
    'ps_news_news_block',
    'ps_market_study_market_studies_block',
    'ps_faq_faq_block',
  ];

  /**
   * Repeatable item list keys per plugin.
   *
   * @var array<string, string>
   */
  private const ITEM_LIST_KEYS = [
    'ps_content_services_grid_block' => 'items',
    'ps_content_outils_accordion_block' => 'items',
    'ps_search_search_shortcuts_block' => 'items',
    'ps_market_study_market_studies_block' => 'studies',
    'ps_content_experts_accompagnement_block' => 'steps',
    'ps_offer_offers_carousel_block' => 'offers',
    'ps_faq_faq_block' => 'faq_items',
  ];

  /**
   * Top-level keys that stay identical across translations.
   *
   * @var list<string>
   */
  private const NEUTRAL_TOP_LEVEL_KEYS = [
    'id',
    'provider',
    'label',
    'label_display',
    'uuid',
    'background_image',
    'promo_background_image',
    'promo_offers_use_dynamic',
    'illustration',
    'max_visible',
    'show_favorite',
    'show_compare',
    'autoplay',
    'items_count',
  ];

  /**
   * Item-level keys that stay identical across translations.
   *
   * @var list<string>
   */
  private const NEUTRAL_ITEM_KEYS = [
    'weight',
    'image',
    'icon',
    'illustration',
    'link_type',
    'button_style',
    'modal_id',
    'preset_operation',
    'preset_asset',
    'preset_locality',
    'opened_by_default',
    'date',
    'nid',
    'remove',
  ];

  /**
   * @return array{title: string, subtitle: string}
   */
  public static function heading(array $config): array {
    return [
      'title' => self::string($config, 'title'),
      'subtitle' => self::string($config, 'subtitle'),
    ];
  }

  /**
   * @return array{label: string, url: string}
   */
  public static function footerCta(array $config): array {
    return [
      'label' => self::string($config, 'see_more_label'),
      'url' => self::string($config, 'see_more_url'),
    ];
  }

  public static function string(array $config, string $key): string {
    return trim((string) ($config[$key] ?? ''));
  }

  /**
   * Converts legacy bilingual config to monolingual config for a layout.
   *
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  public static function migrateForLanguage(array $config, string $langcode, ?string $pluginId = NULL): array {
    if (!self::usesLegacyLocalizedKeys($config)) {
      return $config;
    }

    $pluginId ??= (string) ($config['id'] ?? '');
    $suffix = '_' . $langcode;
    $migrated = [];

    foreach ($config as $key => $value) {
      if (self::isLocalizedKey($key)) {
        $base = self::baseKey($key);
        if (str_ends_with($key, $suffix)) {
          $migrated[$base] = $value;
        }
        continue;
      }

      if (is_array($value) && self::isItemListKey($key)) {
        $migrated[$key] = array_values(array_map(
          static fn (mixed $item): mixed => is_array($item)
            ? self::migrateItemForLanguage($item, $langcode)
            : $item,
          $value,
        ));
        continue;
      }

      $migrated[$key] = $value;
    }

    return $migrated;
  }

  /**
   * Merges language-neutral values from source into target configuration.
   *
   * @param array<string, mixed> $target
   * @param array<string, mixed> $source
   *
   * @return array<string, mixed>
   */
  public static function applyNeutralValues(array $target, array $source, ?string $pluginId = NULL): array {
    $pluginId ??= (string) ($target['id'] ?? $source['id'] ?? '');

    foreach (self::NEUTRAL_TOP_LEVEL_KEYS as $key) {
      if (array_key_exists($key, $source)) {
        $target[$key] = $source[$key];
      }
    }

    $listKey = self::ITEM_LIST_KEYS[$pluginId] ?? NULL;
    if ($listKey === NULL || !isset($source[$listKey]) || !is_array($source[$listKey])) {
      return $target;
    }

    $targetItems = isset($target[$listKey]) && is_array($target[$listKey]) ? $target[$listKey] : [];
    $sourceItems = $source[$listKey];
    $mergedItems = [];

    foreach ($sourceItems as $index => $sourceItem) {
      if (!is_array($sourceItem)) {
        continue;
      }
      $targetItem = isset($targetItems[$index]) && is_array($targetItems[$index])
        ? $targetItems[$index]
        : [];
      $mergedItems[] = self::applyNeutralItemValues($targetItem, $sourceItem);
    }

    $target[$listKey] = $mergedItems;
    return $target;
  }

  public static function isHomepagePlugin(string $pluginId): bool {
    return in_array($pluginId, self::HOMEPAGE_PLUGIN_IDS, TRUE);
  }

  public static function isDomainBodyPlugin(string $pluginId): bool {
    return in_array($pluginId, self::DOMAIN_BODY_PLUGIN_IDS, TRUE);
  }

  public static function shouldSynchronizeBlockConfiguration(string $pluginId): bool {
    return self::isHomepagePlugin($pluginId) || self::isDomainBodyPlugin($pluginId);
  }

  /**
   * Keeps translated copy from the target when structure sync runs.
   *
   * @param array<string, mixed> $target
   * @param array<string, mixed> $merged
   *
   * @return array<string, mixed>
   */
  public static function preserveTranslatableValues(array $target, array $merged, ?string $pluginId = NULL): array {
    $pluginId ??= (string) ($merged['id'] ?? $target['id'] ?? '');

    foreach (['title', 'subtitle', 'see_more_label', 'see_more_url'] as $key) {
      if (isset($target[$key]) && is_string($target[$key]) && $target[$key] !== '') {
        $merged[$key] = $target[$key];
      }
    }

    $listKey = self::ITEM_LIST_KEYS[$pluginId] ?? NULL;
    if ($listKey === NULL || !isset($target[$listKey]) || !is_array($target[$listKey])) {
      return $merged;
    }

    $mergedItems = isset($merged[$listKey]) && is_array($merged[$listKey]) ? $merged[$listKey] : [];
    foreach ($target[$listKey] as $index => $targetItem) {
      if (!is_array($targetItem) || !isset($mergedItems[$index]) || !is_array($mergedItems[$index])) {
        continue;
      }
      foreach (['card_title', 'body', 'button_label', 'question', 'answer', 'link_label', 'step_label', 'step_title', 'step_body'] as $key) {
        if (isset($targetItem[$key]) && is_string($targetItem[$key]) && $targetItem[$key] !== '') {
          $mergedItems[$index][$key] = $targetItem[$key];
        }
      }
    }
    $merged[$listKey] = $mergedItems;

    return $merged;
  }

  /**
   * @param array<string, mixed> $config
   */
  public static function usesLegacyLocalizedKeys(array $config): bool {
    foreach (array_keys($config) as $key) {
      if (self::isLocalizedKey((string) $key)) {
        return TRUE;
      }
    }

    foreach (self::ITEM_LIST_KEYS as $listKey) {
      if (!isset($config[$listKey]) || !is_array($config[$listKey])) {
        continue;
      }
      foreach ($config[$listKey] as $item) {
        if (!is_array($item)) {
          continue;
        }
        foreach (array_keys($item) as $itemKey) {
          if (self::isLocalizedKey((string) $itemKey)) {
            return TRUE;
          }
        }
      }
    }

    return FALSE;
  }

  /**
   * @param array<string, mixed> $item
   *
   * @return array<string, mixed>
   */
  private static function migrateItemForLanguage(array $item, string $langcode): array {
    $suffix = '_' . $langcode;
    $migrated = [];

    foreach ($item as $key => $value) {
      if (self::isLocalizedKey((string) $key)) {
        $base = self::baseKey((string) $key);
        if (str_ends_with((string) $key, $suffix)) {
          $migrated[$base] = $value;
        }
        continue;
      }
      $migrated[$key] = $value;
    }

    return $migrated;
  }

  /**
   * @param array<string, mixed> $target
   * @param array<string, mixed> $source
   *
   * @return array<string, mixed>
   */
  private static function applyNeutralItemValues(array $target, array $source): array {
    foreach (self::NEUTRAL_ITEM_KEYS as $key) {
      if (array_key_exists($key, $source)) {
        $target[$key] = $source[$key];
      }
    }
    return $target;
  }

  private static function isItemListKey(string $key): bool {
    return in_array($key, self::ITEM_LIST_KEYS, TRUE);
  }

  private static function isLocalizedKey(string $key): bool {
    return str_ends_with($key, '_en') || str_ends_with($key, '_fr');
  }

  private static function baseKey(string $key): string {
    return (string) preg_replace('/_(en|fr)$/', '', $key);
  }

}
