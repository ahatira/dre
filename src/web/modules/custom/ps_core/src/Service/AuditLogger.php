<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Psr\Log\LoggerInterface;

final class AuditLogger {

  public function __construct(
    private readonly LoggerInterface $logger,
  ) {}

  public function log(string $action, array $context = []): void {
    $this->logger->notice('ps_core action: {action}', [
      'action' => $action,
      'context' => $context,
    ]);
  }

}
