<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Service;

/**
 * Allowed catalogue source values for feature definitions.
 */
final class FeatureDefinitionSource {

  public const BO = 'bo';

  public const XML = 'xml';

  public const LEGACY = 'legacy';

  /**
   * Returns all allowed source machine names.
   *
   * @return string[]
   *   Source values.
   */
  public static function allowedValues(): array {
    return [
      self::BO,
      self::XML,
      self::LEGACY,
    ];
  }

  /**
   * Checks whether a source value is allowed.
   */
  public static function isValid(string $source): bool {
    return in_array($source, self::allowedValues(), TRUE);
  }

}
