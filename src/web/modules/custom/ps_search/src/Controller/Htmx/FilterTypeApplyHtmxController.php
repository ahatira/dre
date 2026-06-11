<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller\Htmx;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Htmx\Htmx;
use Symfony\Component\HttpFoundation\Request;

/**
 * HTMX apply trigger for the Property type filter popin (Phase 5A.2).
 */
final class FilterTypeApplyHtmxController extends ControllerBase {

  /**
   * Validates type params and returns an HTMX apply trigger (no HTML swap).
   *
   * The client keeps building the full navigation URL; this route confirms
   * the request server-side and fires ps-search-filter-htmx-apply.
   */
  public function applyType(Request $request): array {
    $build = [
      '#markup' => '',
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args'],
      ],
    ];

    (new Htmx())->triggerAfterSettleHeader([
      'ps-search-filter-htmx-apply' => [
        'popinKey' => 'type',
      ],
    ])->applyTo($build);

    return $build;
  }

}
