<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_starterkit_split\Hook;

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
      $form['ui_suite_bnp']['library']['css_loading']['#options']['ui_suite_bnp_starterkit_split/framework'] = $this->t('Starterkit');
    }
  }

}
