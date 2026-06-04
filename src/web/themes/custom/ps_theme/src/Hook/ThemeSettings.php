<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Add theme settings.
 */
class ThemeSettings {

  use StringTranslationTrait;

  /**
   * Implements hook_form_system_theme_settings_alter().
   */
  #[Hook('form_system_theme_settings_alter')]
  public function alter(array &$form, FormStateInterface $formState): void {
    if (isset($form['ui_suite_bnp']['library']['css_loading']['#options'])) {
      $form['ui_suite_bnp']['library']['css_loading']['#options']['ps_theme/framework'] = $this->t('PS overrides (legacy split build)');
    }
  }

}
