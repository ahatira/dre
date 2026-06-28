<?php

declare(strict_types=1);

namespace Drupal\ps_theme\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\webform\WebformSubmissionForm;

/**
 * Webform presentation hooks — wizard action bar styling.
 */
final class WebformFormHooks {

  /**
   * Adds wizard action bar classes and attaches the form-wizard-actions SDC.
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    if (!str_starts_with($form_id, 'webform_submission_') || !str_ends_with($form_id, '_form')) {
      return;
    }

    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $form['#attached']['library'][] = 'core/components.ps_theme--form-wizard-actions';

    if (isset($form['actions'])) {
      $this->applyWizardActionsPresentation($form['actions']);
    }

    if (isset($form['ps_webform_sticky_footer']['actions'])) {
      $this->applyWizardActionsPresentation($form['ps_webform_sticky_footer']['actions']);
    }
  }

  /**
   * Adds the shared wizard actions wrapper class to a form actions element.
   *
   * @param array<string, mixed> $actions
   *   The form actions render array.
   */
  private function applyWizardActionsPresentation(array &$actions): void {
    $actions['#attributes']['class'] ??= [];
    if (!in_array('ps-form-wizard-actions', $actions['#attributes']['class'], TRUE)) {
      $actions['#attributes']['class'][] = 'ps-form-wizard-actions';
    }
  }

}
