<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Alter libraries.
 */
class LibraryInfoAlter {

  public const string FRAMEWORK_CSS_LIBRARY = 'ps_theme/framework';

  public const array DEPENDENCIES_MAPPING = [
    'components.ui_suite_bnp--accordion' => [
      'ps_theme/component_accordion',
    ],
    'components.ui_suite_bnp--accordion_item' => [
      'ps_theme/component_accordion',
    ],
    'components.ui_suite_bnp--alert' => [
      'ps_theme/component_alert',
    ],
    'components.ui_suite_bnp--badge' => [
      'ps_theme/component_badge',
    ],
    'components.ui_suite_bnp--breadcrumb' => [
      'ps_theme/component_breadcrumb',
    ],
    'components.ui_suite_bnp--button_group' => [
      'ps_theme/component_button_group',
    ],
    'components.ui_suite_bnp--button_toolbar' => [
      'ps_theme/component_button_group',
    ],
    'components.ui_suite_bnp--card' => [
      'ps_theme/component_card',
    ],
    'components.ui_suite_bnp--card_body' => [
      'ps_theme/component_card',
    ],
    'components.ui_suite_bnp--card_group' => [
      'ps_theme/component_card',
    ],
    'components.ui_suite_bnp--card_overlay' => [
      'ps_theme/component_card',
    ],
    'components.ui_suite_bnp--carousel' => [
      'ps_theme/component_carousel',
    ],
    'components.ui_suite_bnp--carousel_item' => [
      'ps_theme/component_carousel',
    ],
    'components.ui_suite_bnp--close_button' => [
      'ps_theme/component_close_button',
    ],
    'components.ui_suite_bnp--dropdown' => [
      'ps_theme/component_dropdown',
      'ps_theme/component_button_group',
    ],
    'components.ui_suite_bnp--list_group' => [
      'ps_theme/component_list_group',
    ],
    'components.ui_suite_bnp--list_group_item' => [
      'ps_theme/component_list_group',
    ],
    'components.ui_suite_bnp--modal' => [
      'ps_theme/component_modal',
    ],
    'components.ui_suite_bnp--nav' => [
      'ps_theme/component_nav',
    ],
    'components.ui_suite_bnp--navbar' => [
      'ps_theme/component_navbar',
    ],
    'components.ui_suite_bnp--navbar_nav' => [
      'ps_theme/component_navbar',
    ],
    'components.ui_suite_bnp--offcanvas' => [
      'ps_theme/component_offcanvas',
    ],
    'components.ui_suite_bnp--pagination' => [
      'ps_theme/component_pagination',
    ],
    'components.ui_suite_bnp--progress' => [
      'ps_theme/component_progress',
    ],
    'components.ui_suite_bnp--progress_stacked' => [
      'ps_theme/component_progress',
    ],
    'components.ui_suite_bnp--spinner' => [
      'ps_theme/component_spinner',
    ],
    'components.ui_suite_bnp--table' => [
      'ps_theme/component_table',
    ],
    'components.ui_suite_bnp--table_cell' => [
      'ps_theme/component_table',
    ],
    'components.ui_suite_bnp--table_row' => [
      'ps_theme/component_table',
    ],
    'components.ui_suite_bnp--toast' => [
      'ps_theme/component_toast',
    ],
    'components.ui_suite_bnp--toast_container' => [
      'ps_theme/component_toast',
    ],
  ];

  public function __construct(
    protected ThemeSettingsProvider $themeSettings,
  ) {}

  /**
   * Implements hook_library_info_alter().
   */
  #[Hook('library_info_alter')]
  public function alter(array &$libraries, string $extension): void {
    if ($extension != 'core') {
      return;
    }

    $css_library = $this->themeSettings->getSetting('library.css_loading') ?? '';
    // Parent framework_css_re ships component CSS — split mapping only for legacy mode.
    if ($css_library === 'ui_suite_bnp/framework_css_re' || $css_library === '') {
      return;
    }
    if ($css_library != static::FRAMEWORK_CSS_LIBRARY) {
      return;
    }

    foreach (static::DEPENDENCIES_MAPPING as $library => $dependencies) {
      if (!isset($libraries[$library]) || !\is_array($libraries[$library])) {
        continue;
      }

      $libraries[$library] = NestedArray::mergeDeepArray([$libraries[$library], ['dependencies' => $dependencies]]);
    }
  }

}
