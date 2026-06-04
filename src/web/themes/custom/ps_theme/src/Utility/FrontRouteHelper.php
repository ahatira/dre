<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

/**
 * Detects Property Search public front routes.
 */
final class FrontRouteHelper {

  /**
   * @var list<string>
   */
  private const PUBLIC_ROUTES = [
    'view.ps_search_offers.page_list',
    '<front>',
  ];

  public static function isPublicRoute(): bool {
    if (\Drupal::service('path.matcher')->isFrontPage()) {
      return TRUE;
    }

    $routeMatch = \Drupal::routeMatch();
    $route = $routeMatch->getRouteName();
    if ($route === NULL) {
      return FALSE;
    }

    if (in_array($route, self::PUBLIC_ROUTES, TRUE)) {
      return TRUE;
    }

    if ($route === 'entity.node.canonical') {
      $node = $routeMatch->getParameter('node');
      return is_object($node) && method_exists($node, 'bundle') && $node->bundle() === 'offer';
    }

    return FALSE;
  }

}
