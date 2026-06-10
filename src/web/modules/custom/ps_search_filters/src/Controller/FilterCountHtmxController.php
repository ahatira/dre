<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Service\SearchResultCounter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTMX fragments for live filter counts in the search filter bar.
 */
final class FilterCountHtmxController extends ControllerBase {

  public function __construct(
    private readonly SearchResultCounter $resultCounter,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.result_counter'),
    );
  }

  /**
   * Returns a count label fragment for HTMX innerHTML swap.
   *
   * Uses the same query parameters as GET /ps-search/count.
   */
  public function countLabel(Request $request): array {
    $count = $this->resultCounter->countBusinessFilters($request);

    return [
      '#theme' => 'ps_search_filter_count_label',
      '#count' => $count,
      '#cache' => [
        'contexts' => ['url.query_args'],
        'max-age' => 60,
      ],
    ];
  }

}
