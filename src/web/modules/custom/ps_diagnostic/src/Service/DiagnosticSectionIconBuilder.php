<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Builds the offer diagnostics section heading icon from module settings.
 */
final class DiagnosticSectionIconBuilder implements CacheableDependencyInterface {

  private const DEFAULT_ICON = 'bnp_custom:energy-cons';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Builds a render array for the diagnostics section heading icon.
   *
   * @param array<string, mixed> $settings
   *   Optional icon display settings (size, color, etc.).
   *
   * @return array<string, mixed>
   *   Render array for the icon.
   */
  public function buildRenderable(array $settings = []): array {
    $icon_id = trim((string) ($this->configFactory->get('ps_diagnostic.settings')->get('section_icon') ?? ''));
    if ($icon_id === '') {
      $icon_id = self::DEFAULT_ICON;
    }

    $parts = IconIdUtility::splitIconId($icon_id);
    if ($parts === NULL) {
      $parts = IconIdUtility::resolveParts(self::DEFAULT_ICON, 'bnp_custom', 'energy-cons');
    }

    $icon_settings = $settings + [
      'size' => '24px',
      'alt' => '',
    ];

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-offer-section__title-icon']],
      'icon' => IconDefinition::getRenderable($parts['pack'] . ':' . $parts['id'], $icon_settings),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return $this->configFactory->get('ps_diagnostic.settings')->getCacheTags();
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
