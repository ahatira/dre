<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook implementations for diagnostic field formatters.
 */
final class DiagnosticThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    $variables = [
      'item' => NULL,
      'type_id' => NULL,
      'type_label' => NULL,
      'unit' => NULL,
      'value_display' => NULL,
      'diagnostic_date_display' => NULL,
      'validity_end_date_display' => NULL,
      'show_dates' => TRUE,
      'show_ranges' => TRUE,
      'show_unknown_banner' => TRUE,
      'show_reference_table' => TRUE,
      'is_disabled' => FALSE,
      'disabled_reason' => NULL,
      'disabled_message' => NULL,
      'classes' => [],
      'scale_rows' => [],
      'active_class' => NULL,
      'legend_low' => NULL,
      'legend_high' => NULL,
    ];

    return [
      'ps_diagnostic_item_horizontal' => [
        'variables' => $variables,
        'template' => 'diagnostic-item-horizontal',
      ],
      'ps_diagnostic_item_vertical' => [
        'variables' => $variables,
        'template' => 'diagnostic-item-vertical',
      ],
      'ps_diagnostic_item_full' => [
        'variables' => $variables,
        'template' => 'diagnostic-item-full',
      ],
    ];
  }

}
