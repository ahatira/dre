<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_starterkit_split\Hook;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Extension\ThemeSettingsProvider;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Alter libraries.
 */
class LibraryInfoAlter {

  public const string FRAMEWORK_CSS_LIBRARY = 'ui_suite_bnp_starterkit_split/framework';

  public const array DEPENDENCIES_MAPPING = [
    'components.ui_suite_bnp--accordion' => [
      'ui_suite_bnp_starterkit_split/component_accordion',
    ],
    'components.ui_suite_bnp--accordion_item' => [
      'ui_suite_bnp_starterkit_split/component_accordion',
    ],
    'components.ui_suite_bnp--alert' => [
      'ui_suite_bnp_starterkit_split/component_alert',
    ],
    'components.ui_suite_bnp--badge' => [
      'ui_suite_bnp_starterkit_split/component_badge',
    ],
    'components.ui_suite_bnp--breadcrumb' => [
      'ui_suite_bnp_starterkit_split/component_breadcrumb',
    ],
    'components.ui_suite_bnp--button_group' => [
      'ui_suite_bnp_starterkit_split/component_button_group',
    ],
    'components.ui_suite_bnp--button_toolbar' => [
      'ui_suite_bnp_starterkit_split/component_button_group',
    ],
    'components.ui_suite_bnp--card' => [
      'ui_suite_bnp_starterkit_split/component_card',
    ],
    'components.ui_suite_bnp--card_body' => [
      'ui_suite_bnp_starterkit_split/component_card',
    ],
    'components.ui_suite_bnp--card_group' => [
      'ui_suite_bnp_starterkit_split/component_card',
    ],
    'components.ui_suite_bnp--card_overlay' => [
      'ui_suite_bnp_starterkit_split/component_card',
    ],
    'components.ui_suite_bnp--carousel' => [
      'ui_suite_bnp_starterkit_split/component_carousel',
    ],
    'components.ui_suite_bnp--carousel_item' => [
      'ui_suite_bnp_starterkit_split/component_carousel',
    ],
    'components.ui_suite_bnp--close_button' => [
      'ui_suite_bnp_starterkit_split/component_close_button',
    ],
    'components.ui_suite_bnp--dropdown' => [
      'ui_suite_bnp_starterkit_split/component_dropdown',
      'ui_suite_bnp_starterkit_split/component_button_group',
    ],
    'components.ui_suite_bnp--list_group' => [
      'ui_suite_bnp_starterkit_split/component_list_group',
    ],
    'components.ui_suite_bnp--list_group_item' => [
      'ui_suite_bnp_starterkit_split/component_list_group',
    ],
    'components.ui_suite_bnp--modal' => [
      'ui_suite_bnp_starterkit_split/component_modal',
    ],
    'components.ui_suite_bnp--nav' => [
      'ui_suite_bnp_starterkit_split/component_nav',
    ],
    'components.ui_suite_bnp--navbar' => [
      'ui_suite_bnp_starterkit_split/component_navbar',
    ],
    'components.ui_suite_bnp--navbar_nav' => [
      'ui_suite_bnp_starterkit_split/component_navbar',
    ],
    'components.ui_suite_bnp--offcanvas' => [
      'ui_suite_bnp_starterkit_split/component_offcanvas',
    ],
    'components.ui_suite_bnp--pagination' => [
      'ui_suite_bnp_starterkit_split/component_pagination',
    ],
    'components.ui_suite_bnp--progress' => [
      'ui_suite_bnp_starterkit_split/component_progress',
    ],
    'components.ui_suite_bnp--progress_stacked' => [
      'ui_suite_bnp_starterkit_split/component_progress',
    ],
    'components.ui_suite_bnp--spinner' => [
      'ui_suite_bnp_starterkit_split/component_spinner',
    ],
    'components.ui_suite_bnp--table' => [
      'ui_suite_bnp_starterkit_split/component_table',
    ],
    'components.ui_suite_bnp--table_cell' => [
      'ui_suite_bnp_starterkit_split/component_table',
    ],
    'components.ui_suite_bnp--table_row' => [
      'ui_suite_bnp_starterkit_split/component_table',
    ],
    'components.ui_suite_bnp--toast' => [
      'ui_suite_bnp_starterkit_split/component_toast',
    ],
    'components.ui_suite_bnp--toast_container' => [
      'ui_suite_bnp_starterkit_split/component_toast',
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
    // Attach dynamically to components the split CSS.
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
