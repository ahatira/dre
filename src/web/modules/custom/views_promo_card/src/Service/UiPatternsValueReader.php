<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

/**
 * Reads flat values from stored UI Patterns prop configuration.
 */
final class UiPatternsValueReader {

  /**
   * Returns a string prop value from UI Patterns storage.
   *
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   * @param string $prop_id
   *   Prop machine name.
   */
  public static function getPropValue(array $ui_patterns, string $prop_id): string {
    $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
    $prop = $props[$prop_id] ?? NULL;
    if (!is_array($prop)) {
      return '';
    }
    $source = $prop['source'] ?? NULL;
    if (!is_array($source)) {
      return '';
    }
    return trim((string) ($source['value'] ?? ''));
  }

  /**
   * Returns all prop values as a flat string map.
   *
   * @param array<string, mixed> $ui_patterns
   *   UI Patterns component configuration.
   *
   * @return array<string, string>
   *   Prop values keyed by prop ID.
   */
  public static function getPropValues(array $ui_patterns): array {
    $props = is_array($ui_patterns['props'] ?? NULL) ? $ui_patterns['props'] : [];
    $values = [];
    foreach ($props as $prop_id => $prop) {
      if (!is_string($prop_id)) {
        continue;
      }
      $values[$prop_id] = self::getPropValue(['props' => [$prop_id => $prop]], $prop_id);
    }
    return $values;
  }

}
