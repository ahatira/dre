<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Search\Push\SearchPushBlockBuilder;
use Drupal\views\ViewExecutable;

/**
 * Injects the push calculator card into search result rows.
 */
final class SearchPushViewsHooks {

  public function __construct(
    private readonly SearchPushBlockBuilder $pushBlockBuilder,
  ) {}

  /**
   * Implements hook_preprocess_HOOK() for views unformatted rows.
   */
  #[Hook('preprocess_views_view_unformatted')]
  public function preprocessViewsViewUnformatted(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable) {
      return;
    }

    if ($view->id() !== 'ps_search_offers' || ($view->current_display ?? '') !== 'page_list') {
      return;
    }

    $pageRowCount = count($variables['rows'] ?? []);
    $totalResults = (int) ($view->total_rows ?? 0);

    if (!$this->pushBlockBuilder->shouldDisplay($totalResults, $pageRowCount)) {
      return;
    }

    $pushCard = $this->pushBlockBuilder->build();
    if ($pushCard === NULL) {
      return;
    }

    $variables['search_push_card'] = $pushCard;
    $variables['search_push_insert_after'] = $this->pushBlockBuilder->getInsertAfterIndex();
  }

}
