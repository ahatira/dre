<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Admin UI tweaks for ps_form configuration forms.
 */
final class PsFormAdminHooks {

  private const TRANSLATION_TASK_URGENCY = 'config_translation.local_tasks:config_translation.item.overview.ps_form.settings_urgency_contact';

  /**
   * Maps settings and translate routes to their translation local task.
   *
   * @var array<string, string>
   */
  private const ROUTE_TRANSLATION_TASK = [
    'ps_form.settings' => self::TRANSLATION_TASK_URGENCY,
    'config_translation.item.overview.ps_form.settings_urgency_contact' => self::TRANSLATION_TASK_URGENCY,
  ];

  /**
   * Promotes Translate tabs on contact settings forms.
   */
  #[Hook('local_tasks_alter')]
  public function localTasksAlter(array &$local_tasks): void {
    if (isset($local_tasks[self::TRANSLATION_TASK_URGENCY])) {
      $local_tasks[self::TRANSLATION_TASK_URGENCY]['base_route'] = 'ps_form.admin_overview';
      $local_tasks[self::TRANSLATION_TASK_URGENCY]['title'] = new TranslatableMarkup('Translate');
      unset($local_tasks[self::TRANSLATION_TASK_URGENCY]['parent_id']);
    }
  }

  /**
   * Renames Translate tabs and keeps only the relevant one visible.
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    if (!isset($data['tabs'][0]) || !is_array($data['tabs'][0])) {
      return;
    }

    $activeTranslationTask = self::ROUTE_TRANSLATION_TASK[$route_name] ?? NULL;
    if ($activeTranslationTask !== NULL && isset($data['tabs'][0][$activeTranslationTask]['#link']['title'])) {
      $data['tabs'][0][$activeTranslationTask]['#link']['title'] = new TranslatableMarkup('Translate');
    }

    if ($activeTranslationTask !== self::TRANSLATION_TASK_URGENCY) {
      unset($data['tabs'][0][self::TRANSLATION_TASK_URGENCY]);
    }
  }

}
