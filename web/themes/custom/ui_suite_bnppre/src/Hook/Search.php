<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hooks for Search support.
 */
class Search {

  /**
   * Implements hook_form_FORM_ID_alter().
   */
  #[Hook('form_search_block_form_alter')]
  public function alter(array &$form, FormStateInterface $formState, string $form_id): void {
    if (!isset($form['keys'])) {
      return;
    }

    $form['keys']['#input_group_button'] = TRUE;
  }

}
