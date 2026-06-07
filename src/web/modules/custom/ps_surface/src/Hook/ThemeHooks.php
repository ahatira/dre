<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Hook;

use Drupal\Core\Hook\Attribute\Hook;

/**
 * Theme hook definitions for ps_surface.
 */
final class ThemeHooks {

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_surface_division_table' => [
        'variables' => [
          'columns' => [],
          'rows' => [],
          'default_sort' => ['column' => 'lot', 'direction' => 'asc'],
          'table_id' => 'ps-surface-table',
        ],
      ],
    ];
  }

}
