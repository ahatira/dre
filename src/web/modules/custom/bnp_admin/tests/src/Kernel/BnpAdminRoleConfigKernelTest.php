<?php

declare(strict_types=1);

namespace Drupal\Tests\bnp_admin\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\user\Entity\Role;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * @group bnp_admin
 */
#[RunTestsInSeparateProcesses]
final class BnpAdminRoleConfigKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'block',
    'path',
    'coffee',
    'sitewide_alert',
    'trash',
    'locale',
    'language',
    'content_translation',
    'masquerade',
    'entity_clone',
    'config_perms',
    'config_split',
    'redirect_after_login',
    'roleassign',
    'bnp_admin',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installConfig(['bnp_admin']);
  }

  /**
   * Ensures baseline BNP roles are installed with expected permissions.
   */
  public function testBaselineRolesAreInstalled(): void {
    $contentEditor = Role::load('content_editor');
    self::assertNotNull($contentEditor);
    self::assertTrue($contentEditor->hasPermission('access coffee'));
    self::assertTrue($contentEditor->hasPermission('view published sitewide alert entities'));

    $translateEditor = Role::load('translate_editor');
    self::assertNotNull($translateEditor);
    self::assertTrue($translateEditor->hasPermission('translate interface'));
    self::assertTrue($translateEditor->hasPermission('create content translations'));

    $siteAdmin = Role::load('site_admin');
    self::assertNotNull($siteAdmin);
    self::assertTrue($siteAdmin->hasPermission('administer configuration split'));
    self::assertTrue($siteAdmin->hasPermission('masquerade as any user'));
    self::assertFalse($siteAdmin->hasPermission('masquerade as super user'));

    $administrator = Role::load('administrator');
    self::assertNotNull($administrator);
    self::assertTrue($administrator->isAdmin());
    self::assertTrue($administrator->hasPermission('masquerade as super user'));
  }

  /**
   * Ensures module settings describe portable Gin branding defaults.
   */
  public function testModuleSettingsDefaults(): void {
    $settings = $this->config('bnp_admin.settings');
    self::assertTrue($settings->get('core.user_register_admin_only'));
    self::assertSame('gin', $settings->get('core.admin_theme'));
    self::assertTrue($settings->get('gin.enabled'));
    self::assertSame('green', $settings->get('gin.preset_accent_color'));
  }

}
