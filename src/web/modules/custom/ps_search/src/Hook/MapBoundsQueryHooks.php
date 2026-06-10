<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\Service\SearchFilterQueryBuilder;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Applies active map bounds to Search API queries backing the search UI.
 *
 * - page_list: business filters + map zone (list cards and counts).
 * - map_attachment: zero results (empty geofield shell; markers via API).
 */
final class MapBoundsQueryHooks {

  /**
   * Search API query IDs that scope results to the active map zone.
   */
  private const ZONE_SCOPED_SEARCH_IDS = [
    'views_page:ps_search_offers__page_list',
  ];

  /**
   * Search API query ID for the empty geofield map shell (markers via API).
   */
  private const MAP_SHELL_SEARCH_ID = 'views_attachment:ps_search_offers__map_attachment';

  public function __construct(
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Implements hook_search_api_query_alter().
   */
  #[Hook('search_api_query_alter')]
  public function searchApiQueryAlter(QueryInterface $query): void {
    $searchId = (string) $query->getSearchId();

    if ($searchId === self::MAP_SHELL_SEARCH_ID) {
      $query->range(0, 0);
      return;
    }

    if (!in_array($searchId, self::ZONE_SCOPED_SEARCH_IDS, TRUE)) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    if (!$bounds instanceof MapBounds) {
      return;
    }

    $this->filterQueryBuilder->applyMapBounds($query, $bounds);
  }

}
