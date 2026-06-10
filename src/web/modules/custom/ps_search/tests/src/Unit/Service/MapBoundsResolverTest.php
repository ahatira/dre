<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_search\Service\LocationCentroidResolver;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\Tests\ps_search\Unit\Stub\StubSearchResultGeoBoundsResolver;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\MapBoundsResolver
 * @group ps_search
 */
final class MapBoundsResolverTest extends UnitTestCase {

  /**
   * @covers ::resolveActiveBounds
   */
  public function testParseExplicitBounds(): void {
    $resolver = $this->createResolver();
    $request = Request::create('/find-property', 'GET', [
      'map_bounds' => '48.80,2.30,48.90,2.40',
    ]);

    $bounds = $resolver->resolveActiveBounds($request);
    $this->assertNotNull($bounds);
    $this->assertEqualsWithDelta(48.80, $bounds->swLat, 0.0001);
    $this->assertEqualsWithDelta(2.30, $bounds->swLng, 0.0001);
    $this->assertEqualsWithDelta(48.90, $bounds->neLat, 0.0001);
    $this->assertEqualsWithDelta(2.40, $bounds->neLng, 0.0001);
  }

  /**
   * @covers ::resolveActiveBounds
   */
  public function testAutoFitWhenDefaultZoneIsPartial(): void {
    $resultBounds = new MapBounds(42.0, -5.0, 51.0, 9.0);
    $resolver = $this->createResolver(new StubSearchResultGeoBoundsResolver(4, 448, $resultBounds));
    $request = Request::create('/find-property');

    $bounds = $resolver->resolveActiveBounds($request);
    $this->assertNotNull($bounds);
    $this->assertSame($resultBounds->toQueryValue(), $bounds->toQueryValue());
    $this->assertTrue($resolver->autoFitToResults($request));
  }

  /**
   * @covers ::resolveActiveBounds
   */
  public function testAutoFitToResultsWhenDefaultZoneEmpty(): void {
    $resultBounds = new MapBounds(43.0, -1.0, 51.0, 8.0);
    $resolver = $this->createResolver(new StubSearchResultGeoBoundsResolver(0, 50, $resultBounds));
    $request = Request::create('/find-property', 'GET', [
      'operation_type' => 'LOC',
      'asset_type' => 'BUR',
    ]);

    $bounds = $resolver->resolveActiveBounds($request);
    $this->assertNotNull($bounds);
    $this->assertSame($resultBounds->toQueryValue(), $bounds->toQueryValue());
    $this->assertTrue($resolver->autoFitToResults($request));
  }

  /**
   * @covers ::resolveActiveBounds
   */
  public function testDefaultBoundsFromConfig(): void {
    $resolver = $this->createResolver();
    $request = Request::create('/find-property');

    $bounds = $resolver->resolveActiveBounds($request);
    $this->assertNotNull($bounds);
    $this->assertLessThan(46.603354, $bounds->swLat);
    $this->assertGreaterThan(46.603354, $bounds->neLat);
  }

  /**
   * @covers ::localityFilterChanged
   */
  public function testLocalityFilterChanged(): void {
    $locationFilter = $this->createLocationSearchFilter();
    $resolver = new MapBoundsResolver(
      new LocationCentroidResolver($locationFilter, $this->createConfigFactory()),
      $locationFilter,
      $this->createConfigFactory(),
      new StubSearchResultGeoBoundsResolver(),
    );

    $paris = Request::create('/', 'GET', ['locality' => 'Paris']);
    $lyon = Request::create('/', 'GET', ['locality' => 'Lyon']);
    $this->assertTrue($resolver->localityFilterChanged($lyon, $paris));
    $this->assertFalse($resolver->localityFilterChanged($paris, $paris));
  }

  /**
   * Builds a resolver with default test doubles.
   */
  private function createResolver(?StubSearchResultGeoBoundsResolver $geoBoundsResolver = NULL): MapBoundsResolver {
    $locationFilter = $this->createLocationSearchFilter();

    return new MapBoundsResolver(
      new LocationCentroidResolver($locationFilter, $this->createConfigFactory()),
      $locationFilter,
      $this->createConfigFactory(),
      $geoBoundsResolver ?? new StubSearchResultGeoBoundsResolver(),
    );
  }

  /**
   * Builds a location filter with an empty dictionary backend.
   */
  private function createLocationSearchFilter(): LocationSearchFilter {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->willReturn([]);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('ps_dictionary_entry')->willReturn($storage);

    return new LocationSearchFilter(
      $this->createMock(Connection::class),
      new DictionaryResolver($entityTypeManager),
    );
  }

  /**
   * Builds a config factory stub for map zone defaults.
   */
  private function createConfigFactory(): ConfigFactoryInterface {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnMap([
      ['default_center_lat', 46.603354],
      ['default_center_lng', 1.888334],
      ['default_radius_km', 50],
    ]);

    $factory = $this->createMock(ConfigFactoryInterface::class);
    $factory->method('get')->with('ps_search.map_zone_settings')->willReturn($config);

    return $factory;
  }

}
