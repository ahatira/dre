<?php

declare(strict_types=1);

namespace Drupal\ps_price\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook implementations for ps_price.
 */
final class PriceThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_price_offer' => [
        'variables' => [
          'value_prefix' => '',
          'value' => '',
          'unit' => '',
          'tooltip_text' => '',
          'tooltip_html' => '',
          'tooltip_aria' => '',
        ],
        'template' => 'ps-price-offer',
      ],
    ];
  }

}
