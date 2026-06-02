<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp\Hook;

use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Alter libraries.
 */
class LibraryInfoAlter {

  public function __construct(
    protected ThemeSettingsProvider $themeSettings,
  ) {}

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function alter(array &$libraries, string $extension): void {
    if ($extension != 'ui_suite_bnp') {
      return;
    }

    if (!isset($libraries['framework'])) {
      return;
    }

    $js_library = $this->themeSettings->getSetting('library.js_loading') ?? 'ui_suite_bnp/framework_js';
    if ($js_library) {
      $libraries['framework']['dependencies'][] = $js_library;
    }

    $css_library = $this->themeSettings->getSetting('library.css_loading') ?? 'ui_suite_bnp/framework_css_re';
    if ($css_library) {
      $libraries['framework']['dependencies'][] = $css_library;
    }
  }

}
