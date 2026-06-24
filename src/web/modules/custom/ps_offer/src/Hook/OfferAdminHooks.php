<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Admin UI tweaks for the offer configuration hub.
 */
final class OfferAdminHooks {

  /**
   * Offer config translation local task plugin IDs.
   *
   * @var list<string>
   */
  private const TRANSLATION_TASKS = [
    'config_translation.local_tasks:config_translation.item.overview.ps_offer.budget_display_settings',
    'config_translation.local_tasks:config_translation.item.overview.ps_offer.budget_popover_settings',
    'config_translation.local_tasks:config_translation.item.overview.ps_offer.surface_display_settings',
    'config_translation.local_tasks:config_translation.item.overview.ps_offer.media_settings',
    'config_translation.local_tasks:config_translation.item.overview.ps_offer.section_settings',
  ];

  /**
   * Maps settings and translate routes to their translation local task.
   *
   * @var array<string, string>
   */
  private const ROUTE_TRANSLATION_TASK = [
    'ps_offer.budget_display_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.budget_display_settings',
    'config_translation.item.overview.ps_offer.budget_display_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.budget_display_settings',
    'ps_offer.budget_popover_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.budget_popover_settings',
    'config_translation.item.overview.ps_offer.budget_popover_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.budget_popover_settings',
    'ps_offer.surface_display_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.surface_display_settings',
    'config_translation.item.overview.ps_offer.surface_display_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.surface_display_settings',
    'ps_offer.media_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.media_settings',
    'config_translation.item.overview.ps_offer.media_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.media_settings',
    'ps_offer.section_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.section_settings',
    'config_translation.item.overview.ps_offer.section_settings' => 'config_translation.local_tasks:config_translation.item.overview.ps_offer.section_settings',
  ];

  /**
   * Shows Translate on the primary tab row for the active settings section.
   */
  #[Hook('local_tasks_alter')]
  public function localTasksAlter(array &$local_tasks): void {
    foreach (self::TRANSLATION_TASKS as $task_id) {
      if (!isset($local_tasks[$task_id])) {
        continue;
      }
      $local_tasks[$task_id]['base_route'] = 'ps_offer.admin_overview';
      $local_tasks[$task_id]['title'] = new TranslatableMarkup('Translate');
      unset($local_tasks[$task_id]['parent_id']);
    }
  }

  /**
   * Keeps only the Translate tab for the current section.
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    if (!isset($data['tabs'][0]) || !is_array($data['tabs'][0])) {
      return;
    }

    $activeTranslationTask = self::ROUTE_TRANSLATION_TASK[$route_name] ?? NULL;

    foreach (array_keys($data['tabs'][0]) as $task_id) {
      if (!in_array($task_id, self::TRANSLATION_TASKS, TRUE)) {
        continue;
      }
      if ($task_id !== $activeTranslationTask) {
        unset($data['tabs'][0][$task_id]);
        continue;
      }
      if (isset($data['tabs'][0][$task_id]['#link']['title'])) {
        $data['tabs'][0][$task_id]['#link']['title'] = new TranslatableMarkup('Translate');
      }
    }
  }

}
