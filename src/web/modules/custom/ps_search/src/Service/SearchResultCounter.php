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
    return $this->executeCount($request, FALSE);
  }

  /**
   * Counts offers matching business filters within the active map zone.
   */
  public function countInBounds(Request $request): int {
    return $this->executeCount($request, TRUE);
  }

  /**
   * Runs a zero-range Search API query and returns the result count.
   */
  private function executeCount(Request $request, bool $includeBounds): int {
    $index = Index::load('offers');
    if (!$index) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);

    if ($includeBounds) {
      $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
      if ($bounds instanceof MapBounds) {
        $this->filterQueryBuilder->applyMapBounds($query, $bounds);
      }
    }

    try {
      return (int) $query->execute()->getResultCount();
    }
    catch (\Exception) {
      return 0;
    }
  }

}
