<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Service;

/**
 * Builds UI Patterns prop configuration arrays for SDC components.
 */
final class UiPatternsPropBuilder {

  /**
   * Builds a textfield prop configuration.
   */
  public static function textfield(string $value): array {
    return [
      'source_id' => 'textfield',
      'source' => [
        'value' => $value,
      ],
    ];
  }

  /**
   * Builds a URL prop configuration.
   */
  public static function url(string $value): array {
    return [
      'source_id' => 'url',
      'source' => [
        'value' => $value,
      ],
    ];
  }

  /**
   * Builds a select prop configuration.
   */
  public static function select(string $value): array {
    return [
      'source_id' => 'select',
      'source' => [
        'value' => $value,
      ],
    ];
  }

  /**
   * Builds an attributes prop configuration.
   */
  public static function attributes(string $value): array {
    return [
      'source_id' => 'attributes',
      'source' => [
        'value' => $value,
      ],
    ];
  }

  /**
   * Builds a full UI Patterns component configuration.
   *
   * @param string $pattern_id
   *   SDC pattern ID.
   * @param array<string, array<string, mixed>> $props
   *   Prop configurations keyed by prop name.
   */
  public static function component(string $pattern_id, array $props): array {
    return [
      'component_id' => $pattern_id,
      'variant_id' => NULL,
      'props' => $props,
      'slots' => [],
    ];
  }

}
