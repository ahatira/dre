<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Extension\ExtensionPathResolver;
use Psr\Log\LoggerInterface;

/**
 * Applies ps_theme shell config from config/install after the theme is default.
 *
 * Drupal's theme:enable imports block layout before theme regions are ready;
 * Block::preSave() then moves blocks to branding and disables them.
 * Re-apply install YAML once ps_theme is the default theme.
 */
final class ThemeShellInstaller {

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ExtensionPathResolver $extensionPathResolver,
    private readonly LoggerInterface $logger,
  ) {}

  /**
   * Imports block layout, shell menus and theme settings from config/install.
   */
  public function applyShellInstallConfig(): void {
    $installPath = $this->extensionPathResolver->getPath('theme', 'ps_theme') . '/config/install';
    if (!is_dir($installPath)) {
      throw new \RuntimeException('Missing ps_theme config/install directory.');
    }

    $storage = new FileStorage($installPath);
    $imported = 0;

    foreach ($storage->listAll() as $configName) {
      if (!$this->shouldImportConfig($configName)) {
        continue;
      }

      $data = $storage->read($configName);
      if (!is_array($data)) {
        continue;
      }

      $this->configFactory->getEditable($configName)->setData($data)->save(TRUE);
      $imported++;
    }

    $this->logger->notice('ps_theme: applied {count} shell install config objects.', [
      'count' => $imported,
    ]);
  }

  /**
   * Whether a config object from ps_theme/config/install should be imported.
   */
  private function shouldImportConfig(string $configName): bool {
    return str_starts_with($configName, 'block.block.ps_theme_')
      || str_starts_with($configName, 'system.menu.ps_')
      || $configName === 'ps_theme.settings';
  }

}
