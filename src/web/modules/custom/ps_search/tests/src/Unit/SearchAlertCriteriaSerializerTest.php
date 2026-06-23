<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Search\Context\SearchContextStorageMapper;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\SearchAlertCriteriaSerializer;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
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
 * @coversDefaultClass \Drupal\ps_search\Service\SearchAlertCriteriaSerializer
 */
final class SearchAlertCriteriaSerializerTest extends UnitTestCase {

  /**
   * @covers ::buildRequest
   */
  public function testBuildRequestRestoresSearchContextAttribute(): void {
    $serializer = $this->buildSerializer();
    $mapper = new SearchContextStorageMapper();
    $context = $this->sampleContext();
    $criteria = [
      'schema_version' => SearchAlertCriteriaSerializer::SCHEMA_VERSION_CONTEXT,
      'context' => $mapper->export($context, ['mf_ceiling_height' => '3']),
      'search_path' => '/a-louer/bureaux/paris-75/',
      'langcode' => 'fr',
    ];

    $request = $serializer->buildRequest($criteria);
    $attached = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);

    self::assertInstanceOf(SearchContext::class, $attached);
    self::assertSame('paris-75', $attached->geo?->slug);
    self::assertSame('/a-louer/bureaux/paris-75/', $request->getPathInfo());
    self::assertSame('3', $request->query->get('mf_ceiling_height'));
  }

  /**
   * @covers ::hash
   */
  public function testHashIsStableForContextCriteria(): void {
    $serializer = $this->buildSerializer();
    $mapper = new SearchContextStorageMapper();
    $criteria = [
      'schema_version' => SearchAlertCriteriaSerializer::SCHEMA_VERSION_CONTEXT,
      'context' => $mapper->export($this->sampleContext(), []),
      'search_path' => '/a-louer/bureaux/paris-75/',
      'langcode' => 'fr',
    ];

    $normalized = $serializer->normalizeCriteria($criteria);

    self::assertSame($serializer->hash($normalized), $serializer->hash($criteria));
  }

  private function buildSerializer(): SearchAlertCriteriaSerializer {
    $contextSerializer = $this->createMock(SearchContextSerializerInterface::class);
    $contextSerializer->method('buildSeoPath')->willReturn('/a-louer/bureaux/paris-75/');
    $contextSerializer->method('buildQueryParams')->willReturn([]);

    $locationFilter = (new \ReflectionClass(LocationSearchFilter::class))
      ->newInstanceWithoutConstructor();

    return new SearchAlertCriteriaSerializer(
      $locationFilter,
      $this->createMock(LanguageManagerInterface::class),
      $contextSerializer,
      new SearchContextStorageMapper(),
      new SearchEngineSettingsReader($this->createConfigFactory()),
    );
  }

  private function createConfigFactory(): ConfigFactoryInterface {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(static fn (string $key) => match ($key) {
      'features' => ['use_search_context' => TRUE],
      default => NULL,
    });

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_search.engine_settings')->willReturn($config);

    return $configFactory;
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
      filters: new SearchFilters('LOC', 'BUR', NULL, NULL, NULL, []),
      spatial: new SpatialConstraint(
        SpatialMode::BboxAndPostal,
        $bbox,
        ['75'],
        NULL,
        NULL,
        NULL,
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
