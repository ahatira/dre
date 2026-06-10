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
  ) {}

  /**
   * Counts offers matching business filters only (no map zone).
   */
  public function countBusinessFilters(Request $request): int {
    $index = Index::load('offers');
    if (!$index) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);

    try {
      return (int) $query->execute()->getResultCount();
    }
    catch (\Exception) {
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
    $index = Index::load('offers');
    if (!$index) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);
    $this->filterQueryBuilder->applyMapBounds($query, $bounds);

    try {
      return (int) $query->execute()->getResultCount();
    }
    catch (\Exception) {
      return 0;
    }
  }

}
