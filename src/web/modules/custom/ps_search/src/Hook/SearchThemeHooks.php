<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks for ps_search filter UI..
 */
final class SearchThemeHooks {

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
          'more_criteria_groups' => [],
          'core_criteria_items' => [],
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
      'ps_search_filter_bar_mobile_actions' => [
        'variables' => [],
        'template' => 'ps-search-filter-bar-mobile-actions',
      ],
      'ps_search_more_criteria_items' => [
        'variables' => [
          'items' => [],
          'checkbox_two_columns' => FALSE,
          'id_prefix' => 'ps-more',
        ],
        'template' => 'ps-search-more-criteria-items',
      ],
      'ps_search_filter_count_label' => [
        'variables' => [
          'count' => 0,
        ],
        'template' => 'ps-search-filter-count-label',
      ],
      'ps_search_results_header' => [
        'variables' => [
          'title' => '',
          'count' => 0,
          'zone_count' => 0,
          'sort_options' => [],
        ],
        'template' => 'ps-search-results-header',
      ],
    ];
  }

  /**
   * Implements hook_preprocess_HOOK().
   */
  #[Hook('preprocess_ps_search_more_criteria_items')]
  public function preprocessMoreCriteriaItems(array &$variables): void {
    $checkboxCount = 0;
    foreach ($variables['items'] as $item) {
      if (($item['widget'] ?? 'checkbox') === 'checkbox') {
        $checkboxCount++;
      }
    }
    $variables['checkbox_two_columns'] = $checkboxCount > 2;
  }

}
