<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook registrations for Property Search.
 */
final class ThemeHooks {

  /**
   * Registers custom theme hooks.
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_language_switcher' => [
        'variables' => [
          'current_icon' => NULL,
          'current_label' => NULL,
          'current_langcode' => NULL,
          'links' => [],
        ],
      ],
      'ps_header_search_panel' => [
        'variables' => [
          'label' => NULL,
          'form' => NULL,
          'container' => 'container-fluid',
        ],
      ],
      'ps_header_search_trigger' => [
        'variables' => [
          'label' => NULL,
          'variant' => 'toolbar',
        ],
      ],
      'ps_homepage_contact_offcanvas_placeholder' => [
        'variables' => [
          'message' => NULL,
        ],
      ],
      'ps_form_offcanvas' => [
        'variables' => [
          'webform' => NULL,
          'webform_id' => NULL,
          'panel_id' => NULL,
        ],
        'template' => 'ps-form-offcanvas-panel',
      ],
      'ps_offer_webform_modal' => [
        'variables' => [
          'webform' => NULL,
          'webform_id' => NULL,
          'panel_id' => NULL,
        ],
        'template' => 'ps-offer-webform-modal-panel',
      ],
      'ps_mega_menu_tools' => [
        'variables' => [],
        'template' => 'ps-mega-menu-tools',
      ],
    ];
  }

}
