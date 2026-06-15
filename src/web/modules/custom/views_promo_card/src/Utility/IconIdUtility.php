<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Utility;

use Drupal\Core\Theme\Icon\IconDefinition;
use Drupal\Core\Theme\Icon\IconDefinitionInterface;

/**
 * Helpers for normalizing UI Icons pack:id values.
 */
final class IconIdUtility {

  /**
   * Extracts a pack:id icon value from an icon picker submission.
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
    $iconId = is_string($value) && $value !== '' ? $value : '';
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

}
