<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves how many list rows the map should mirror (filters + pagination).
 *
 * The page_list display paginates via load-more; the markers API scopes to the
 * same row window using ps_list_loaded_count.
 */
final class SearchListLoadedLimitResolver {

  /**
   * Query argument sent by the search page JS when reloading the map.
   */
  public const QUERY_ARG = 'ps_list_loaded_count';

  /**
   * Query argument for incremental marker fetches (load-more offset).
   */
  public const OFFSET_QUERY_ARG = 'ps_list_marker_offset';

  /**
   * Default load-more page size (views.view.ps_search_offers page_list pager).
   */
  private const DEFAULT_PAGE_SIZE = 40;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly SearchResultCounter $resultCounter,
  ) {}

  /**
   * Resolves the Search API range upper bound for map markers.
   */
  public function resolve(Request $request): int {
    $explicit = (int) $request->query->get(self::QUERY_ARG);
    if ($explicit > 0) {
      return min($explicit, 1000);
    }

    $threshold = max(1, (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('list_pager_threshold') ?? 100));
    $zoneCount = $this->resultCounter->countInBounds($request);
    if ($zoneCount > 0 && $zoneCount <= $threshold) {
      return $zoneCount;
    }

    return self::DEFAULT_PAGE_SIZE;
  }

  /**
   * Resolves Search API range offset for incremental marker loads.
   */
  public function resolveOffset(Request $request): int {
    $offset = (int) $request->query->get(self::OFFSET_QUERY_ARG, 0);
    return max(0, min($offset, 1000));
  }

  /**
   * Resolves the page size for a single marker fetch window.
   */
  public function resolvePageSize(Request $request, int $listLimit, int $offset): int {
    if ($offset > 0) {
      return max(1, min($listLimit - $offset, self::DEFAULT_PAGE_SIZE));
    }
    return max(1, $listLimit);
  }

}
