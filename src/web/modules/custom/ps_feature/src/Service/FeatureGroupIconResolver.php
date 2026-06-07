<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\ps_core\Utility\IconIdUtility;
use Drupal\ps_feature\Entity\FeatureGroup;

/**
 * Resolves UI Icons for feature group headings.
 */
final class FeatureGroupIconResolver implements CacheableDependencyInterface {

  private const FALLBACK_ICON = 'bnp_custom:not-available';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Builds a render array for a feature group heading icon.
   *
   * @param string $group_id
   *   Feature group machine name, or "_other".
   * @param array<string, mixed> $settings
   *   Optional icon display settings (size, color, etc.).
   *
   * @return array<string, mixed>
   *   Render array for the icon.
   */
  public function buildRenderable(string $group_id, array $settings = []): array {
    $parts = $this->resolveParts($group_id);
    $icon_settings = $settings + [
      'size' => '24px',
      'alt' => '',
    ];

    $render = IconDefinition::getRenderable($parts['full_id'], $icon_settings);

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['feature-grouped-title__icon']],
      'icon' => $render,
    ];
  }

  /**
   * Resolves icon pack/id parts for a feature group.
   *
   * @return array{pack: string, id: string, full_id: string}
   *   Resolved icon identifiers.
   */
  public function resolveParts(string $group_id): array {
    $group_icon = '';
    if ($group_id !== '' && $group_id !== '_other') {
      $group = $this->entityTypeManager->getStorage('fb_feature_group')->load($group_id);
      if ($group instanceof FeatureGroup) {
        $group_icon = $group->getIcon();
      }
    }

    if ($group_icon !== '') {
      $parts = IconIdUtility::splitIconId($group_icon);
      if ($parts !== NULL) {
        return [
          'pack' => $parts['pack'],
          'id' => $parts['id'],
          'full_id' => $parts['pack'] . ':' . $parts['id'],
        ];
      }
    }

    $default = $this->configFactory->get('ps_feature.settings')->get('default_group_icon') ?? self::FALLBACK_ICON;
    $defaultParts = IconIdUtility::splitIconId((string) $default);

    if ($defaultParts !== NULL) {
      return [
        'pack' => $defaultParts['pack'],
        'id' => $defaultParts['id'],
        'full_id' => $defaultParts['pack'] . ':' . $defaultParts['id'],
      ];
    }

    return IconIdUtility::resolveParts(self::FALLBACK_ICON, 'bnp_custom', 'not-available');
  }

  /**
   * Returns cache tags for a feature group icon render.
   *
   * @return array<int, string>
   *   Cache tags.
   */
  public function getCacheTagsForGroup(string $group_id): array {
    $tags = $this->getCacheTags();

    if ($group_id !== '' && $group_id !== '_other') {
      $tags = Cache::mergeTags($tags, ["config:ps_feature.feature_group.$group_id"]);
    }

    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return $this->configFactory->get('ps_feature.settings')->getCacheTags();
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
