<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Editorial field keys and defaults for the homepage search hero block.
 */
final class HomepageSearchHeroEditorial {

  /**
   * Text fields stored as {key}_en / {key}_fr in block configuration.
   *
   * @var list<string>
   */
  public const TEXT_KEYS = [
    'title',
    'transaction_type_label',
    'op_buy_label',
    'op_rent_label',
    'op_flexible_label',
    'location_label',
    'location_placeholder',
    'property_type_label',
    'property_type_placeholder',
    'surface_min_label',
    'surface_min_placeholder',
    'price_max_label',
    'price_max_placeholder',
    'optional_label',
    'search_button_label',
    'delegate_prompt',
    'delegate_button_label',
    'promo_title',
    'promo_offers_line',
    'promo_description',
    'promo_cta_label',
  ];

  /**
   * URL fields stored as {key}_en / {key}_fr.
   *
   * @var list<string>
   */
  public const URL_KEYS = [
    'delegate_url',
    'promo_cta_url',
  ];

  /**
   * @return array<string, mixed>
   *   Default block plugin configuration.
   */
  public static function defaultBlockConfiguration(): array {
    $config = [
      'background_image' => NULL,
      'promo_background_image' => NULL,
      'background_alt' => '',
      'promo_background_alt' => '',
    ];

    foreach (self::TEXT_KEYS as $key) {
      $config[$key . '_en'] = '';
      $config[$key . '_fr'] = '';
    }
    foreach (self::URL_KEYS as $key) {
      $config[$key . '_en'] = '';
      $config[$key . '_fr'] = '';
    }

    return $config;
  }

  /**
   * Resolves localized editorial values for rendering.
   *
   * @param array<string, mixed> $config
   *   Block plugin configuration.
   * @param string $langcode
   *   Interface language code.
   *
   * @return array<string, string>
   */
  public static function resolve(array $config, string $langcode): array {
    $resolved = [];

    foreach (self::TEXT_KEYS as $key) {
      $resolved[$key] = self::localizedValue($config, $key, $langcode, 'heroSearch', $key);
    }

    foreach (self::URL_KEYS as $key) {
      $resolved[$key] = self::localizedValue($config, $key, $langcode, 'heroSearch', $key);
    }

    $resolved['background_alt'] = trim((string) ($config['background_alt'] ?? ''));
    if ($resolved['background_alt'] === '') {
      $resolved['background_alt'] = $resolved['title'];
    }

    $resolved['promo_background_alt'] = trim((string) ($config['promo_background_alt'] ?? ''));
    if ($resolved['promo_background_alt'] === '') {
      $resolved['promo_background_alt'] = $resolved['promo_title'];
    }

    return $resolved;
  }

  /**
   * @param array<string, mixed> $config
   */
  private static function localizedValue(
    array $config,
    string $key,
    string $langcode,
    string $demoSection,
    string $demoKey,
  ): string {
    $field = $key . '_' . $langcode;
    $value = trim((string) ($config[$field] ?? ''));
    if ($value !== '') {
      return $value;
    }

    $fallback = trim((string) ($config[$key . '_en'] ?? ''));
    if ($fallback !== '') {
      return $fallback;
    }

    return HomepageContent::heroSearchField($demoKey, $langcode);
  }

}
