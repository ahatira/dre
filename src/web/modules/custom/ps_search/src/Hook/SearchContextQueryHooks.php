<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Contract\SearchQueryExecutorInterface;
use Drupal\ps_search\Service\MapBoundsResolver;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\Service\SearchViewsQueryGate;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Applies SearchContext via SearchQueryExecutor on Views search queries.
 */
final class SearchContextQueryHooks {

  /**
   * Search API query IDs driven by SearchContext in v2.
   *
   * @var list<string>
   */
  private const SEARCH_CONTEXT_QUERY_IDS = [
    'views_page:ps_search_offers__page_list',
  ];

  public function __construct(
    private readonly SearchQueryExecutorInterface $queryExecutor,
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly MapBoundsResolver $mapBoundsResolver,
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

    if (!$this->engineSettings->isSearchContextEnabled()) {
      return;
    }

    if (!in_array((string) $query->getSearchId(), self::SEARCH_CONTEXT_QUERY_IDS, TRUE)) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $context = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);
    if (!$context instanceof SearchContext) {
      return;
    }

    $legacyBounds = $this->queryExecutor->shouldSkipLegacyMapBounds($context)
      ? NULL
      : $this->mapBoundsResolver->resolveActiveBounds($request);

    $this->queryExecutor->applySpatial($query, $context, $legacyBounds);
  }

}
