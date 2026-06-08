<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks for ps_search_filters.
 */
final class Theme {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_search_filter_bar' => [
        'variables' => [
          'operation_types' => [],
          'asset_types' => [],
          'more_criteria' => [],
          'active_op' => NULL,
          'active_flexible' => TRUE,
          'active_asset' => NULL,
          'active_op_label' => NULL,
          'active_asset_label' => NULL,
          'budget_config' => [],
          'lang_prefix' => '',
          'show_surface_filter' => TRUE,
          'show_capacity_filter' => FALSE,
          'capacity_filter_label' => 'Seats',
        ],
        'template' => 'ps-search-filter-bar',
      ],
    ];
  }

}
