<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\GeoZone\GeoZonePostalPrefixBuilder;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\GeoZone\GeoZonePostalPrefixBuilder
 */
final class GeoZonePostalPrefixBuilderTest extends UnitTestCase {

  /**
   * @covers ::forDepartmentCode
   */
  public function testFrenchCorsicaPrefixes(): void {
    $builder = new GeoZonePostalPrefixBuilder();

    self::assertSame(['200'], $builder->forDepartmentCode('fr', '2A'));
    self::assertSame(['202'], $builder->forDepartmentCode('fr', '2B'));
    self::assertSame(['75'], $builder->forDepartmentCode('fr', '75'));
  }

}
