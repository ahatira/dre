<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Utility;

/**
 * Display mode for homepage blocks in S-D Section Library layouts.
 */
final class HomepageSectionDisplayMode {

  public const BODY_ONLY_KEY = '_sd_body_only';

  /**
   * @param array<string, mixed> $configuration
   */
  public static function isBodyOnly(array $configuration): bool {
    return !empty($configuration[self::BODY_ONLY_KEY]);
  }

  /**
   * @param array<string, mixed> $configuration
   *
   * @return array<string, mixed>
   */
  public static function markBodyOnly(array $configuration): array {
    $configuration[self::BODY_ONLY_KEY] = TRUE;
    return $configuration;
  }

}
