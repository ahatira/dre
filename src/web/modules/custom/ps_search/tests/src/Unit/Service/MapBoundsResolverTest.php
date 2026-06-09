<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\ps_search\Service\LocationCentroidResolver;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\MapBoundsResolver;
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
    $locationFilter = $this->createMock(LocationSearchFilter::class);
    $locationFilter->method('extractTokensFromRequest')->willReturnMap([
      [Request::create('/', 'GET', ['locality' => 'Paris'])], ['Paris'],
      [Request::create('/', 'GET', ['locality' => 'Lyon'])], ['Lyon'],
      [Request::create('/', 'GET', ['locality' => 'Paris'])], ['Paris'],
    ]);

    $resolver = new MapBoundsResolver(
      $this->createMock(LocationCentroidResolver::class),
      $locationFilter,
      $this->createConfigFactory(),
    );

    $paris = Request::create('/', 'GET', ['locality' => 'Paris']);
    $lyon = Request::create('/', 'GET', ['locality' => 'Lyon']);
    $this->assertTrue($resolver->localityFilterChanged($lyon, $paris));
    $this->assertFalse($resolver->localityFilterChanged($paris, $paris));
  }

  /**
   * Builds a resolver with default test doubles.
   */
  private function createResolver(): MapBoundsResolver {
    $centroid = $this->createMock(LocationCentroidResolver::class);
    $centroid->method('resolveFromRequest')->willReturn(NULL);

    $locationFilter = $this->createMock(LocationSearchFilter::class);
    $locationFilter->method('extractTokensFromRequest')->willReturn([]);

    return new MapBoundsResolver(
      $centroid,
      $locationFilter,
      $this->createConfigFactory(),
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
