<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\search_api\Query\QueryInterface;

/**
 * Applies SearchContext constraints to Search API queries.
 */
interface SearchQueryExecutorInterface {

  /**
   * Applies business filters from the search context (excludes spatial zone).
   */
  public function applyBusinessFilters(QueryInterface $query, SearchContext $context): void;

  /**
   * Applies spatial constraints from the search context.
   */
  public function applySpatial(QueryInterface $query, SearchContext $context, ?MapBounds $legacyMapBounds = NULL): void;

  /**
   * Applies list sort from the search context.
   */
  public function applyListSort(QueryInterface $query, SearchContext $context): void;

  /**
   * Applies business filters and spatial constraints.
   */
  public function apply(QueryInterface $query, SearchContext $context, ?MapBounds $legacyMapBounds = NULL): void;

  /**
   * Whether legacy MapBoundsResolver bounds should be skipped for this context.
   */
  public function shouldSkipLegacyMapBounds(SearchContext $context): bool;

}
