<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller\Htmx;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Htmx\Htmx;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTMX apply trigger for the desktop More filters offcanvas.
 */
final class FilterMoreApplyHtmxController extends ControllerBase {

  /**
   * Validates filter params and returns an HTMX apply trigger (no HTML swap).
   */
  public function applyMore(Request $request): array {
    $build = [
      '#markup' => '',
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args'],
      ],
    ];

    (new Htmx())->triggerAfterSettleHeader([
      'ps-search-filter-htmx-apply' => [
        'popinKey' => 'more',
      ],
    ])->applyTo($build);

    return $build;
  }

}
