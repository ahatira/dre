<?php

declare(strict_types=1);

namespace Drupal\ps_context\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Local task adjustments for matrix admin pages.
 */
final class MatrixAdminHooks {

  /**
   * Implements hook_menu_local_tasks_alter().
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    $translateKeys = [
      'config_translation.local_tasks:entity.ps_context_rule.config_translation_overview',
      'config_translation.local_tasks:entity.ps_context_label_profile.config_translation_overview',
    ];

    foreach ($translateKeys as $key) {
      if (isset($data['tabs'][0][$key])) {
        $data['tabs'][0][$key]['#link']['title'] = new TranslatableMarkup('Translate');
      }
    }
  }

}
