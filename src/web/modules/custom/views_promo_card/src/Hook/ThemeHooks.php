<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook registrations.
 */
final class ThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'promo_card_preview_page' => [
        'render element' => 'page',
        'template' => 'promo-card-preview-page',
      ],
    ];
  }

}
