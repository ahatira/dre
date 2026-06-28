<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Admin UI tweaks for ps_form configuration forms.
 */
final class PsFormAdminHooks {

  private const TRANSLATION_TASK = 'config_translation.local_tasks:config_translation.item.overview.ps_form.contact_settings';

  /**
   * Maps settings and translate routes to their translation local task.
   *
   * @var array<string, string>
   */
  private const ROUTE_TRANSLATION_TASK = [
    'ps_form.contact_settings' => self::TRANSLATION_TASK,
    'config_translation.item.overview.ps_form.contact_settings' => self::TRANSLATION_TASK,
  ];

  /**
   * Promotes the Translate tab on the contact settings form.
   */
  #[Hook('local_tasks_alter')]
  public function localTasksAlter(array &$local_tasks): void {
    if (!isset($local_tasks[self::TRANSLATION_TASK])) {
      return;
    }

    $local_tasks[self::TRANSLATION_TASK]['base_route'] = 'ps_form.contact_settings';
    $local_tasks[self::TRANSLATION_TASK]['title'] = new TranslatableMarkup('Translate');
    unset($local_tasks[self::TRANSLATION_TASK]['parent_id']);
  }

  /**
   * Renames the Translate tab label on the contact settings form.
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    if (!isset($data['tabs'][0]) || !is_array($data['tabs'][0])) {
      return;
    }

    $activeTranslationTask = self::ROUTE_TRANSLATION_TASK[$route_name] ?? NULL;
    if ($activeTranslationTask === NULL || !isset($data['tabs'][0][$activeTranslationTask])) {
      return;
    }

    if (isset($data['tabs'][0][$activeTranslationTask]['#link']['title'])) {
      $data['tabs'][0][$activeTranslationTask]['#link']['title'] = new TranslatableMarkup('Translate');
    }
  }

}
