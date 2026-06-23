<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

/**
 * Maps canonical geo zone ids to Drupal config-safe storage keys.
 *
 * Drupal config mapping keys cannot contain dots; zone ids use dots
 * (e.g. department.fr.75). We encode dots as double underscores.
 */
final class GeoZoneConfigKeys {

  private const DOT_REPLACEMENT = '__';

  /**
   * Encodes a canonical zone id for use as a config mapping key.
   */
  public static function encodeStorageKey(string $id): string {
    return str_replace('.', self::DOT_REPLACEMENT, $id);
  }

  /**
   * Decodes a config mapping key back to the canonical zone id.
   */
  public static function decodeStorageKey(string $storageKey): string {
    return str_replace(self::DOT_REPLACEMENT, '.', $storageKey);
  }

}
