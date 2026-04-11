<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hooks for ps_diagnostic module.
 */
final class ThemeHooks {

  /**
   * Implements hook_theme().
   *
   * @return array<string, mixed>
   *   Theme definitions.
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_diagnostic_item' => [
        'variables' => [
          'item' => NULL,
          'diagnostic_type' => NULL,
          'class_label' => NULL,
          'type_label' => NULL,
          'numeric_value' => NULL,
          'valid_from' => NULL,
          'valid_to' => NULL,
          'no_classification' => FALSE,
          'non_applicable' => FALSE,
          'show_type_label' => FALSE,
          'show_numeric_value' => FALSE,
          'show_validity_dates' => FALSE,
          'default_layout' => 'horizontal',
          'dim_diagnostic' => FALSE,
          'dim_opacity' => 30,
        ],
        'template' => 'ps-diagnostic-item',
      ],
      'ps_diagnostic_item__horizontal' => [
        'variables' => [
          'item' => NULL,
          'diagnostic_type' => NULL,
          'class_label' => NULL,
          'type_label' => NULL,
          'numeric_value' => NULL,
          'valid_from' => NULL,
          'valid_to' => NULL,
          'no_classification' => FALSE,
          'non_applicable' => FALSE,
          'show_type_label' => FALSE,
          'show_numeric_value' => FALSE,
          'show_validity_dates' => FALSE,
          'default_layout' => 'horizontal',
          'dim_diagnostic' => FALSE,
          'dim_opacity' => 30,
        ],
        'template' => 'ps-diagnostic-item--horizontal',
      ],
      'ps_diagnostic_item__vertical' => [
        'variables' => [
          'item' => NULL,
          'diagnostic_type' => NULL,
          'class_label' => NULL,
          'type_label' => NULL,
          'numeric_value' => NULL,
          'valid_from' => NULL,
          'valid_to' => NULL,
          'no_classification' => FALSE,
          'non_applicable' => FALSE,
          'show_type_label' => FALSE,
          'show_numeric_value' => FALSE,
          'show_validity_dates' => FALSE,
          'default_layout' => 'vertical',
          'dim_diagnostic' => FALSE,
          'dim_opacity' => 30,
        ],
        'template' => 'ps-diagnostic-item--vertical',
      ],
      'ps_diagnostic_item__compact' => [
        'variables' => [
          'item' => NULL,
          'diagnostic_type' => NULL,
          'class_label' => NULL,
          'type_label' => NULL,
          'numeric_value' => NULL,
          'valid_from' => NULL,
          'valid_to' => NULL,
          'no_classification' => FALSE,
          'non_applicable' => FALSE,
          'show_type_label' => FALSE,
          'show_numeric_value' => FALSE,
          'show_validity_dates' => FALSE,
          'default_layout' => 'compact',
          'dim_diagnostic' => FALSE,
          'dim_opacity' => 30,
        ],
        'template' => 'ps-diagnostic-item--compact',
      ],
    ];
  }

}
