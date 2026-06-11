<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller\Htmx;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Htmx\Htmx;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTMX apply trigger for the Location filter popin (Phase 5A.3).
 */
final class FilterLocationApplyHtmxController extends ControllerBase {

  /**
   * Validates location params and returns an HTMX apply trigger (no HTML swap).
   */
  public function applyLocation(Request $request): array {
    $build = [
      '#markup' => '',
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args'],
      ],
    ];

    (new Htmx())->triggerAfterSettleHeader([
      'ps-search-filter-htmx-apply' => [
        'popinKey' => 'location',
      ],
    ])->applyTo($build);

    return $build;
  }

}
