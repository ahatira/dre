<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Service;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Extension\ExtensionPathResolver;

/**
 * Loads per-language default configuration for homepage LB blocks.
 */
final class HomepageBlockDefaultsLoader {

  /**
   * @var array<string, mixed>|null
   */
  private ?array $defaults = NULL;

  public function __construct(
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly EntityRepositoryInterface $entityRepository,
  ) {}

  /**
   * Returns default block configuration for a plugin and language.
   *
   * @return array<string, mixed>
   */
  public function forPlugin(string $pluginId, string $langcode): array {
    $all = $this->loadDefaults();
    $defaultLang = \Drupal::languageManager()->getDefaultLanguage()->getId();
    $fallbacks = match ($defaultLang) {
      'en' => ['en', 'fr'],
      'fr' => ['fr', 'en'],
      default => [$defaultLang, 'fr', 'en'],
    };
    $candidates = array_unique([
      $langcode,
      $this->regionalAlias($langcode),
      ...$fallbacks,
    ], SORT_STRING);

    foreach ($candidates as $candidate) {
      $specific = $all[$pluginId][$candidate] ?? NULL;
      if (!is_array($specific)) {
        continue;
      }
      if ($candidate === 'en') {
        return $this->resolveMediaUuids($specific);
      }
      $base = $all[$pluginId]['fr'] ?? [];
      if (!is_array($base) || $base === []) {
        $base = $all[$pluginId]['en'] ?? [];
      }
      if (!is_array($base) || $base === []) {
        return $specific;
      }
      // Locale overlays inherit FR (or EN) block structure, not bare EN strings.
      return $this->resolveMediaUuids(array_replace($base, $specific));
    }
    return [];
  }

  private function regionalAlias(string $langcode): string {
    return match ($langcode) {
      'lb' => 'fr',
      default => $langcode,
    };
  }

  /**
   * @return array<string, mixed>
   */
  private function loadDefaults(): array {
    if ($this->defaults !== NULL) {
      return $this->defaults;
    }

    $modulePath = $this->extensionPathResolver->getPath('module', 'ps_homepage');
    $file = $modulePath . '/data/homepage_block_defaults.yml';
    if (!is_readable($file)) {
      $this->defaults = [];
      return $this->defaults;
    }

    $parsed = Yaml::decode((string) file_get_contents($file));
    $this->defaults = is_array($parsed) ? $parsed : [];

    foreach (['es', 'it', 'nl', 'pl', 'de', 'lb'] as $langcode) {
      $overlay = $modulePath . '/data/homepage_block_defaults.' . $langcode . '.yml';
      if (!is_readable($overlay)) {
        continue;
      }
      $overlayParsed = Yaml::decode((string) file_get_contents($overlay));
      if (!is_array($overlayParsed)) {
        continue;
      }
      foreach ($overlayParsed as $pluginId => $langBlocks) {
        if (!is_array($langBlocks)) {
          continue;
        }
        foreach ($langBlocks as $lang => $config) {
          if (is_array($config)) {
            $this->defaults[$pluginId][$lang] = array_replace(
              $this->defaults[$pluginId][$lang] ?? [],
              $config,
            );
          }
        }
      }
    }

    return $this->defaults;
  }

  /**
   * Maps stable demo media UUIDs to runtime media IDs when entities exist.
   *
   * @param array<string, mixed> $config
   *
   * @return array<string, mixed>
   */
  private function resolveMediaUuids(array $config): array {
    $uuidMap = [
      'background_media_uuid' => 'background_image',
      'promo_background_media_uuid' => 'promo_background_image',
      'illustration_media_uuid' => 'illustration',
      'image_media_uuid' => 'image',
    ];

    foreach ($uuidMap as $uuidKey => $targetKey) {
      if (!isset($config[$uuidKey]) || !is_string($config[$uuidKey])) {
        continue;
      }
      try {
        $media = $this->entityRepository->loadEntityByUuid('media', $config[$uuidKey]);
      }
      catch (\Exception) {
        $media = NULL;
      }
      if ($media !== NULL) {
        $config[$targetKey] = (int) $media->id();
      }
      unset($config[$uuidKey]);
    }

    return $config;
  }

}
