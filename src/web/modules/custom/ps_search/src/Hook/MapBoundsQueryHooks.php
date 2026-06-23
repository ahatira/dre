<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Contract\SearchQueryExecutorInterface;
use Drupal\ps_search\Contract\SearchQueryFactoryInterface;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\Service\SearchViewsQueryGate;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Applies active map bounds to Search API queries backing the search UI.
 *
 * page_list: business filters + map zone (list cards and counts).
 * Map markers render via /api/ps/markers on the PS map shell.
 */
final class MapBoundsQueryHooks {

  /**
   * Search API query IDs that scope results to the active map zone.
   */
  private const ZONE_SCOPED_SEARCH_IDS = [
    'views_page:ps_search_offers__page_list',
  ];

  public function __construct(
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly SearchQueryFactoryInterface $queryFactory,
    private readonly SearchQueryExecutorInterface $queryExecutor,
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly RequestStack $requestStack,
    private readonly SearchViewsQueryGate $viewsQueryGate,
  ) {}

  /**
   * Implements hook_search_api_query_alter().
   */
  #[Hook('search_api_query_alter')]
  public function searchApiQueryAlter(QueryInterface $query): void {
    if ($this->viewsQueryGate->isHandledByContextQuery($query)) {
      return;
    }

    $searchId = (string) $query->getSearchId();
    if (!in_array($searchId, self::ZONE_SCOPED_SEARCH_IDS, TRUE)) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $context = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);
    if ($context instanceof SearchContext
      && $this->engineSettings->isSearchContextEnabled()
      && $this->queryExecutor->shouldSkipLegacyMapBounds($context)) {
      return;
    }

    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    if (!$bounds instanceof MapBounds) {
      return;
    }

    $this->queryFactory->applyMapBounds($query, $bounds);
  }

}
