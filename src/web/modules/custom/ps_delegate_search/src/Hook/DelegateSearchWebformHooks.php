<?php

declare(strict_types=1);

namespace Drupal\ps_delegate_search\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\webform\WebformSubmissionForm;

/**
 * Webform hooks for delegate search.
 */
final class DelegateSearchWebformHooks {

  /**
   * Preprocesses the delegate search webform.
   */
  #[Hook('form_webform_submission_delegate_search_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    // Add custom classes to the form.
    $form['#attributes']['class'][] = 'ps-delegate-search-form';

    // Add footer with contact info.
    $form['#prefix'] = '<div class="ps-delegate-search-modal__content">';
    $form['#suffix'] = '</div>';
  }

}
