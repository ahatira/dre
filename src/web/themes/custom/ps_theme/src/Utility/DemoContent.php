<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Utility;

use Drupal\Core\Extension\ModuleExtensionList;

/**
 * Detects when demo content is owned by the ps_demo module.
 */
final class DemoContent {

  /**
   * Whether ps_demo export files exist (module need not be enabled yet).
   */
  public static function isManagedByPsDemo(?ModuleExtensionList $moduleExtensionList = NULL): bool {
    $paths = [
      DRUPAL_ROOT . '/modules/custom/ps_demo/content',
    ];

    if (\Drupal::moduleHandler()->moduleExists('ps_demo')) {
      $moduleExtensionList ??= \Drupal::service('extension.list.module');
      $paths[] = DRUPAL_ROOT . '/' . $moduleExtensionList->getPath('ps_demo') . '/content';
    }

    foreach (array_unique($paths) as $path) {
      if (!is_dir($path)) {
        continue;
      }
      foreach (glob($path . '/*/*.yml') ?: [] as $file) {
        if (is_readable($file)) {
          return TRUE;
        }
      }
    }

    return FALSE;
  }

}
