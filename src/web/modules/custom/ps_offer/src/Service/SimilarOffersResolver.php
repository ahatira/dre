<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\views\Views;

/**
 * Resolves similar offer node IDs via the ps_offer_similar View.
 */
final class SimilarOffersResolver {

  /**
   * Returns similar offer node IDs for the current offer detail route.
   *
   * Contextual filters are resolved from the route node (operation, asset type,
   * exclude current offer).
   *
   * @return int[]
   *   Ordered node IDs, or empty when the view is unavailable or has no rows.
   */
  public function resolveNids(): array {
    $view = Views::getView('ps_offer_similar');
    if ($view === NULL) {
      return [];
    }

    $view->setDisplay('offer_detail_similar');
    $view->execute();
    if (empty($view->result)) {
      return [];
    }

    $nids = [];
    foreach ($view->result as $row) {
      if (isset($row->nid)) {
        $nids[] = (int) $row->nid;
      }
    }

    return $nids;
  }

}
