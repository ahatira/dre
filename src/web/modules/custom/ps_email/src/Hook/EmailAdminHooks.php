<?php

declare(strict_types=1);

namespace Drupal\ps_email\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Admin UI tweaks for ps_email configuration forms.
 */
final class EmailAdminHooks {

  private const TRANSLATION_TASK_SHELL = 'config_translation.local_tasks:config_translation.item.overview.ps_email.shell_footer';

  /**
   * Promotes Translate tabs on email settings forms.
   */
  #[Hook('local_tasks_alter')]
  public function localTasksAlter(array &$local_tasks): void {
    if (!isset($local_tasks[self::TRANSLATION_TASK_SHELL])) {
      return;
    }
    $local_tasks[self::TRANSLATION_TASK_SHELL]['base_route'] = 'ps_email.admin';
    $local_tasks[self::TRANSLATION_TASK_SHELL]['title'] = new TranslatableMarkup('Translate');
    unset($local_tasks[self::TRANSLATION_TASK_SHELL]['parent_id']);
  }

  /**
   * Renames Translate tabs and keeps only the relevant one visible.
   */
  #[Hook('menu_local_tasks_alter')]
  public function menuLocalTasksAlter(array &$data, string $route_name): void {
    if (!isset($data['tabs'][0]) || !is_array($data['tabs'][0])) {
      return;
    }

    $activeTranslationTask = $route_name === 'ps_email.shell_footer'
      || $route_name === 'config_translation.item.overview.ps_email.shell_footer'
      ? self::TRANSLATION_TASK_SHELL
      : NULL;

    if ($activeTranslationTask !== NULL && isset($data['tabs'][0][$activeTranslationTask]['#link']['title'])) {
      $data['tabs'][0][$activeTranslationTask]['#link']['title'] = new TranslatableMarkup('Translate');
    }

    if ($activeTranslationTask !== self::TRANSLATION_TASK_SHELL) {
      unset($data['tabs'][0][self::TRANSLATION_TASK_SHELL]);
    }
  }

}
