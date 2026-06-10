<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\ValueObject\MapBounds;
use Symfony\Component\HttpFoundation\Request;

/**
 * Counts search results and resolves geographic bounds for map auto-fit.
 */
interface SearchResultGeoBoundsResolverInterface {

  /**
   * Counts offers matching business filters only.
   */
  public function countBusinessFilters(Request $request): int;

  /**
   * Counts offers matching business filters within explicit map bounds.
   */
  public function countInBoundsWithBounds(Request $request, MapBounds $bounds): int;

  /**
   * Builds bounds containing all geo-located offers matching business filters.
   */
  public function resolveFromFilteredResults(Request $request): ?MapBounds;

}
