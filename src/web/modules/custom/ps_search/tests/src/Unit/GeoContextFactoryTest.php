<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Search\Context\GeoContextFactory;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\GeoZone;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Search\Context\GeoContextFactory
 */
final class GeoContextFactoryTest extends UnitTestCase {

  /**
   * @covers ::fromGeoZone
   */
  public function testFromGeoZoneMapsDepartment(): void {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);
    $zone = new GeoZone(
      id: 'department.fr.75',
      type: GeoZoneType::Department,
      countryCode: 'fr',
      code: '75',
      label: 'Paris',
      slug: 'paris-75',
      lat: 48.8566,
      lng: 2.3522,
      bbox: $bbox,
      postalPrefixes: ['75'],
      weight: 75,
    );

    $context = GeoContextFactory::fromGeoZone($zone);

    self::assertSame('department.fr.75', $context->id);
    self::assertSame(GeoContextType::Department, $context->type);
    self::assertSame(GeoPrecision::Admin, $context->precision);
    self::assertSame(['75'], $context->postalPrefixes);
  }

}
