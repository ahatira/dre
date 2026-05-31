<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_core\Service\AuditLogger;
use Drupal\Tests\UnitTestCase;

final class AuditLoggerTest extends UnitTestCase {

  public function testLogWritesNotice(): void {
    $logger = $this->createMock(LoggerChannelInterface::class);
    $logger
      ->expects($this->once())
      ->method('notice')
      ->with(
        'ps_core action: {action}',
        $this->callback(static function (array $context): bool {
          return isset($context['action']) && $context['action'] === 'test_action';
        }),
      );

    $audit_logger = new AuditLogger($logger);
    $audit_logger->log('test_action');
  }

}
