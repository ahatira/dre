<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds Search API queries mirroring Views/BEF filter parameters.
 */
interface SearchQueryFactoryInterface {

  /**
   * Creates a query on the offers index with business filters and bounds.
   */
  public function createQuery(Request $request, ?MapBounds $bounds = NULL): QueryInterface;

  /**
   * Applies business search filters from the request (excludes map zone).
   */
  public function applyBusinessFilters(QueryInterface $query, Request $request): void;

  /**
   * Applies list sort parameters from the URL (mirrors Views page_list sort).
   */
  public function applyListSort(QueryInterface $query, Request $request): void;

  /**
   * Applies the active map bounding box to a Search API query.
   */
  public function applyMapBounds(QueryInterface $query, MapBounds $bounds): void;

  /**
   * Applies business filters and optional map zone bounds from the request.
   */
  public function apply(QueryInterface $query, Request $request, ?MapBounds $bounds = NULL): void;

}
