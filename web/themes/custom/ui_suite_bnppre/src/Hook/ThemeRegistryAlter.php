<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Add theme key variables.
 */
class ThemeRegistryAlter {

  /**
   * Implements hook_theme_registry_alter().
   */
  #[Hook('theme_registry_alter')]
  public function alter(array &$themeRegistry): void {
    foreach ($themeRegistry as $themeKey => $themeDefinition) {
      // Skip theme hooks that don't set variables.
      if (!isset($themeRegistry[$themeKey]['variables']) || !\is_array($themeRegistry[$themeKey]['variables'])) {
        continue;
      }
      $themeRegistry[$themeKey]['variables'] += [
        'context' => [],
      ];
    }
  }

}
