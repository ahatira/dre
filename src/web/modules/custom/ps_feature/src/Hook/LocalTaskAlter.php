<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Hook implementations for local tasks customization.
 */
class LocalTaskAlter {

  /**
   * Implements hook_menu_local_tasks_alter().
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    // Simplify translation tab titles to just "Traduire".
    if (isset($data['tabs'][0]['config_translation.local_tasks:entity.fb_feature_group.config_translation_overview'])) {
      $data['tabs'][0]['config_translation.local_tasks:entity.fb_feature_group.config_translation_overview']['#link']['title'] = new TranslatableMarkup('Translate');
    }
    if (isset($data['tabs'][0]['config_translation.local_tasks:entity.fb_feature_definition.config_translation_overview'])) {
      $data['tabs'][0]['config_translation.local_tasks:entity.fb_feature_definition.config_translation_overview']['#link']['title'] = new TranslatableMarkup('Translate');
    }
  }

}
