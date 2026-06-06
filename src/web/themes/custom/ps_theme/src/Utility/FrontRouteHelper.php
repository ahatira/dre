<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Symfony\Component\Routing\Route;

/**
 * Detects front-office routes that use the Stellar page shell.
 *
 * ps_theme preprocess runs only when this theme is active (admin uses gin).
 * The shell (header/footer slots) must be available on all public pages, not
 * only on a hard-coded route whitelist.
 */
final class FrontRouteHelper {

  /**
   * Routes that must not render the full Stellar chrome.
   *
   * @var list<string>
   */
  private const EXCLUDED_ROUTES = [
    'system.ajax',
    'system.csrftoken',
    'big_pipe.nojs',
  ];

  public static function isPublicRoute(): bool {
    $route = \Drupal::routeMatch()->getRouteObject();
    if (!$route instanceof Route) {
      return FALSE;
    }

    if ($route->getOption('_admin_route')) {
      return FALSE;
    }

    $route_name = \Drupal::routeMatch()->getRouteName();
    if ($route_name !== NULL && in_array($route_name, self::EXCLUDED_ROUTES, TRUE)) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Whether editor tools should render on the public theme shell.
   *
   * Visitors keep the ISO maquette; editors with toolbar access see local tasks,
   * page title, primary actions, and help while previewing the public theme.
   */
  public static function shouldShowEditorTools(): bool {
    return \Drupal::currentUser()->hasPermission('access toolbar');
  }

}
