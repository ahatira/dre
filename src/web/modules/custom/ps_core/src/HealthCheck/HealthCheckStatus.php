<?php

declare(strict_types=1);

namespace Drupal\ps_core\HealthCheck;

/**
 * Allowed health check result statuses.
 */
final class HealthCheckStatus {

  public const OK = 'ok';

  public const WARNING = 'warning';

  public const FAIL = 'fail';

  public const INFO = 'info';

  /**
   * @return list<string>
   */
  public static function all(): array {
    return [
      self::OK,
      self::WARNING,
      self::FAIL,
      self::INFO,
    ];
  }

}
