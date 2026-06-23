<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Entity\Index;
use Symfony\Component\HttpFoundation\Request;

/**
 * Executes Search API count queries for global and zone-scoped results.
 */
final class SearchResultCounter {

  public function __construct(
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly ?SearchSolrCircuitBreaker $circuitBreaker = NULL,
  ) {}

  /**
   * Whether Solr queries should be skipped for this request.
   */
  private function skipSearch(): bool {
    return $this->circuitBreaker?->isUnavailable() ?? FALSE;
  }

  /**
   * Clears the circuit after a successful Solr query.
   */
  private function recordSearchSuccess(): void {
    $this->circuitBreaker?->recordSuccess();
  }

  /**
   * Opens the circuit after a Solr failure.
   */
  private function recordSearchFailure(\Throwable $exception): void {
    $this->circuitBreaker?->recordFailure($exception);
  }

  /**
   * Counts offers matching business filters only (no map zone).
   */
  public function countBusinessFilters(Request $request): int {
    if ($this->skipSearch()) {
      return 0;
    }

    $index = Index::load('offers');
    if (!$index) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);

    try {
      $count = (int) $query->execute()->getResultCount();
      $this->recordSearchSuccess();
      return $count;
    }
    catch (\Throwable $exception) {
      $this->recordSearchFailure($exception);
      return 0;
    }
  }

  /**
   * Counts offers matching business filters within the active map zone.
   */
  public function countInBounds(Request $request): int {
    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    if (!$bounds instanceof MapBounds) {
      return 0;
    }

    return $this->countInBoundsWithBounds($request, $bounds);
  }

  /**
   * Counts offers matching business filters within explicit map bounds.
   */
  public function countInBoundsWithBounds(Request $request, MapBounds $bounds): int {
    if ($this->skipSearch()) {
      return 0;
    }

    $index = Index::load('offers');
    if (!$index) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->apply($query, $request, $bounds);

    try {
      $count = (int) $query->execute()->getResultCount();
      $this->recordSearchSuccess();
      return $count;
    }
    catch (\Throwable $exception) {
      $this->recordSearchFailure($exception);
      return 0;
    }
  }

}
