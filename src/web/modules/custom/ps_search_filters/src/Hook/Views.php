<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search_filters\Service\FilterBarBuilder;
use Drupal\ps_search_filters\Service\SearchResultsHeaderBuilder;
use Drupal\views\ViewExecutable;

/**
 * Injects the search filter bar on the public search view.
 */
final class Views {

  public function __construct(
    private readonly FilterBarBuilder $filterBarBuilder,
    private readonly SearchResultsHeaderBuilder $resultsHeaderBuilder,
  ) {}

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable || $view->id() !== 'ps_search_offers') {
      return;
    }

    if (($variables['display_id'] ?? '') !== 'page_list') {
      return;
    }

    $variables['search_filter_bar'] = $this->filterBarBuilder->build();
    $variables['search_filter_bar_mobile_actions'] = $this->filterBarBuilder->buildMobileActions();
    $variables['search_results_header'] = $this->resultsHeaderBuilder->buildRenderArray($view);
  }

}
