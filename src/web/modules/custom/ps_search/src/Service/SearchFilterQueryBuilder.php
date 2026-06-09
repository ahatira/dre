<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies URL search filter parameters to a Search API query.
 */
final class SearchFilterQueryBuilder {

  /**
   * Max accepted server-side bound for surface filters (m²).
   */
  private const MAX_SURFACE = 200000.0;

  /**
   * Max accepted server-side bound for budget filters (€/m²/year).
   */
  private const MAX_BUDGET = 100000.0;

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly MoreCriteriaConditionApplier $moreCriteriaApplier,
    private readonly MapBoundsResolver $mapBoundsResolver,
  ) {}

  /**
   * Applies business search filters from the request (excludes map zone).
   */
  public function applyBusinessFilters(QueryInterface $query, Request $request): void {
    $operationType = $this->queryFacetCode($request, 'operation_type');
    $assetType = $this->queryFacetCode($request, 'asset_type');
    $localityTokens = $this->locationSearchFilter->extractTokensFromRequest($request);
    $surfaceMin = $this->queryRangeBound($request, 'surface', 'min', 'surface_min', self::MAX_SURFACE);
    $surfaceMax = $this->queryRangeBound($request, 'surface', 'max', 'surface_max', self::MAX_SURFACE);
    $budgetMin = $this->queryRangeBound($request, 'budget', 'min', 'budget_min', self::MAX_BUDGET);
    $budgetMax = $this->queryRangeBound($request, 'budget', 'max', 'budget_max', self::MAX_BUDGET);
    $capacityMin = $this->queryRangeBound($request, 'capacity', 'min', 'capacity_min', 500.0);
    $capacityMax = $this->queryRangeBound($request, 'capacity', 'max', 'capacity_max', 500.0);

    if ($operationType !== NULL) {
      $query->addCondition('field_operation_type', $operationType);
    }
    if ($assetType !== NULL) {
      $query->addCondition('field_asset_type', $assetType);
    }
    if ($localityTokens !== []) {
      $this->locationSearchFilter->applyToQuery($query, $localityTokens);
    }
    if ($surfaceMin !== NULL) {
      $query->addCondition('surface_total', $surfaceMin, '>=');
    }
    if ($surfaceMax !== NULL) {
      $query->addCondition('surface_total', $surfaceMax, '<=');
    }
    if ($budgetMin !== NULL) {
      $query->addCondition('field_budget_value', $budgetMin, '>=');
    }
    if ($budgetMax !== NULL) {
      $query->addCondition('field_budget_value', $budgetMax, '<=');
    }
    if ($capacityMin !== NULL) {
      $query->addCondition('field_capacity_total', $capacityMin, '>=');
    }
    if ($capacityMax !== NULL) {
      $query->addCondition('field_capacity_total', $capacityMax, '<=');
    }

    $this->moreCriteriaApplier->apply($query, $request);
  }

  /**
   * Applies the active map bounding box to a Search API query.
   */
  public function applyMapBounds(QueryInterface $query, MapBounds $bounds): void {
    $query->addCondition('field_geo_lat', $bounds->swLat, '>=');
    $query->addCondition('field_geo_lat', $bounds->neLat, '<=');
    $query->addCondition('field_geo_lng', $bounds->swLng, '>=');
    $query->addCondition('field_geo_lng', $bounds->neLng, '<=');
  }

  /**
   * Applies business filters and the active map zone from the request.
   */
  public function apply(QueryInterface $query, Request $request): void {
    $this->applyBusinessFilters($query, $request);
    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    if ($bounds instanceof MapBounds) {
      $this->applyMapBounds($query, $bounds);
    }
  }

  /**
   * Reads a facet filter code from scalar or Views/Facets bracket params.
   */
  private function queryFacetCode(Request $request, string $key): ?string {
    $value = $this->readQueryParameter($request, $key);
    if (is_string($value) && $value !== '') {
      return $this->sanitizeCode($value);
    }

    if (!is_array($value) || $value === []) {
      return NULL;
    }

    foreach ($value as $code => $facetValue) {
      if (is_string($code) && $code !== '') {
        $sanitized = $this->sanitizeCode($code);
        if ($sanitized !== NULL) {
          return $sanitized;
        }
      }
      if (is_string($facetValue) && $facetValue !== '') {
        $sanitized = $this->sanitizeCode($facetValue);
        if ($sanitized !== NULL) {
          return $sanitized;
        }
      }
    }

    return NULL;
  }

  /**
   * Reads a numeric range bound from API or BEF bracket query params.
   */
  private function queryRangeBound(
    Request $request,
    string $befKey,
    string $bound,
    string $flatKey,
    float $max,
  ): ?float {
    $flatRaw = $this->readQueryParameter($request, $flatKey);
    if (is_string($flatRaw) || is_int($flatRaw) || is_float($flatRaw)) {
      $flatValue = $this->sanitizePositiveNumber($flatRaw, $max);
      if ($flatValue !== NULL) {
        return $flatValue;
      }
    }

    $nested = $this->readQueryParameter($request, $befKey);
    if (!is_array($nested) || !array_key_exists($bound, $nested)) {
      return NULL;
    }

    return $this->sanitizePositiveNumber($nested[$bound], $max);
  }

  /**
   * Reads a scalar or array query parameter without InputBag type exceptions.
   */
  private function readQueryParameter(Request $request, string $key): mixed {
    if (!$request->query->has($key)) {
      return NULL;
    }

    try {
      return $request->query->all($key);
    }
    catch (BadRequestException) {
      return $request->query->getString($key);
    }
  }

  /**
   * Sanitizes a filter code: only A–Z letters, max 10 chars.
   */
  private function sanitizeCode(mixed $value): ?string {
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^A-Z]/i', '', strtoupper(substr($value, 0, 10)));
    return $cleaned !== '' ? $cleaned : NULL;
  }

  /**
   * Sanitizes a positive numeric value.
   */
  private function sanitizePositiveNumber(mixed $value, float $max): ?float {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    $num = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($num === FALSE || $num < 0) {
      return NULL;
    }
    return min((float) $num, $max);
  }

}
