<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Context;

use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\ps_search\ValueObject\RangeFilter;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\ps_search\ValueObject\SearchSort;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parses normalized business filters and sort from HTTP query parameters.
 */
final class SearchFiltersParser {

  private const MAX_SURFACE = 200000.0;

  private const MAX_BUDGET = 100000.0;

  private const MAX_CAPACITY = 500.0;

  /**
   * Allowed sort combinations (sort_by|sort_order).
   *
   * @var array<string, array{0: string, 1: string}>
   */
  private const ALLOWED_SORTS = [
    'surface_total|ASC' => ['surface_total', 'ASC'],
    'surface_total|DESC' => ['surface_total', 'DESC'],
    SearchSort::DISTANCE_SORT_FIELD . '|ASC' => [SearchSort::DISTANCE_SORT_FIELD, 'ASC'],
    'field_budget_value|ASC' => ['field_budget_value', 'ASC'],
    'field_budget_value|DESC' => ['field_budget_value', 'DESC'],
  ];

  public function __construct(
    private readonly SearchContentLanguageResolver $contentLanguageResolver,
  ) {}

  /**
   * Parses business filters from path facet codes and query string.
   *
   * @param array{operation_type: ?string, asset_type: ?string} $pathFacets
   */
  public function parseFilters(Request $request, array $pathFacets): SearchFilters {
    $operationType = $pathFacets['operation_type'] ?? $this->queryFacetCode($request, 'operation_type');
    $assetType = $pathFacets['asset_type'] ?? $this->queryFacetCode($request, 'asset_type');

    $surface = $this->parseRange($request, 'surface', 'surface_min', 'surface_max', self::MAX_SURFACE);
    $budget = $this->parseRange($request, 'budget', 'budget_min', 'budget_max', self::MAX_BUDGET);
    $capacity = $this->parseRange($request, 'capacity', 'capacity_min', 'capacity_max', self::MAX_CAPACITY);

    return new SearchFilters(
      operationType: $operationType,
      assetType: $assetType,
      surface: $surface,
      budget: $budget,
      capacity: $capacity,
      moreCriteria: [],
    );
  }

  /**
   * Parses sort parameters from the query string.
   */
  public function parseSort(Request $request): SearchSort {
    $sortBy = is_string($request->query->get('sort_by')) ? trim((string) $request->query->get('sort_by')) : '';
    $sortOrder = strtoupper(is_string($request->query->get('sort_order')) || is_numeric($request->query->get('sort_order'))
      ? (string) $request->query->get('sort_order')
      : SearchSort::DEFAULT_SORT_ORDER);

    if (!in_array($sortOrder, ['ASC', 'DESC'], TRUE)) {
      $sortOrder = SearchSort::DEFAULT_SORT_ORDER;
    }

    if ($sortBy !== '' && isset(self::ALLOWED_SORTS[$sortBy . '|' . $sortOrder])) {
      [$field, $order] = self::ALLOWED_SORTS[$sortBy . '|' . $sortOrder];
      return new SearchSort(sortBy: $field, sortOrder: $order);
    }

    if ($sortBy !== '') {
      foreach (self::ALLOWED_SORTS as [$field, $order]) {
        if ($field === $sortBy) {
          return new SearchSort(sortBy: $field, sortOrder: $order);
        }
      }
    }

    return new SearchSort(
      sortBy: SearchSort::DEFAULT_SORT_BY,
      sortOrder: SearchSort::DEFAULT_SORT_ORDER,
    );
  }

  /**
   * Resolves the content language for the search context.
   */
  public function resolveLangcode(Request $request): string {
    return $this->contentLanguageResolver->resolvePrimaryLangcode($request);
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

  private function parseRange(
    Request $request,
    string $befKey,
    string $flatMinKey,
    string $flatMaxKey,
    float $max,
  ): ?RangeFilter {
    $min = $this->queryRangeBound($request, $befKey, 'min', $flatMinKey, $max);
    $maxValue = $this->queryRangeBound($request, $befKey, 'max', $flatMaxKey, $max);

    if ($min === NULL && $maxValue === NULL) {
      return NULL;
    }

    return new RangeFilter(min: $min, max: $maxValue);
  }

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

  private function sanitizeCode(mixed $value): ?string {
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^A-Z]/i', '', strtoupper(substr($value, 0, 10)));
    return $cleaned !== '' ? $cleaned : NULL;
  }

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
