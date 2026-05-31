<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Kernel;

use Drupal\Core\Routing\RouteProviderInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

#[RunTestsInSeparateProcesses]
final class PsCoreHubAccessKernelTest extends KernelTestBase {

  protected static $modules = [
    'system',
    'user',
    'field',
    'views',
    'ps_core',
  ];

  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installConfig(['ps_core']);

    $authenticated = Role::load('authenticated');
    if ($authenticated !== NULL) {
      $authenticated
        ->revokePermission('access ps_core content section')
        ->revokePermission('access ps_core structure section')
        ->revokePermission('access ps_core config section')
        ->revokePermission('administer ps_core')
        ->save();
    }
  }

  public function testSectionRoutesUseNativeAdminController(): void {
    /** @var \Drupal\Core\Routing\RouteProviderInterface $routeProvider */
    $routeProvider = $this->container->get('router.route_provider');
    self::assertInstanceOf(RouteProviderInterface::class, $routeProvider);

    self::assertSame(
      '\\Drupal\\system\\Controller\\SystemController::systemAdminMenuBlockPage',
      $routeProvider->getRouteByName('ps_core.content')->getDefault('_controller')
    );
    self::assertSame(
      '\\Drupal\\system\\Controller\\SystemController::systemAdminMenuBlockPage',
      $routeProvider->getRouteByName('ps_core.structure')->getDefault('_controller')
    );
    self::assertSame(
      '\\Drupal\\system\\Controller\\SystemController::systemAdminMenuBlockPage',
      $routeProvider->getRouteByName('ps_core.config')->getDefault('_controller')
    );

    self::assertSame(
      'access ps_core content section',
      $routeProvider->getRouteByName('ps_core.content')->getRequirement('_permission')
    );
    self::assertSame(
      'access ps_core structure section',
      $routeProvider->getRouteByName('ps_core.structure')->getRequirement('_permission')
    );
    self::assertSame(
      'access ps_core config section',
      $routeProvider->getRouteByName('ps_core.config')->getRequirement('_permission')
    );

  }

  public function testInstalledEditorRolePermissions(): void {
    $editorRole = Role::load('ps_content_editor');
    self::assertNotNull($editorRole);

    self::assertTrue($editorRole->hasPermission('access ps_core hub'));
    self::assertTrue($editorRole->hasPermission('access ps_core content section'));
    self::assertTrue($editorRole->hasPermission('access ps_core structure section'));
    self::assertFalse($editorRole->hasPermission('access ps_core config section'));
    self::assertFalse($editorRole->hasPermission('administer ps_core'));
  }

  public function testInstalledAdminRolePermissions(): void {
    $adminRole = Role::load('ps_admin');
    self::assertNotNull($adminRole);

    self::assertTrue($adminRole->hasPermission('access ps_core hub'));
    self::assertTrue($adminRole->hasPermission('access ps_core content section'));
    self::assertTrue($adminRole->hasPermission('access ps_core structure section'));
    self::assertTrue($adminRole->hasPermission('access ps_core config section'));
    self::assertTrue($adminRole->hasPermission('administer ps_core'));
  }

}
