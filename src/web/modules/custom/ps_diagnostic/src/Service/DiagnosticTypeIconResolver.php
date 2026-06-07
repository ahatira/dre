<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Service;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\ps_core\Utility\IconIdUtility;

/**
 * Resolves UI Icons for diagnostic type headings.
 */
final class DiagnosticTypeIconResolver implements CacheableDependencyInterface {

  private const FALLBACK_ICON = 'bnp_custom:not-available';

  /**
   * Legacy semantic keys stored before UI Icons migration.
   *
   * @var array<string, string>
   */
  private const LEGACY_ICON_MAP = [
    'energy' => 'bnp_custom:energy-cons',
    'co2' => 'bnp_custom:gas-emission',
  ];

  /**
   * Default icons per diagnostic type machine name.
   *
   * @var array<string, string>
   */
  private const DEFAULT_TYPE_ICONS = [
    'dpe' => 'bnp_custom:energy-cons',
    'ges' => 'bnp_custom:gas-emission',
  ];

  public function __construct(
    private readonly DiagnosticTypeOptionsProvider $typeOptionsProvider,
  ) {}

  /**
   * Builds a render array for a diagnostic type heading icon.
   *
   * @param string $type_id
   *   Diagnostic type machine name.
   * @param array<string, mixed> $settings
   *   Optional icon display settings (size, color, etc.).
   *
   * @return array<string, mixed>
   *   Render array for the icon.
   */
  public function buildRenderable(string $type_id, array $settings = []): array {
    $parts = $this->resolveParts($type_id);
    $icon_settings = $settings + [
      'size' => '24px',
      'alt' => '',
    ];

    $render = IconDefinition::getRenderable($parts['full_id'], $icon_settings);

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['ps-diagnostic__title-icon']],
      'icon' => $render,
    ];
  }

  /**
   * Resolves icon pack/id parts for a diagnostic type.
   *
   * @return array{pack: string, id: string, full_id: string}
   *   Resolved icon identifiers.
   */
  public function resolveParts(string $type_id): array {
    $stored_icon = '';
    $type = $this->typeOptionsProvider->getType($type_id);
    if ($type !== NULL) {
      $stored_icon = $type->getIcon();
    }

    if ($stored_icon !== '') {
      $parts = IconIdUtility::splitIconId($stored_icon);
      if ($parts !== NULL) {
        return [
          'pack' => $parts['pack'],
          'id' => $parts['id'],
          'full_id' => $parts['pack'] . ':' . $parts['id'],
        ];
      }

      $legacy = self::LEGACY_ICON_MAP[strtolower($stored_icon)] ?? '';
      if ($legacy !== '') {
        $legacyParts = IconIdUtility::splitIconId($legacy);
        if ($legacyParts !== NULL) {
          return [
            'pack' => $legacyParts['pack'],
            'id' => $legacyParts['id'],
            'full_id' => $legacyParts['pack'] . ':' . $legacyParts['id'],
          ];
        }
      }
    }

    $default = self::DEFAULT_TYPE_ICONS[strtolower($type_id)] ?? self::FALLBACK_ICON;
    $defaultParts = IconIdUtility::splitIconId($default);

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
   * Returns cache tags for a diagnostic type icon render.
   *
   * @return array<int, string>
   *   Cache tags.
   */
  public function getCacheTagsForType(string $type_id): array {
    $tags = $this->getCacheTags();

    if ($type_id !== '') {
      $tags = Cache::mergeTags($tags, ["config:ps_diagnostic.type.$type_id"]);
    }

    return $tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags(): array {
    return ['config:ps_diagnostic.type.*'];
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
