<?php

declare(strict_types=1);

namespace Drupal\ps_core\HealthCheck;

/**
 * Value object returned by a platform health check.
 */
final class HealthCheckResult {

  /**
   * @param list<array{title: string, route: string, params?: array<string, scalar>}> $links
   * @param list<string> $commands
   */
  public function __construct(
    public readonly string $status,
    public readonly string $message,
    public readonly array $links = [],
    public readonly array $commands = [],
    public readonly ?string $detail = NULL,
  ) {
    if (!in_array($status, HealthCheckStatus::all(), TRUE)) {
      throw new \InvalidArgumentException(sprintf('Invalid health status: %s', $status));
    }
  }

}
