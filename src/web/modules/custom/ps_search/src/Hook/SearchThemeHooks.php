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
          'capacity_config' => [],
          'hide_operation_section' => FALSE,
          'show_more_filters' => TRUE,
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
          'selected_sort_label' => '',
        ],
        'template' => 'ps-search-results-header',
      ],
      'ps_search_mobile_bottom_bar' => [
        'variables' => [],
        'template' => 'ps-search-mobile-bottom-bar',
      ],
      'ps_search_alert_offcanvas' => [
        'variables' => [
          'webform' => NULL,
        ],
        'template' => 'ps-search-alert-offcanvas',
      ],
      'ps_search_alert_criteria_display' => [
        'variables' => [
          'zones' => [],
          'criteria' => [],
        ],
        'template' => 'ps-search-alert-criteria-display',
      ],
      'ps_search_map_zone_controls' => [
        'variables' => [
          'search_transport_icons' => [],
        ],
        'template' => 'ps-search-map-zone-controls',
      ],
      'ps_search_homepage_entry' => [
        'variables' => [
          'operation_types' => [],
          'asset_types' => [],
          'active_op' => NULL,
          'active_flexible' => FALSE,
          'active_asset' => NULL,
          'active_op_label' => NULL,
          'active_asset_label' => NULL,
          'search_path' => '',
          'budget_config' => [],
          'show_surface_filter' => TRUE,
          'show_capacity_filter' => FALSE,
          'capacity_filter_label' => '',
          'capacity_config' => [],
          'hero_capacity_config' => [],
          'labels' => [],
        ],
        'template' => 'ps-search-homepage-entry',
      ],
      'ps_search_alert_digest_body' => [
        'variables' => [
          'list_title' => NULL,
          'offers' => [],
          'search_url' => NULL,
        ],
        'template' => 'ps-search-alert-digest-body',
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
