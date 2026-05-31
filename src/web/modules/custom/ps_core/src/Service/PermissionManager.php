<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\Core\Session\AccountProxyInterface;

final class PermissionManager {

  public function __construct(
    private readonly AccountProxyInterface $currentUser,
    private readonly RouteProviderInterface $routeProvider,
  ) {}

  public function hasPermission(string $permission): bool {
    return $this->currentUser->hasPermission($permission);
  }

  /**
   * Returns TRUE if user has at least one permission from the list.
   */
  public function hasAnyPermission(array $permissions): bool {
    foreach ($permissions as $permission) {
      if (is_string($permission) && $permission !== '' && $this->currentUser->hasPermission($permission)) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Returns TRUE if user has all permissions from the list.
   */
  public function hasAllPermissions(array $permissions): bool {
    foreach ($permissions as $permission) {
      if (!is_string($permission) || $permission === '' || !$this->currentUser->hasPermission($permission)) {
        return FALSE;
      }
    }
    return TRUE;
  }

  /**
   * Filters routes by required permission and route existence.
   *
   * @param array<string,string> $routeToPermission
   *   Associative array route_name => required_permission.
   *
   * @return string[]
   *   Allowed route names for current user.
   */
  public function allowedRoutes(array $routeToPermission): array {
    $allowed = [];
    foreach ($routeToPermission as $routeName => $permission) {
      if (!is_string($routeName) || !is_string($permission) || $routeName === '' || $permission === '') {
        continue;
      }
      if (!$this->currentUser->hasPermission($permission)) {
        continue;
      }
      try {
        $this->routeProvider->getRouteByName($routeName);
        $allowed[] = $routeName;
      }
      catch (\Throwable) {
        // Skip unknown routes.
      }
    }
    return $allowed;
  }

}
