<?php

declare(strict_types=1);

namespace Drupal\ps_core\Utility;

use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\Core\Theme\Icon\IconDefinitionInterface;

/**
 * Helpers for normalizing UI Icons pack:id values.
 */
final class IconIdUtility {

  /**
   * Default BNP custom icon pack used across Property Search.
   */
  public const DEFAULT_BNP_PACK = 'bnp_custom';

  /**
   * Returns a stored icon id or the configured fallback.
   */
  public static function normalizeStoredIcon(mixed $value, string $fallback): string {
    if (is_string($value) && $value !== '') {
      return $value;
    }

    return $fallback;
  }

  /**
   * Extracts a pack:id icon value from an icon_autocomplete submission.
   */
  public static function extractFromSubmission(mixed $value, string $fallback = ''): string {
    if (is_string($value) && $value !== '') {
      return $value;
    }

    if (!is_array($value)) {
      return $fallback;
    }

    if (!empty($value['target_id']) && is_string($value['target_id'])) {
      return $value['target_id'];
    }

    if (!empty($value['icon_id']) && is_string($value['icon_id'])) {
      return $value['icon_id'];
    }

    if (!empty($value['icon']) && $value['icon'] instanceof IconDefinitionInterface) {
      return $value['icon']->getId();
    }

    if (!empty($value['object']) && $value['object'] instanceof IconDefinitionInterface) {
      return $value['object']->getId();
    }

    return $fallback;
  }

  /**
   * Parses a pack:id string into pack and icon machine names.
   *
   * @return array{pack: string, id: string}|null
   *   Parsed icon parts, or NULL when invalid.
   */
  public static function splitIconId(string $iconId): ?array {
    if ($iconId === '') {
      return NULL;
    }

    $iconData = IconDefinition::getIconDataFromId($iconId);
    if ($iconData === NULL) {
      return NULL;
    }

    return [
      'pack' => $iconData['pack_id'],
      'id' => $iconData['icon_id'],
    ];
  }

  /**
   * Parses a stored icon with fallback pack/id parts.
   *
   * @return array{pack: string, id: string, full_id: string}
   *   Resolved icon identifiers.
   */
  public static function resolveParts(mixed $value, string $defaultPack, string $defaultId): array {
    $iconId = self::normalizeStoredIcon($value, '');
    $parts = $iconId !== '' ? self::splitIconId($iconId) : NULL;

    if ($parts === NULL) {
      return [
        'pack' => $defaultPack,
        'id' => $defaultId,
        'full_id' => $defaultPack . ':' . $defaultId,
      ];
    }

    return [
      'pack' => $parts['pack'],
      'id' => $parts['id'],
      'full_id' => $parts['pack'] . ':' . $parts['id'],
    ];
  }

  /**
   * Maps icon arrays for SDC component props (id → icon_id).
   *
   * JSON Schema property names must not use "id" — ui_patterns
   * ReferencesResolver treats it as a schema URI keyword.
   *
   * @param array<string, array{pack: string, id: string}> $icons
   *   Icon maps using internal pack/id keys.
   *
   * @return array<string, array{pack: string, icon_id: string}>
   *   Icon definitions keyed for component props.
   */
  public static function forComponentProp(array $icons): array {
    $mapped = [];
    foreach ($icons as $key => $icon) {
      $mapped[$key] = [
        'pack' => (string) ($icon['pack'] ?? self::DEFAULT_BNP_PACK),
        'icon_id' => (string) ($icon['icon_id'] ?? $icon['id'] ?? ''),
      ];
    }
    return $mapped;
  }

}
