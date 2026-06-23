<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Geocoding\GeoZoneGeocodingProvider;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoZone;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Geocoding\GeoZoneGeocodingProvider
 */
final class GeoZoneGeocodingProviderTest extends UnitTestCase {

  /**
   * @covers ::resolve
   */
  public function testResolveBySlug(): void {
    $zone = $this->parisZone();
    $repository = $this->createMock(GeoZoneRepositoryInterface::class);
    $repository->method('findBySlug')->with('paris-75', 'fr')->willReturn($zone);
    $repository->method('allForCountry')->willReturn([]);

    $provider = new GeoZoneGeocodingProvider($repository);
    $result = $provider->resolve('paris-75', 'fr', 'fr');

    self::assertNotNull($result->geo);
    self::assertSame('paris-75', $result->geo->slug);
    self::assertFalse($result->ambiguous);
  }

  /**
   * @covers ::resolve
   */
  public function testResolveByDepartmentCode(): void {
    $zone = $this->parisZone();
    $repository = $this->createMock(GeoZoneRepositoryInterface::class);
    $repository->method('findByPostalPrefix')->with('75', 'fr')->willReturn($zone);
    $repository->method('allForCountry')->willReturn([]);

    $provider = new GeoZoneGeocodingProvider($repository);
    $result = $provider->resolve('75', 'fr', 'fr');

    self::assertNotNull($result->geo);
    self::assertSame('department.fr.75', $result->geo->id);
  }

  private function parisZone(): GeoZone {
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm(48.8566, 2.3522, 20.0);

    return new GeoZone(
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
  }

}
