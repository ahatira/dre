<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\ps_search\Contract\SearchQueryFactoryInterface;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Backward-compatible facade delegating to SearchQueryFactory.
 *
 * Prefer injecting SearchQueryFactoryInterface directly (L5 API).
 */
final class SearchFilterQueryBuilder {

  public function __construct(
    private readonly SearchQueryFactoryInterface $queryFactory,
  ) {}

  /**
   * Applies business search filters from the request (excludes map zone).
   */
  public function applyBusinessFilters(QueryInterface $query, Request $request): void {
    $this->queryFactory->applyBusinessFilters($query, $request);
  }

  /**
   * Applies list sort parameters from the URL (mirrors Views page_list sort).
   */
  public function applyListSort(QueryInterface $query, Request $request): void {
    $this->queryFactory->applyListSort($query, $request);
  }

  /**
   * Applies the active map bounding box to a Search API query.
   */
  public function applyMapBounds(QueryInterface $query, MapBounds $bounds): void {
    $this->queryFactory->applyMapBounds($query, $bounds);
  }

  /**
   * Applies business filters and optional map zone bounds from the request.
   */
  public function apply(QueryInterface $query, Request $request, ?MapBounds $bounds = NULL): void {
    $this->queryFactory->apply($query, $request, $bounds);
  }

}
