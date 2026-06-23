<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Language\Language;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\Search\Context\SearchContextResolver;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\Search\Context\SearchFiltersParser;
use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoZone;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * @coversDefaultClass \Drupal\ps_search\Search\Context\SearchContextResolver
 */
final class SearchContextResolverTest extends UnitTestCase {

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
   * @covers ::resolve
   */
  public function testResolveDepartmentFromSeoPath(): void {
    $parisZone = $this->sampleParisZone();
    $repository = $this->createMock(GeoZoneRepositoryInterface::class);
    $repository->method('findBySlug')->with('paris-75', 'fr')->willReturn($parisZone);

    $pathResolver = $this->createMock(SearchPathResolverInterface::class);
    $pathResolver->method('resolveFacetsFromPathSegments')->willReturn([
      'operation_type' => 'LOC',
      'asset_type' => 'BUR',
      'locality_segments' => ['paris-75'],
    ]);

    $resolver = $this->buildResolver($repository, $pathResolver);
    $request = Request::create('/a-louer/bureaux/paris-75/', 'GET', [
      'operation_type' => 'LOC',
      'asset_type' => 'BUR',
    ]);

    $context = $resolver->resolve($request);

    self::assertTrue($context->isValid);
    self::assertNotNull($context->geo);
    self::assertSame('department.fr.75', $context->geo->id);
    self::assertSame('LOC', $context->filters->operationType);
    self::assertSame('BUR', $context->filters->assetType);
    self::assertSame(SpatialMode::BboxAndPostal, $context->spatial->mode);
    self::assertSame(['75'], $context->spatial->postalPrefixes);
  }

  /**
   * @covers ::resolve
   */
  public function testInvalidWhenLocationRequiredWithoutGeo(): void {
    $repository = $this->createMock(GeoZoneRepositoryInterface::class);
    $pathResolver = $this->createMock(SearchPathResolverInterface::class);
    $pathResolver->method('resolveFacetsFromPathSegments')->willReturn([
      'operation_type' => 'LOC',
      'asset_type' => NULL,
      'locality_segments' => [],
    ]);

    $engineConfig = $this->createMock(ImmutableConfig::class);
    $engineConfig->method('get')->willReturnCallback(function (string $key) {
      return match ($key) {
        'location_required' => TRUE,
        'default_geo_zone_by_country' => [],
        'default_radius_by_geo_type' => [],
        default => NULL,
      };
    });

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('ps_search.engine_settings')->willReturn($engineConfig);

    $resolver = $this->buildResolver($repository, $pathResolver, $configFactory);
    $context = $resolver->resolve(Request::create('/a-louer/', 'GET'));

    self::assertFalse($context->isValid);
    self::assertSame('location_required', $context->invalidReason);
    self::assertNull($context->geo);
  }

  /**
   * @covers ::isSearchRequest
   */
  public function testIsSearchRequestDetectsSeoOperationPath(): void {
    $pathResolver = $this->createMock(SearchPathResolverInterface::class);
    $pathResolver->method('getInternalPath')->willReturn('/find-property');
    $pathResolver->method('isOperationSlug')->with('fr', 'a-louer')->willReturn(TRUE);

    $resolver = $this->buildResolver(
      $this->createMock(GeoZoneRepositoryInterface::class),
      $pathResolver,
    );

    self::assertTrue($resolver->isSearchRequest(Request::create('/a-louer/bureaux/paris-75/')));
  }

  private function buildResolver(
    GeoZoneRepositoryInterface $repository,
    SearchPathResolverInterface $pathResolver,
    ?ConfigFactoryInterface $configFactory = NULL,
  ): SearchContextResolver {
    $language = new Language(['id' => 'fr']);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getCurrentLanguage')->willReturn($language);
    $languageManager->method('getDefaultLanguage')->willReturn($language);

    $contentLanguageResolver = new SearchContentLanguageResolver($languageManager);
    $filtersParser = new SearchFiltersParser($contentLanguageResolver);

    if ($configFactory === NULL) {
      $engineConfig = $this->createMock(ImmutableConfig::class);
      $engineConfig->method('get')->willReturnCallback(function (string $key) {
        return match ($key) {
          'location_required' => FALSE,
          'default_geo_zone_by_country' => [],
          'default_radius_by_geo_type' => [],
          default => NULL,
        };
      });
      $configFactory = $this->createMock(ConfigFactoryInterface::class);
      $configFactory->method('get')->with('ps_search.engine_settings')->willReturn($engineConfig);
    }

    return new SearchContextResolver(
      $pathResolver,
      $repository,
      $filtersParser,
      $configFactory,
      $languageManager,
    );
  }

  private function sampleParisZone(): GeoZone {
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
