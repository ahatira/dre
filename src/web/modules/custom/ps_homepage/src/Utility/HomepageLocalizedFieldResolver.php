<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Resolves EN/FR editorial fields from homepage block configuration.
 */
final class HomepageLocalizedFieldResolver {

  /**
   * Returns the localized value for a keyed field.
   */
  public static function resolve(array $config, string $key, ?string $langcode = NULL): string {
    $langcode ??= HomepageContent::langcode();
    $localized = $config[$key . '_' . $langcode] ?? NULL;
    if (is_string($localized) && $localized !== '') {
      return $localized;
    }

    $fallback = $config[$key . '_en'] ?? '';
    return is_string($fallback) ? $fallback : '';
  }

  /**
   * @return array{title: string, subtitle: string}
   */
  public static function resolveHeading(array $config, ?string $langcode = NULL): array {
    return [
      'title' => self::resolve($config, 'title', $langcode),
      'subtitle' => self::resolve($config, 'subtitle', $langcode),
    ];
  }

  /**
   * @return array{label: string, url: string}
   */
  public static function resolveFooterCta(array $config, ?string $langcode = NULL): array {
    return [
      'label' => self::resolve($config, 'see_more_label', $langcode),
      'url' => self::resolve($config, 'see_more_url', $langcode),
    ];
  }

}
