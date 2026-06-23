<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\Service\SearchPresetQueryBuilder;
use Drupal\ps_search\Service\SearchSeoCanonicalUrlBuilder;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\GeoZone;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\ps_search\ValueObject\SearchSort;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\SearchPresetQueryBuilder
 */
final class SearchPresetQueryBuilderTest extends UnitTestCase {

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
   * @covers ::buildUrl
   */
  public function testBuildUrlUsesContextSerializerWhenFlagEnabled(): void {
    $builder = $this->buildBuilder(TRUE);
    $url = $builder->buildUrl('LOC', 'BUR', '75', 'fr');

    self::assertSame('/a-louer/bureaux/paris-75/', $url);
  }

  private function buildBuilder(bool $contextEnabled): SearchPresetQueryBuilder {
    $language = new Language(['id' => 'fr']);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getDefaultLanguage')->willReturn($language);
    $languageManager->method('getCurrentLanguage')->willReturn($language);
    $languageManager->method('getLanguage')->with('fr')->willReturn($language);

    $pathResolver = $this->createMock(SearchPathResolverInterface::class);
    $pathResolver->method('buildSeoFilterPathPrefix')
      ->with('fr', 'LOC', 'BUR')
      ->willReturn('/a-louer/bureaux/');
    $pathResolver->method('getSlugForLang')->willReturn('find-property');

    $canonicalBuilder = (new \ReflectionClass(SearchSeoCanonicalUrlBuilder::class))
      ->newInstanceWithoutConstructor();

    $bbox = new GeoBoundingBox(48.8, 2.2, 48.9, 2.5);
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
    );

    $geoZoneRepository = $this->createMock(GeoZoneRepositoryInterface::class);
    $geoZoneRepository->method('findByPostalPrefix')->with('75', 'fr')->willReturn($zone);

    $context = $this->sampleContext();
    $contextResolver = $this->createMock(SearchContextResolverInterface::class);
    $contextResolver->method('resolve')->willReturn($context);

    $contextSerializer = $this->createMock(SearchContextSerializerInterface::class);
    $contextSerializer->method('buildSeoPath')->willReturn('/a-louer/bureaux/paris-75/');

    $engineSettings = new SearchEngineSettingsReader($this->createConfigFactory($contextEnabled));

    return new SearchPresetQueryBuilder(
      $canonicalBuilder,
      $pathResolver,
      $languageManager,
      $engineSettings,
      $contextSerializer,
      $contextResolver,
      $geoZoneRepository,
    );
  }

  private function createConfigFactory(bool $contextEnabled): ConfigFactoryInterface {
    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(static fn (string $key) => match ($key) {
      'features' => ['use_search_context' => $contextEnabled],
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
