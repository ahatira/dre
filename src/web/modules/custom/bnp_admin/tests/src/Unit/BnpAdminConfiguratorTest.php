<?php

declare(strict_types=1);

namespace Drupal\Tests\bnp_admin\Unit;

use Drupal\bnp_admin\BnpAdminConfigurator;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Tests\UnitTestCase;

/**
 * @group bnp_admin
 */
final class BnpAdminConfiguratorTest extends UnitTestCase {

  /**
   * Ensures baseline application updates expected editable configs.
   */
  public function testApplyBaselineUpdatesEditableConfigs(): void {
    $bnpSettings = $this->createMock(ImmutableConfig::class);
    $bnpSettings->method('get')->willReturnMap([
      ['core.user_register_admin_only', TRUE],
      ['core.node_use_admin_theme', TRUE],
      ['core.admin_theme', 'gin'],
      ['gin.enabled', TRUE],
      ['gin.preset_accent_color', 'green'],
      ['gin.classic_toolbar', 'new'],
      ['gin.show_description_toggle', TRUE],
      ['gin_login.enabled', TRUE],
      ['gin_login.brand_image_use_default', TRUE],
    ]);

    $savedConfigs = [];
    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->with('bnp_admin.settings')->willReturn($bnpSettings);
    $configFactory->method('getEditable')->willReturnCallback(
      function (string $name) use (&$savedConfigs): Config {
        $editable = $this->createMock(Config::class);
        $editable->method('set')->willReturnSelf();
        $editable->expects(self::once())->method('save')->with(TRUE)->willReturnCallback(
          static function () use (&$savedConfigs, $name): void {
            $savedConfigs[] = $name;
          },
        );
        return $editable;
      },
    );

    $moduleExtensionList = $this->createMock(ModuleExtensionList::class);
    $moduleExtensionList->method('getPath')->with('bnp_admin')->willReturn('modules/custom/bnp_admin');

    $themeExtensionList = $this->createMock(ThemeExtensionList::class);
    $themeExtensionList->method('getPath')->with('gin')->willReturn('themes/contrib/gin');

    $configurator = new BnpAdminConfigurator(
      $configFactory,
      $moduleExtensionList,
      $themeExtensionList,
      $this->createMock(\Drupal\Core\Entity\EntityTypeManagerInterface::class),
    );
    $configurator->applyBaseline();

    self::assertSame(
      [
        'user.settings',
        'node.settings',
        'system.theme',
        'gin.settings',
        'gin_login.settings',
      ],
      $savedConfigs,
    );
  }

}
