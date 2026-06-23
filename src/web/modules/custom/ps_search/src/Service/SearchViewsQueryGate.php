<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\ps_search\Plugin\views\query\SearchContextViewsQuery;
use Drupal\search_api\Query\QueryInterface;
use Drupal\views\ViewExecutable;

/**
 * Detects when the SearchContext Views query plugin owns filter application.
 */
final class SearchViewsQueryGate {

  public function __construct(
    private readonly SearchEngineSettingsReader $engineSettings,
  ) {}

  /**
   * Whether v2 SearchContextViewsQuery already applied filters for this query.
   */
  public function isHandledByContextQuery(QueryInterface $query): bool {
    if (!$this->engineSettings->isSearchContextEnabled()) {
      return FALSE;
    }

    if (in_array('ps_search_context_query', $query->getTags(), TRUE)) {
      return TRUE;
    }

    $view = $query->getOption('search_api_view');
    if (!$view instanceof ViewExecutable) {
      return FALSE;
    }

    return $view->getQuery() instanceof SearchContextViewsQuery;
  }

}
