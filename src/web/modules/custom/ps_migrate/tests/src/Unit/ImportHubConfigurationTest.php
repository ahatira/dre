<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit;

use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\Yaml\Yaml;

/**
 * Validates CRM import hub routing and menu placement configuration.
 */
#[Group('ps_migrate')]
final class ImportHubConfigurationTest extends UnitTestCase {

  /**
   * CRM import overview route uses the dedicated import section permission.
   */
  public function testAdminOverviewRouteUsesImportSectionPermission(): void {
    $routing = $this->loadRouting('ps_migrate.admin_overview');

    self::assertSame(
      '\\Drupal\\system\\Controller\\SystemController::systemAdminMenuBlockPage',
      $routing['defaults']['_controller'],
    );
    self::assertSame(
      'access ps_core import section',
      $routing['requirements']['_permission'],
    );
  }

  /**
   * Rejection report must not depend on the config section permission.
   */
  public function testPostImportReportRouteDoesNotRequireConfigSection(): void {
    $routing = $this->loadRouting('ps_migrate.post_import_report');
    $permission = (string) $routing['requirements']['_permission'];

    self::assertStringContainsString('manage ps_migrate', $permission);
    self::assertStringNotContainsString('access ps_core config section', $permission);
  }

  /**
   * CRM import hub menu link is a direct child of the Property Search hub.
   */
  public function testImportMenuLinkIsHubSection(): void {
    $menu = $this->loadMenuLinks();

    self::assertArrayHasKey('ps_migrate.admin_overview', $menu);
    self::assertSame('ps_core.hub', $menu['ps_migrate.admin_overview']['parent']);
    self::assertSame(7, $menu['ps_migrate.admin_overview']['weight']);
  }

  /**
   * Loads a single route definition from ps_migrate.routing.yml.
   *
   * @return array<string, mixed>
   *   Parsed route definition.
   */
  private function loadRouting(string $routeName): array {
    $path = dirname(__DIR__, 3) . '/ps_migrate.routing.yml';
    $routes = Yaml::parseFile($path);
    self::assertIsArray($routes);
    self::assertArrayHasKey($routeName, $routes);

    return $routes[$routeName];
  }

  /**
   * Loads menu link definitions from ps_migrate.links.menu.yml.
   *
   * @return array<string, mixed>
   *   Parsed menu link definitions keyed by plugin ID.
   */
  private function loadMenuLinks(): array {
    $path = dirname(__DIR__, 3) . '/ps_migrate.links.menu.yml';
    $links = Yaml::parseFile($path);
    self::assertIsArray($links);

    return $links;
  }

}
