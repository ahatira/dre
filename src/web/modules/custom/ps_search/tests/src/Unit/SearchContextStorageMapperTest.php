<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\ps_search\Search\Context\SearchContextStorageMapper;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\RangeFilter;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\ps_search\ValueObject\SearchSort;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Search\Context\SearchContextStorageMapper
 */
final class SearchContextStorageMapperTest extends UnitTestCase {

  /**
   * @covers ::export
   * @covers ::import
   */
  public function testRoundTripPreservesGeoAndFilters(): void {
    $mapper = new SearchContextStorageMapper();
    $context = $this->sampleContext();

    $payload = $mapper->export($context, ['mf_ceiling_height' => '3']);
    $restored = $mapper->import($payload);

    self::assertSame('department.fr.75', $restored->geo?->id);
    self::assertSame('paris-75', $restored->geo?->slug);
    self::assertSame('LOC', $restored->filters->operationType);
    self::assertSame('BUR', $restored->filters->assetType);
    self::assertSame(['mf_ceiling_height' => '3'], $restored->filters->moreCriteria);
    self::assertSame(SpatialMode::BboxAndPostal, $restored->spatial->mode);
    self::assertTrue($restored->isValid);
  }

  private function sampleContext(): SearchContext {
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

    return new SearchContext(
      geo: $geo,
      filters: new SearchFilters(
        operationType: 'LOC',
        assetType: 'BUR',
        surface: new RangeFilter(100.0, 500.0),
        budget: NULL,
        capacity: NULL,
        moreCriteria: [],
      ),
      spatial: new SpatialConstraint(
        mode: SpatialMode::BboxAndPostal,
        bbox: $bbox,
        postalPrefixes: ['75'],
        radiusM: NULL,
        viewport: NULL,
        isochroneGeoJson: NULL,
      ),
      sort: new SearchSort(SearchSort::DEFAULT_SORT_BY, SearchSort::DEFAULT_SORT_ORDER),
      langcode: 'fr',
      countryCode: 'fr',
      locationRequired: FALSE,
      isValid: TRUE,
      invalidReason: NULL,
    );
  }

}
