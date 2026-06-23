<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Context;

use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\RangeFilter;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\ps_search\ValueObject\SearchSort;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;

/**
 * Exports and imports SearchContext payloads for alert storage (M7).
 */
final class SearchContextStorageMapper {

  /**
   * Exports a search context for JSON alert criteria storage.
   *
   * @param \Drupal\ps_search\ValueObject\SearchContext $context
   *   Resolved search context from the current request.
   * @param array<string, mixed> $moreCriteria
   *   mf_* feature filters from the HTTP request.
   *
   * @return array<string, mixed>
   *   Stable storage payload nested under alert criteria "context".
   */
  public function export(SearchContext $context, array $moreCriteria = []): array {
    return [
      'geo' => $context->geo instanceof GeoContext ? $this->exportGeo($context->geo) : NULL,
      'filters' => $this->exportFilters($context->filters, $moreCriteria),
      'spatial' => $this->exportSpatial($context->spatial),
      'sort' => [
        'sortBy' => $context->sort->sortBy,
        'sortOrder' => $context->sort->sortOrder,
      ],
      'langcode' => $context->langcode,
      'countryCode' => $context->countryCode,
      'locationRequired' => $context->locationRequired,
      'isValid' => $context->isValid,
      'invalidReason' => $context->invalidReason,
    ];
  }

  /**
   * Rebuilds a SearchContext from stored alert criteria payload.
   *
   * @param array<string, mixed> $data
   *   Payload previously produced by ::export().
   *
   * @return \Drupal\ps_search\ValueObject\SearchContext
   *   Hydrated search context for alert replay.
   */
  public function import(array $data): SearchContext {
    $geo = isset($data['geo']) && is_array($data['geo'])
      ? $this->importGeo($data['geo'])
      : NULL;

    $filters = isset($data['filters']) && is_array($data['filters'])
      ? $this->importFilters($data['filters'])
      : new SearchFilters(NULL, NULL, NULL, NULL, NULL, []);

    $spatial = isset($data['spatial']) && is_array($data['spatial'])
      ? $this->importSpatial($data['spatial'])
      : new SpatialConstraint(SpatialMode::None, NULL, [], NULL, NULL, NULL);

    $sortData = is_array($data['sort'] ?? NULL) ? $data['sort'] : [];
    $sort = new SearchSort(
      sortBy: is_string($sortData['sortBy'] ?? NULL) && $sortData['sortBy'] !== ''
        ? $sortData['sortBy']
        : SearchSort::DEFAULT_SORT_BY,
      sortOrder: is_string($sortData['sortOrder'] ?? NULL) && $sortData['sortOrder'] !== ''
        ? strtoupper($sortData['sortOrder'])
        : SearchSort::DEFAULT_SORT_ORDER,
    );

    return new SearchContext(
      geo: $geo,
      filters: $filters,
      spatial: $spatial,
      sort: $sort,
      langcode: is_string($data['langcode'] ?? NULL) ? $data['langcode'] : 'en',
      countryCode: is_string($data['countryCode'] ?? NULL) ? strtolower($data['countryCode']) : 'com',
      locationRequired: (bool) ($data['locationRequired'] ?? FALSE),
      isValid: (bool) ($data['isValid'] ?? TRUE),
      invalidReason: is_string($data['invalidReason'] ?? NULL) ? $data['invalidReason'] : NULL,
    );
  }

  /**
   * Exports business filters and more-criteria for storage.
   *
   * @param \Drupal\ps_search\ValueObject\SearchFilters $filters
   *   Normalized business filters.
   * @param array<string, mixed> $moreCriteria
   *   mf_* feature filters from the HTTP request.
   *
   * @return array<string, mixed>
   *   Serialized filter payload.
   */
  private function exportFilters(SearchFilters $filters, array $moreCriteria): array {
    return [
      'operationType' => $filters->operationType,
      'assetType' => $filters->assetType,
      'surface' => $this->exportRange($filters->surface),
      'budget' => $this->exportRange($filters->budget),
      'capacity' => $this->exportRange($filters->capacity),
      'moreCriteria' => $moreCriteria,
    ];
  }

  /**
   * Exports spatial constraints for storage.
   *
   * @return array<string, mixed>
   *   Serialized spatial payload.
   */
  private function exportSpatial(SpatialConstraint $spatial): array {
    return [
      'mode' => $spatial->mode->value,
      'bbox' => $spatial->bbox?->toConfigArray(),
      'postalPrefixes' => $spatial->postalPrefixes,
      'radiusM' => $spatial->radiusM,
      'viewport' => $spatial->viewport?->toQueryValue(),
      'isochroneGeoJson' => $spatial->isochroneGeoJson,
    ];
  }

  /**
   * Exports geo context for storage.
   *
   * @return array<string, mixed>
   *   Serialized geo payload.
   */
  private function exportGeo(GeoContext $geo): array {
    return [
      'id' => $geo->id,
      'slug' => $geo->slug,
      'type' => $geo->type->value,
      'label' => $geo->label,
      'lat' => $geo->lat,
      'lng' => $geo->lng,
      'bbox' => $geo->bbox->toConfigArray(),
      'countryCode' => $geo->countryCode,
      'postalPrefixes' => $geo->postalPrefixes,
      'radiusM' => $geo->radiusM,
      'precision' => $geo->precision->value,
      'source' => $geo->source,
    ];
  }

  /**
   * Exports a numeric range filter for storage.
   *
   * @return array<string, float|null>|null
   *   Serialized range or NULL when empty.
   */
  private function exportRange(?RangeFilter $range): ?array {
    if ($range === NULL || $range->isEmpty()) {
      return NULL;
    }

    return [
      'min' => $range->min,
      'max' => $range->max,
    ];
  }

  /**
   * Imports geo context from stored payload.
   *
   * @param array<string, mixed> $data
   *   Stored geo array.
   */
  private function importGeo(array $data): GeoContext {
    $bbox = GeoBoundingBox::fromConfigArray(is_array($data['bbox'] ?? NULL) ? $data['bbox'] : [])
      ?? GeoBoundingBox::fromCenterAndRadiusKm(
        (float) ($data['lat'] ?? 0.0),
        (float) ($data['lng'] ?? 0.0),
        10.0,
      );

    return new GeoContext(
      id: (string) ($data['id'] ?? ''),
      slug: (string) ($data['slug'] ?? ''),
      type: GeoContextType::tryFrom((string) ($data['type'] ?? '')) ?? GeoContextType::City,
      label: (string) ($data['label'] ?? ''),
      lat: (float) ($data['lat'] ?? 0.0),
      lng: (float) ($data['lng'] ?? 0.0),
      bbox: $bbox,
      countryCode: strtolower((string) ($data['countryCode'] ?? 'com')),
      postalPrefixes: array_values(array_filter(
        is_array($data['postalPrefixes'] ?? NULL) ? $data['postalPrefixes'] : [],
        static fn ($prefix) => is_string($prefix) && $prefix !== '',
      )),
      radiusM: isset($data['radiusM']) ? (int) $data['radiusM'] : NULL,
      precision: GeoPrecision::tryFrom((string) ($data['precision'] ?? '')) ?? GeoPrecision::Approximate,
      source: (string) ($data['source'] ?? 'stored'),
    );
  }

  /**
   * Imports business filters from stored payload.
   *
   * @param array<string, mixed> $data
   *   Stored filters array.
   */
  private function importFilters(array $data): SearchFilters {
    return new SearchFilters(
      operationType: is_string($data['operationType'] ?? NULL) && $data['operationType'] !== ''
        ? $data['operationType']
        : NULL,
      assetType: is_string($data['assetType'] ?? NULL) && $data['assetType'] !== ''
        ? $data['assetType']
        : NULL,
      surface: $this->importRange($data['surface'] ?? NULL),
      budget: $this->importRange($data['budget'] ?? NULL),
      capacity: $this->importRange($data['capacity'] ?? NULL),
      moreCriteria: is_array($data['moreCriteria'] ?? NULL) ? $data['moreCriteria'] : [],
    );
  }

  /**
   * Imports spatial constraints from stored payload.
   *
   * @param array<string, mixed> $data
   *   Stored spatial array.
   */
  private function importSpatial(array $data): SpatialConstraint {
    $bbox = GeoBoundingBox::fromConfigArray(is_array($data['bbox'] ?? NULL) ? $data['bbox'] : []);

    return new SpatialConstraint(
      mode: SpatialMode::tryFrom((string) ($data['mode'] ?? '')) ?? SpatialMode::None,
      bbox: $bbox,
      postalPrefixes: array_values(array_filter(
        is_array($data['postalPrefixes'] ?? NULL) ? $data['postalPrefixes'] : [],
        static fn ($prefix) => is_string($prefix) && $prefix !== '',
      )),
      radiusM: isset($data['radiusM']) ? (int) $data['radiusM'] : NULL,
      viewport: $this->importViewport($data['viewport'] ?? NULL),
      isochroneGeoJson: is_string($data['isochroneGeoJson'] ?? NULL) ? $data['isochroneGeoJson'] : NULL,
    );
  }

  /**
   * Imports a numeric range filter from stored payload.
   *
   * @param array<string, float|null>|null $data
   *   Stored range array.
   */
  private function importRange(mixed $data): ?RangeFilter {
    if (!is_array($data)) {
      return NULL;
    }

    $min = array_key_exists('min', $data) && $data['min'] !== NULL ? (float) $data['min'] : NULL;
    $max = array_key_exists('max', $data) && $data['max'] !== NULL ? (float) $data['max'] : NULL;
    $range = new RangeFilter($min, $max);

    return $range->isEmpty() ? NULL : $range;
  }

  /**
   * Imports map viewport bounds from a query string.
   */
  private function importViewport(mixed $raw): ?MapBounds {
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

}
