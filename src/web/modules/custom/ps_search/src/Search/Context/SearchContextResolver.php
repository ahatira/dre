<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Context;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves SearchContext from SEO paths and query parameters (no L3 geocoding).
 */
final class SearchContextResolver implements SearchContextResolverInterface {

  private const ENGINE_SETTINGS = 'ps_search.engine_settings';

  private const MAP_BOUNDS_PARAM = 'map_bounds';

  public function __construct(
    private readonly SearchPathResolverInterface $searchPathResolver,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
    private readonly SearchFiltersParser $filtersParser,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function resolve(Request $request): SearchContext {
    $countryCode = $this->resolveCountryCode();
    $langcode = $this->filtersParser->resolveLangcode($request);
    $engineConfig = $this->configFactory->get(self::ENGINE_SETTINGS);
    $locationRequired = (bool) ($engineConfig->get('location_required') ?? FALSE);

    $pathFacets = $this->resolvePathFacets($request, $langcode);
    $filters = $this->filtersParser->parseFilters($request, $pathFacets);
    $sort = $this->filtersParser->parseSort($request);

    $geo = $this->resolveGeo($request, $countryCode, $pathFacets['locality_segments'] ?? []);
    if ($geo === NULL && !$locationRequired) {
      $geo = $this->resolveDefaultGeo($countryCode, $engineConfig->get('default_geo_zone_by_country') ?? []);
    }

    $spatial = $this->buildSpatialConstraint($request, $geo);
    [$isValid, $invalidReason] = $this->validateContext($geo, $filters, $locationRequired);

    return new SearchContext(
      geo: $geo,
      filters: $filters,
      spatial: $spatial,
      sort: $sort,
      langcode: $langcode,
      countryCode: $countryCode,
      locationRequired: $locationRequired,
      isValid: $isValid,
      invalidReason: $invalidReason,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isSearchRequest(Request $request): bool {
    $pathInfo = $request->getPathInfo();
    $internalPath = $this->searchPathResolver->getInternalPath();

    if ($pathInfo === $internalPath || rtrim($pathInfo, '/') === rtrim($internalPath, '/')) {
      return TRUE;
    }

    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $segments = $this->extractPathSegments($request, $langcode);
    if ($segments === []) {
      return $this->searchPathResolver->isSearchPath($pathInfo);
    }

    $first = strtolower($segments[0]);
    if ($this->searchPathResolver->isOperationSlug($langcode, $first)
      || $this->searchPathResolver->isAssetSlug($langcode, $first)) {
      return TRUE;
    }

    return $this->searchPathResolver->isSearchPath($pathInfo);
  }

  /**
   * @return array{operation_type: ?string, asset_type: ?string, locality_segments: list<string>}
   */
  private function resolvePathFacets(Request $request, string $langcode): array {
    $segments = $this->extractPathSegments($request, $langcode);
    if ($segments === []) {
      return [
        'operation_type' => NULL,
        'asset_type' => NULL,
        'locality_segments' => [],
      ];
    }

    return $this->searchPathResolver->resolveFacetsFromPathSegments($langcode, $segments);
  }

  /**
   * @param list<string> $localitySegments
   */
  private function resolveGeo(Request $request, string $countryCode, array $localitySegments): ?GeoContext {
    $zoneSlug = trim((string) $request->query->get('zone', ''));
    if ($zoneSlug !== '') {
      $zone = $this->geoZoneRepository->findBySlug(strtolower($zoneSlug), $countryCode);
      if ($zone !== NULL) {
        return GeoContextFactory::fromGeoZone($zone);
      }
    }

    if ($localitySegments !== []) {
      $slug = strtolower((string) end($localitySegments));
      $zone = $this->geoZoneRepository->findBySlug($slug, $countryCode);
      if ($zone !== NULL) {
        return GeoContextFactory::fromGeoZone($zone);
      }
    }

    return NULL;
  }

  /**
   * @param array<string, mixed> $defaultByCountry
   */
  private function resolveDefaultGeo(string $countryCode, array $defaultByCountry): ?GeoContext {
    $configuredId = isset($defaultByCountry[$countryCode]) && is_string($defaultByCountry[$countryCode])
      ? trim($defaultByCountry[$countryCode])
      : '';

    if ($configuredId !== '') {
      $zone = $this->geoZoneRepository->get($configuredId);
      if ($zone !== NULL) {
        return GeoContextFactory::fromGeoZone($zone, 'config_default');
      }
    }

    $zone = $this->geoZoneRepository->getDefaultForCountry($countryCode);
    return $zone !== NULL ? GeoContextFactory::fromGeoZone($zone, 'country_default') : NULL;
  }

  private function buildSpatialConstraint(Request $request, ?GeoContext $geo): SpatialConstraint {
    $viewport = $this->parseMapBounds($request);
    if ($viewport instanceof MapBounds) {
      return new SpatialConstraint(
        mode: SpatialMode::Viewport,
        bbox: NULL,
        postalPrefixes: [],
        radiusM: NULL,
        viewport: $viewport,
        isochroneGeoJson: NULL,
      );
    }

    if ($geo === NULL) {
      return new SpatialConstraint(
        mode: SpatialMode::None,
        bbox: NULL,
        postalPrefixes: [],
        radiusM: NULL,
        viewport: NULL,
        isochroneGeoJson: NULL,
      );
    }

    if (in_array($geo->type, [GeoContextType::Department, GeoContextType::Region], TRUE)
      && $geo->postalPrefixes !== []) {
      return new SpatialConstraint(
        mode: SpatialMode::BboxAndPostal,
        bbox: $geo->bbox,
        postalPrefixes: $geo->postalPrefixes,
        radiusM: NULL,
        viewport: NULL,
        isochroneGeoJson: NULL,
      );
    }

    $radiusM = $this->resolveDefaultRadius($geo->type);

    return new SpatialConstraint(
      mode: SpatialMode::Geofilt,
      bbox: $geo->bbox,
      postalPrefixes: $geo->postalPrefixes,
      radiusM: $radiusM,
      viewport: NULL,
      isochroneGeoJson: NULL,
    );
  }

  /**
   * @return array{0: bool, 1: ?string}
   */
  private function validateContext(?GeoContext $geo, \Drupal\ps_search\ValueObject\SearchFilters $filters, bool $locationRequired): array {
    if ($locationRequired && $geo === NULL) {
      return [FALSE, 'location_required'];
    }

    return [TRUE, NULL];
  }

  private function resolveDefaultRadius(GeoContextType $type): ?int {
    $config = $this->configFactory->get(self::ENGINE_SETTINGS);
    $byType = is_array($config->get('default_radius_by_geo_type') ?? NULL)
      ? $config->get('default_radius_by_geo_type')
      : [];

    $key = match ($type) {
      GeoContextType::Address => 'address',
      GeoContextType::City => 'city',
      GeoContextType::Postal => 'postal',
      GeoContextType::Coordinates => 'coordinates',
      default => NULL,
    };

    if ($key === NULL || !array_key_exists($key, $byType) || $byType[$key] === NULL) {
      return NULL;
    }

    $radius = filter_var($byType[$key], FILTER_VALIDATE_INT);
    return $radius === FALSE || $radius <= 0 ? NULL : $radius;
  }

  private function parseMapBounds(Request $request): ?MapBounds {
    $raw = $request->query->get(self::MAP_BOUNDS_PARAM);
    if (!is_string($raw) || $raw === '') {
      return NULL;
    }

    $parts = array_map('trim', explode(',', $raw));
    if (count($parts) !== 4) {
      return NULL;
    }

    $values = [];
    foreach ($parts as $part) {
      if (!is_numeric($part)) {
        return NULL;
      }
      $values[] = (float) $part;
    }

    [$swLat, $swLng, $neLat, $neLng] = $values;
    if ($swLat >= $neLat || $swLng >= $neLng) {
      return NULL;
    }

    return new MapBounds($swLat, $swLng, $neLat, $neLng);
  }

  /**
   * @return list<string>
   */
  private function extractPathSegments(Request $request, string $langcode): array {
    $pathInfo = $request->getPathInfo();
    $defaultLang = $this->languageManager->getDefaultLanguage()->getId();
    $langPrefix = ($langcode !== $defaultLang) ? '/' . $langcode : '';

    if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
      $pathInfo = substr($pathInfo, strlen($langPrefix));
    }

    $stripped = ltrim($pathInfo, '/');
    if ($stripped === '') {
      return [];
    }

    return array_values(array_filter(explode('/', $stripped)));
  }

  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    if (is_string($code) && $code !== '') {
      return strtolower($code);
    }

    return 'com';
  }

}
