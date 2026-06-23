<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\Search\Context\SearchContextSerializer;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\ps_search\ValueObject\SearchSort;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Search\Context\SearchContextSerializer
 */
final class SearchContextSerializerTest extends UnitTestCase {

  protected function setUp(): void {
    parent::setUp();
    try {
      Settings::getInstance();
    }
    catch (\BadMethodCallException) {
      new Settings(['ps_country_code' => 'fr']);
    }
  }

  /**
   * @covers ::buildSeoPath
   */
  public function testBuildSeoPathAppendsGeoSlug(): void {
    $serializer = $this->buildSerializer();
    $context = $this->sampleContext();

    self::assertSame('/a-louer/bureaux/paris-75/', $serializer->buildSeoPath($context, 'fr'));
  }

  /**
   * @covers ::buildQueryParams
   */
  public function testBuildQueryParamsOmitsPathEncodedFilters(): void {
    $serializer = $this->buildSerializer();
    $context = $this->sampleContext();

    $query = $serializer->buildQueryParams($context);

    self::assertArrayNotHasKey('operation_type', $query);
    self::assertArrayNotHasKey('asset_type', $query);
    self::assertArrayNotHasKey('zone', $query);
  }

  /**
   * @covers ::buildSeoPathFromQuery
   */
  public function testBuildSeoPathFromQueryUsesZoneSlug(): void {
    $serializer = $this->buildSerializer();

    $path = $serializer->buildSeoPathFromQuery('fr', [
      'operation_type' => 'LOC',
      'asset_type' => 'BUR',
      'zone' => 'paris-75',
    ]);

    self::assertSame('/a-louer/bureaux/paris-75/', $path);
  }

  /**
   * @covers ::toArray
   */
  public function testToArrayIncludesGeoSlug(): void {
    $serializer = $this->buildSerializer();
    $payload = $serializer->toArray($this->sampleContext());

    self::assertSame('paris-75', $payload['geo']['slug']);
    self::assertSame('LOC', $payload['filters']['operationType']);
    self::assertSame('BUR', $payload['filters']['assetType']);
  }

  private function buildSerializer(): SearchContextSerializer {
    $language = new Language(['id' => 'fr']);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getDefaultLanguage')->willReturn($language);
    $languageManager->method('getLanguage')->with('fr')->willReturn($language);

    $pathResolver = $this->createMock(SearchPathResolverInterface::class);
    $pathResolver->method('buildSeoFilterPathPrefix')
      ->with('fr', 'LOC', 'BUR')
      ->willReturn('/a-louer/bureaux/');

    return new SearchContextSerializer(
      $this->createMock(SearchContextResolverInterface::class),
      $pathResolver,
      $this->createMock(GeoZoneRepositoryInterface::class),
      $languageManager,
    );
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
        surface: NULL,
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
