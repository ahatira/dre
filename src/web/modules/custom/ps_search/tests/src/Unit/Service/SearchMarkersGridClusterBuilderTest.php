<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\ps_search\Service\SearchMarkersGridClusterBuilder;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchMarkersGridClusterBuilder
 * @group ps_search
 */
final class SearchMarkersGridClusterBuilderTest extends UnitTestCase {

  /**
   * @covers ::build
   */
  public function testBuildReturnsEmptyForNoPoints(): void {
    $builder = new SearchMarkersGridClusterBuilder();
    $bounds = new MapBounds(48.0, 2.0, 49.0, 3.0);

    $this->assertSame([], $builder->build([], $bounds));
  }

  /**
   * @covers ::build
   */
  public function testBuildAggregatesPointsIntoGridCells(): void {
    $builder = new SearchMarkersGridClusterBuilder();
    $bounds = new MapBounds(0.0, 0.0, 2.0, 2.0);
    $points = [
      ['lat' => 0.5, 'lng' => 0.5],
      ['lat' => 0.6, 'lng' => 0.6],
      ['lat' => 1.5, 'lng' => 1.5],
    ];

    $clusters = $builder->build($points, $bounds, 4);

    $this->assertCount(2, $clusters);
    $counts = array_column($clusters, 'count');
    sort($counts);
    $this->assertSame([1, 2], $counts);

    foreach ($clusters as $cluster) {
      $this->assertArrayHasKey('lat', $cluster);
      $this->assertArrayHasKey('lng', $cluster);
      $this->assertArrayHasKey('count', $cluster);
      $this->assertArrayHasKey('map_bounds', $cluster);
      $this->assertMatchesRegularExpression(
        '/^-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?,-?\d+(?:\.\d+)?$/',
        $cluster['map_bounds'],
      );
    }
  }

}
