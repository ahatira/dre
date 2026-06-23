<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Contract\SearchQueryExecutorInterface;
use Drupal\ps_search\Search\Query\SearchListSortApplier;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\Service\SearchViewsQueryGate;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Applies BNPPRE list sort options to the search results Views query.
 */
final class SearchListSortQueryHooks {

  /**
   * Search API query IDs that should use the custom list sort applier.
   */
  private const LIST_SORT_SEARCH_IDS = [
    'views_page:ps_search_offers__page_list',
  ];

  public function __construct(
    private readonly SearchListSortApplier $sortApplier,
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

    if (!in_array((string) $query->getSearchId(), self::LIST_SORT_SEARCH_IDS, TRUE)) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $context = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);
    if ($context instanceof SearchContext && $this->engineSettings->isSearchContextEnabled()) {
      $sorts = &$query->getSorts();
      $sorts = [];
      $this->queryExecutor->applyListSort($query, $context);
      return;
    }

    $sorts = &$query->getSorts();
    $sorts = [];
    $this->sortApplier->apply($query, $request);
  }

}
