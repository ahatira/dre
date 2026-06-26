<?php

declare(strict_types=1);

namespace Drupal\ps_core\ImportGovernance;

/**
 * Shared lock strategy identifiers for import governance policies.
 */
final class ImportGovernanceLockStrategy {

  public const INHERIT = 'inherit';

  public const LOG_ONLY = 'log_only';

  public const SKIP_ROW = 'skip_row';

  public const SKIP_FIELD = 'skip_field';

  /**
   * Returns allowed strategy machine names.
   *
   * @return string[]
   */
  public static function allowedStrategies(): array {
    return [
      self::LOG_ONLY,
      self::SKIP_ROW,
      self::SKIP_FIELD,
    ];
  }

}
