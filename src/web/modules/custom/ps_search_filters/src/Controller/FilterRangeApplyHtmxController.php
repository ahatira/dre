<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Htmx\Htmx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * HTMX apply trigger for range filter popins (Phase 5A.4).
 */
final class FilterRangeApplyHtmxController extends ControllerBase {

  /**
   * Allowed range popin keys.
   */
  private const ALLOWED_SECTIONS = ['surface', 'capacity', 'budget'];

  /**
   * Validates range params and returns an HTMX apply trigger (no HTML swap).
   */
  public function applyRange(string $section, Request $request): array {
    if (!in_array($section, self::ALLOWED_SECTIONS, TRUE)) {
      throw new NotFoundHttpException();
    }

    $build = [
      '#markup' => '',
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['url.query_args'],
      ],
    ];

    (new Htmx())->triggerAfterSettleHeader([
      'ps-search-filter-htmx-apply' => [
        'popinKey' => $section,
      ],
    ])->applyTo($build);

    return $build;
  }

}
