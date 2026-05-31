<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit;

use Drupal\ps_core\Service\AuditLogger;
use Drupal\ps_core\Service\ConflictDetector;
use Psr\Log\LoggerInterface;
use Drupal\Tests\UnitTestCase;

final class ConflictDetectorTest extends UnitTestCase {

  public function testNoConflictWhenChecksumsMatch(): void {
    $logger_channel = $this->createMock(LoggerInterface::class);
    $logger_channel->expects($this->never())->method('notice');

    $detector = new ConflictDetector(new AuditLogger($logger_channel));

    self::assertFalse($detector->hasConflict(
      ['checksum' => 'abc'],
      ['checksum' => 'abc'],
    ));
  }

  public function testConflictWhenChecksumsDiffer(): void {
    $logger_channel = $this->createMock(LoggerInterface::class);
    $logger_channel->expects($this->once())->method('notice');

    $detector = new ConflictDetector(new AuditLogger($logger_channel));

    self::assertTrue($detector->hasConflict(
      ['checksum' => 'abc'],
      ['checksum' => 'def'],
    ));
  }

}
