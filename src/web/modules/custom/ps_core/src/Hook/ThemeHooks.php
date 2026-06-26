<?php

declare(strict_types=1);

namespace Drupal\ps_core\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks for ps_core shared UI fragments.
 */
final class ThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_core_site_urgency_help' => [
        'variables' => [
          'lead' => '',
          'phone_display' => '',
          'phone_href' => '',
          'hours' => '',
        ],
        'template' => 'ps-core-site-urgency-help',
      ],
      'ps_core_health_admin_overview' => [
        'variables' => [
          'overall_status' => 'healthy',
          'overall_label' => '',
          'lead' => '',
          'refresh_hint' => '',
          'summary_cards' => [],
          'groups' => [],
        ],
        'template' => 'ps-core-health-admin-overview',
      ],
    ];
  }

}
