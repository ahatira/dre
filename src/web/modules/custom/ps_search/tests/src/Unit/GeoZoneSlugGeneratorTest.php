<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\GeoZone\GeoZoneSlugGenerator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\GeoZone\GeoZoneSlugGenerator
 */
final class GeoZoneSlugGeneratorTest extends UnitTestCase {

  /**
   * @covers ::build
   */
  public function testBuildSlugWithCodeSuffix(): void {
    $generator = new GeoZoneSlugGenerator();

    self::assertSame('paris-75', $generator->build('Paris', '75'));
    self::assertSame('rhone-69', $generator->build('Rhône', '69'));
  }

}
