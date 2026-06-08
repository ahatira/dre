<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Reads homepage block copy from ps_demo.homepage config (EN/FR).
 */
final class HomepageContent {

  /**
   * @var array<string, mixed>|null
   */
  private static ?array $data = NULL;

  /**
   * @return array<string, mixed>
   */
  private static function data(): array {
    if (self::$data !== NULL) {
      return self::$data;
    }

    if (!\Drupal::moduleHandler()->moduleExists('ps_demo')) {
      self::$data = [];
      return self::$data;
    }

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $configFactory */
    $configFactory = \Drupal::service('config.factory');
    self::$data = $configFactory->get('ps_demo.homepage')->getRawData();
    return self::$data;
  }

  public static function langcode(?LanguageManagerInterface $languageManager = NULL): string {
    $languageManager ??= \Drupal::languageManager();
    return $languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
  }

  public static function heroTitle(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    return (string) (self::data()['hero']['titles'][$langcode] ?? self::data()['hero']['titles']['en'] ?? '');
  }

  public static function heroSubtitle(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    return (string) (self::data()['hero']['subtitles'][$langcode] ?? self::data()['hero']['subtitles']['en'] ?? '');
  }

  public static function featuredTitle(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    return (string) (self::data()['featured']['titles'][$langcode] ?? self::data()['featured']['titles']['en'] ?? '');
  }

  public static function universTitle(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    return (string) (self::data()['univers']['titles'][$langcode] ?? self::data()['univers']['titles']['en'] ?? '');
  }

  /**
   * @return list<array{label: string, query: string}>
   */
  public static function universItems(?string $langcode = NULL): array {
    $langcode ??= self::langcode();
    $items = self::data()['univers']['items'] ?? [];
    if (!is_array($items)) {
      return [];
    }

    $result = [];
    foreach ($items as $item) {
      if (!is_array($item)) {
        continue;
      }
      $labels = $item['labels'] ?? [];
      $result[] = [
        'label' => (string) ($labels[$langcode] ?? $labels['en'] ?? ''),
        'query' => (string) ($item['query'] ?? ''),
      ];
    }
    return $result;
  }

  public static function editorialTitle(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    return (string) (self::data()['editorial']['titles'][$langcode] ?? self::data()['editorial']['titles']['en'] ?? '');
  }

  public static function editorialBody(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    return (string) (self::data()['editorial']['bodies'][$langcode] ?? self::data()['editorial']['bodies']['en'] ?? '');
  }

  public static function editorialCtaLabel(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    $cta = self::data()['editorial']['cta']['labels'] ?? [];
    return (string) ($cta[$langcode] ?? $cta['en'] ?? '');
  }

  public static function editorialCtaPath(?string $langcode = NULL): string {
    $langcode ??= self::langcode();
    $paths = self::data()['editorial']['cta']['paths'] ?? [];
    return (string) ($paths[$langcode] ?? $paths['en'] ?? '/find-property');
  }

  public static function editorialImageUrl(): string {
    return self::themeAssetUrl((string) (self::data()['editorial']['image'] ?? 'assets/images/hero/hero-profile.png'));
  }

  public static function heroBackgroundUrl(): string {
    $relative = (string) (self::data()['hero']['background'] ?? 'assets/images/hero/hero-homepage.png');
    return self::themeAssetUrl($relative);
  }

  private static function themeAssetUrl(string $relative): string {
    $themePath = \Drupal::service('extension.path.resolver')->getPath('theme', 'ps_theme');
    return Url::fromUri('base:' . $themePath . '/' . ltrim($relative, '/'))->toString();
  }

}
