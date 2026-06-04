<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Service;

use Drupal\ps_theme\Utility\DemoContent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Runs legacy YAML installers when ps_demo is not providing content exports.
 */
final class LegacyDemoContentInstaller {

  /**
   * Creates a LegacyDemoContentInstaller instance.
   */
  public static function create(ContainerInterface $container): self {
    return new self();
  }

  /**
   * Imports menus and/or homepage from theme YAML when ps_demo is absent.
   */
  public function install(bool $menus = TRUE, bool $homepage = TRUE): void {
    if (DemoContent::isManagedByPsDemo()) {
      return;
    }

    if ($menus) {
      StellarMenuInstaller::create(\Drupal::getContainer())->install();
    }
    if ($homepage) {
      HomepageInstaller::create(\Drupal::getContainer())->install();
    }
  }

}
