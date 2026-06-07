<?php

declare(strict_types=1);

namespace Drupal\ps_media\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\Icon\IconDefinition;

/**
 * Resolves configured gallery badge icons for the hero component.
 */
final class GalleryBadgeIconResolver implements CacheableDependencyInterface {

  private const DEFAULT_ICONS = [
    'photos' => ['pack' => 'bnp_custom', 'id' => 'camera'],
    'videos' => ['pack' => 'bnp_custom', 'id' => 'video'],
    'visit_3d' => ['pack' => 'bnp_custom', 'id' => 'visite-guidee'],
    'plan' => ['pack' => 'bnp_custom', 'id' => 'floors'],
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Resolves all gallery badge icons from configuration.
   *
   * @return array<string, array{pack: string, id: string}>
   *   Badge icon definitions keyed by badge type.
   */
  public function resolve(): array {
    $config = $this->configFactory->get('ps_media.gallery_settings');
    $icons = [];

    foreach (self::DEFAULT_ICONS as $badge => $defaults) {
      $configKey = 'badge_icon_' . $badge;
      $icons[$badge] = $this->parseIcon(
        $config->get($configKey),
        $defaults['pack'],
        $defaults['id'],
      );
    }

    return $icons;
  }

  /**
   * Parses a stored icon value with fallback defaults.
   *
   * @return array{pack: string, id: string}
   *   Parsed icon pack and id.
   */
  private function parseIcon(mixed $value, string $defaultPack, string $defaultId): array {
    if (!is_string($value) || $value === '') {
      return ['pack' => $defaultPack, 'id' => $defaultId];
    }

    $iconData = IconDefinition::getIconDataFromId($value);
    if ($iconData === NULL) {
      return ['pack' => $defaultPack, 'id' => $defaultId];
    }

    return [
      'pack' => $iconData['pack_id'],
      'id' => $iconData['icon_id'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return $this->configFactory->get('ps_media.gallery_settings')->getCacheTags();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge(): int {
    return Cache::PERMANENT;
  }

}
