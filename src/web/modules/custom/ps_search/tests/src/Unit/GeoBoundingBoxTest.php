<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\ValueObject\GeoBoundingBox
 */
final class GeoBoundingBoxTest extends UnitTestCase {

  /**
   * @covers ::fromCenterAndRadiusKm
   * @covers ::isValid
   */
  public function testFromCenterAndRadiusKmBuildsValidBox(): void {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 10.0);

    self::assertTrue($bbox->isValid());
    self::assertTrue($bbox->swLat < $bbox->neLat);
    self::assertTrue($bbox->swLng < $bbox->neLng);
  }

}
