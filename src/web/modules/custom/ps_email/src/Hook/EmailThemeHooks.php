<?php

declare(strict_types=1);

namespace Drupal\ps_email\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook registrations for ps_email templates.
 */
final class EmailThemeHooks {

  /**
   * Registers email-specific theme hooks.
   */
  #[Hook('theme')]
  public function theme(): array {
    $cardVariables = [
      'title' => '',
      'reference' => '',
      'property_type' => '',
      'surface' => NULL,
      'location' => NULL,
      'price_amount' => '',
      'price_qualifiers' => '',
      'price_on_request_label' => '',
      'exclusive' => FALSE,
      'url' => '',
      'cta_label' => '',
      'image' => NULL,
      'image_alt' => '',
    ];

    $path = \Drupal::service('extension.list.theme')->getPath('ps_theme_email') . '/templates/cards';

    return [
      'offer_email_card_vertical' => [
        'variables' => $cardVariables,
        'template' => 'offer-email-card-vertical',
        'path' => $path,
      ],
      'offer_email_card_compact' => [
        'variables' => $cardVariables,
        'template' => 'offer-email-card-compact',
        'path' => $path,
      ],
    ];
  }

}
