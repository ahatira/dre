<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Search\Query\SearchSpatialApplier;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Search\Query\SearchSpatialApplier
 */
final class SearchSpatialApplierTest extends UnitTestCase {

  /**
   * @covers ::apply
   */
  public function testApplyBboxAndPostalBuildsConditionGroups(): void {
    $bbox = new GeoBoundingBox(48.8, 2.2, 48.9, 2.5);
    $geo = new GeoContext(
      id: 'department.fr.75',
      slug: 'paris-75',
      type: GeoContextType::Department,
      label: 'Paris',
      lat: 48.8566,
      lng: 2.3522,
      bbox: $bbox,
      countryCode: 'fr',
      postalPrefixes: ['75'],
      radiusM: NULL,
      precision: GeoPrecision::Admin,
      source: 'geo_zone',
    );
    $spatial = new SpatialConstraint(
      mode: SpatialMode::BboxAndPostal,
      bbox: $bbox,
      postalPrefixes: ['75'],
      radiusM: NULL,
      viewport: NULL,
      isochroneGeoJson: NULL,
    );

    $postalGroup = $this->createMock(ConditionGroupInterface::class);
    $rootGroup = $this->createMock(ConditionGroupInterface::class);
    $rootGroup->expects(self::once())->method('addConditionGroup')->with($postalGroup);

    $query = $this->createMock(QueryInterface::class);
    $query->expects(self::exactly(2))
      ->method('createConditionGroup')
      ->willReturnOnConsecutiveCalls($rootGroup, $postalGroup);
    $query->expects(self::once())
      ->method('addConditionGroup')
      ->with($rootGroup);

    (new SearchSpatialApplier())->apply($query, $spatial, $geo);
  }

}
