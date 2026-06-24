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
