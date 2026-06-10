<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search_filters\Service\SearchResultsHeaderBuilder;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * HTMX fragment for the search results pane header (Phase 5A.7).
 */
final class SearchResultsHeaderHtmxController extends ControllerBase {

  public function __construct(
    private readonly SearchResultsHeaderBuilder $resultsHeaderBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search_filters.results_header_builder'),
    );
  }

  /**
   * Returns the results header fragment for HTMX innerHTML swap.
   */
  public function header(): array {
    $view = Views::getView('ps_search_offers');
    if ($view === NULL) {
      return [
        '#markup' => '',
        '#cache' => ['max-age' => 0],
      ];
    }

    $view->setDisplay('page_list');
    return $this->resultsHeaderBuilder->buildRenderArray($view);
  }

}
