<?php

declare(strict_types=1);

namespace Drupal\Tests\bnp_editor\Unit\Service;

use Drupal\bnp_editor\Service\BnpEditorRoleInstaller;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @group bnp_editor
 */
final class BnpEditorRoleInstallerTest extends UnitTestCase {

  /**
   * Ensures the permission map targets BNP baseline roles, not legacy "editor".
   */
  public function testBuildRolePermissionMapUsesBnpRoles(): void {
    $installer = new BnpEditorRoleInstaller(
      $this->createMock(EntityTypeManagerInterface::class),
      $this->createStub(ModuleHandlerInterface::class),
    );

    $map = $installer->buildRolePermissionMap();

    self::assertArrayHasKey('content_editor', $map);
    self::assertArrayHasKey('administrator', $map);
    self::assertArrayNotHasKey('editor', $map);
    self::assertContains('use text format basic_html', $map['content_editor']);
    self::assertContains('administer bnp editor', $map['administrator']);
  }

  /**
   * Ensures PS overlay roles are not referenced (RBAC Option A).
   */
  public function testBuildRolePermissionMapExcludesLegacyPsRoles(): void {
    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->method('moduleExists')->with('ps_core')->willReturn(TRUE);

    $installer = new BnpEditorRoleInstaller(
      $this->createMock(EntityTypeManagerInterface::class),
      $moduleHandler,
    );

    $map = $installer->buildRolePermissionMap();

    self::assertArrayNotHasKey('ps_admin', $map);
    self::assertArrayNotHasKey('ps_content_editor', $map);
    self::assertContains('use text format full_html', $map['content_admin']);
  }

}
