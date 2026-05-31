<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Form alter hooks for ps_surface module.
 */
final class FormHooks {

  /**
   * Implements hook_form_alter().
   */
  #[Hook('form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state, string $form_id): void {
    // Enhance SurfaceDivision entity forms.
    if (str_starts_with($form_id, 'ps_surface_division_')) {
      $this->enhanceDivisionForm($form, $form_state, $form_id);
    }
  }

  /**
   * Enhances SurfaceDivision forms with better styling and organization.
   */
  private function enhanceDivisionForm(array &$form, FormStateInterface $form_state, string $form_id): void {
    // Add CSS class for form-specific styling.
    $form['#attributes']['class'][] = 'ps-surface-division-form';

    // Attach CSS library.
    $form['#attached']['library'][] = 'ps_surface/surface-widget';

    // Group fields visually with better descriptions.
    if (isset($form['division_reference'])) {
      $form['division_reference']['#weight'] = 10;
      $form['division_reference']['widget'][0]['value']['#attributes']['placeholder'] = 'e.g., LOT-A, RDC, R+1';
    }

    if (isset($form['division_label'])) {
      $form['division_label']['#weight'] = 20;
      $form['division_label']['widget'][0]['value']['#attributes']['placeholder'] = 'e.g., Ground floor - Main office space';
    }

    if (isset($form['surfaces'])) {
      $form['surfaces']['#weight'] = 30;
      $form['surfaces']['#description'] = t('Define the qualified surface values for this division. TOTAL is the total area, DISPO is the available area, and ETREF is the reference surface (ERP regulations).');
    }

    if (isset($form['division_status'])) {
      $form['division_status']['#weight'] = 40;
      $form['division_status']['#description'] = t('Select the operational status of this division based on its availability.');
    }

    if (isset($form['availability_text'])) {
      $form['availability_text']['#weight'] = 50;
      $form['availability_text']['#description'] = t('Raw availability text imported from external systems. This is for reference only.');
    }

    if (isset($form['internal_lock'])) {
      $form['internal_lock']['#weight'] = 70;
      $form['internal_lock']['#description'] = t('⚠️ <strong>Enable this lock to prevent automated imports from overwriting manually curated data.</strong> Use this when you have manually corrected or customized this division and want to protect it from future automatic updates.');
    }

    // Add revision log message if available.
    if (isset($form['revision_log_message'])) {
      $form['revision_log_message']['#weight'] = 80;
    }

    // Group actions at the bottom.
    if (isset($form['actions'])) {
      $form['actions']['#weight'] = 100;
    }
  }

}
