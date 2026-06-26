<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Normalizes flat search query params for Views numeric "between" exposed filters.
 *
 * Homepage hero and filter bar submit surface_min=200 while the Views filter
 * identifier surface_min expects surface_min[min]=200 (array shape).
 *
 * Also unwraps array query values for BEF single (boolean) filters so contrib
 * Single widget does not trigger "Array to string conversion" warnings.
 */
final class SearchExposedFiltersQueryNormalizer {

  /**
   * Maps Views exposed identifier => [flat min key, flat max key].
   *
   * @var array<string, array{0: string, 1: string}>
   */
  private const BETWEEN_FILTERS = [
    'surface_min' => ['surface_min', 'surface_max'],
  ];

  /**
   * Core boolean exposed identifiers using the BEF single checkbox widget.
   *
   * @var list<string>
   */
  private const BOOLEAN_FILTER_IDENTIFIERS = [
    'divisible',
    'has_immersive_tour',
    'has_video',
  ];

  /**
   * Converts flat min/max query params to Views BEF array shape.
   */
  public function normalize(Request $request): void {
    self::normalizeRequest($request);
  }

  /**
   * Static entry point for path processors and legacy subscribers.
   */
  public static function normalizeRequest(Request $request): void {
    $normalizer = new self();
    foreach (self::BETWEEN_FILTERS as $identifier => [$flatMinKey, $flatMaxKey]) {
      $normalizer->normalizeBetweenFilter($request, $identifier, $flatMinKey, $flatMaxKey);
    }
    foreach (self::BOOLEAN_FILTER_IDENTIFIERS as $identifier) {
      $normalizer->normalizeBooleanFilter($request, $identifier);
    }
    foreach (array_keys($request->query->all()) as $key) {
      if (is_string($key) && str_starts_with($key, 'feature_')) {
        $normalizer->normalizeBooleanFilter($request, $key);
      }
    }
  }

  /**
   * Normalizes one exposed between-filter identifier.
   */
  private function normalizeBetweenFilter(
    Request $request,
    string $identifier,
    string $flatMinKey,
    string $flatMaxKey,
  ): void {
    $current = $this->readQueryValue($request, $identifier);
    if (is_array($current)) {
      return;
    }

    if (is_string($current) || is_int($current) || is_float($current)) {
      $max = $this->readScalarQueryValue($request, $flatMaxKey);
      $request->query->set($identifier, [
        'min' => (string) $current,
        'max' => $max ?? '',
      ]);
      return;
    }

    $min = $this->readScalarQueryValue($request, $flatMinKey);
    $max = $this->readScalarQueryValue($request, $flatMaxKey);
    if ($min === NULL && $max === NULL) {
      return;
    }

    $request->query->set($identifier, [
      'min' => $min ?? '',
      'max' => $max ?? '',
    ]);
  }

  /**
   * Unwraps list-shaped query values for boolean BEF single filters.
   */
  private function normalizeBooleanFilter(Request $request, string $identifier): void {
    if (!$request->query->has($identifier)) {
      return;
    }

    $value = $this->readQueryValue($request, $identifier);
    if (!is_array($value)) {
      return;
    }

    if ($value === []) {
      $request->query->remove($identifier);
      return;
    }

    if (!array_is_list($value)) {
      return;
    }

    $first = reset($value);
    if (is_string($first) || is_int($first) || is_float($first) || is_bool($first)) {
      $request->query->set($identifier, (string) $first);
    }
  }

  /**
   * Reads a query parameter value, scalar or nested array.
   */
  private function readQueryValue(Request $request, string $key): mixed {
    if (!$request->query->has($key)) {
      return NULL;
    }

    try {
      return $request->query->all($key);
    }
    catch (BadRequestException) {
      return $request->query->get($key);
    }
  }

  /**
   * Reads a scalar query parameter value when present.
   */
  private function readScalarQueryValue(Request $request, string $key): ?string {
    $value = $this->readQueryValue($request, $key);
    if (is_array($value)) {
      return NULL;
    }
    if (is_string($value) || is_int($value) || is_float($value)) {
      return (string) $value;
    }

    return NULL;
  }

}
