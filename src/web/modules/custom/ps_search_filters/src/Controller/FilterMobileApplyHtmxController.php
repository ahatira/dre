<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Htmx\Htmx;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTMX apply trigger for the mobile filters offcanvas (Phase 5A.6).
 */
final class FilterMobileApplyHtmxController extends ControllerBase {

  /**
   * Validates filter params and returns an HTMX apply trigger (no HTML swap).
   */
  public function applyMobile(Request $request): array {
    $build = [
      '#markup' => '',
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args'],
      ],
    ];

    (new Htmx())->triggerAfterSettleHeader([
      'ps-search-filter-htmx-apply' => [
        'popinKey' => 'mobile',
      ],
    ])->applyTo($build);

    return $build;
  }

}
