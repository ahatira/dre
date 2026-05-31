<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit;

use Drupal\ps_core\Service\FieldMapper;
use Drupal\Tests\UnitTestCase;

final class FieldMapperTest extends UnitTestCase {

  public function testMapString(): void {
    $mapper = new FieldMapper();

    self::assertSame('abc', $mapper->mapValue('string', '  abc  '));
    self::assertNull($mapper->mapValue('string', '   '));
  }

  public function testMapDecimal(): void {
    $mapper = new FieldMapper();

    self::assertSame(42.5, $mapper->mapValue('decimal', '42.5'));
    self::assertNull($mapper->mapValue('decimal', 'not-a-number'));
  }

  public function testMapBoolean(): void {
    $mapper = new FieldMapper();

    self::assertTrue($mapper->mapValue('boolean', 'YES'));
    self::assertFalse($mapper->mapValue('boolean', '0'));
  }

}
