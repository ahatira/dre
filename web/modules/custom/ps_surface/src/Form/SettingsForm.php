<?php

declare(strict_types=1);

namespace Drupal\ps_surface\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for Surface field type and validation settings.
 *
 * Manages validation rules (negative values, required fields, etc.).
 *
 * Note: Dictionary type mappings are hardcoded (surface_unit, surface_type,
 * surface_nature, surface_qualification) since they are locked in ps_dictionary.
 * Formatter display options (unit/qualification labels, decimals) are configured
 * per field instance via the formatter plugin settings form.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_surface.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_surface_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_surface.settings');

    // Validation section.
    $form['validation'] = [
      '#type' => 'details',
      '#title' => $this->t('Validation Rules'),
      '#description' => $this->t('Configure validation constraints for surface data.'),
      '#open' => TRUE,
    ];

    $form['validation']['allow_negative'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow negative values'),
      '#description' => $this->t('If unchecked, negative surface values will be rejected during validation.'),
      '#default_value' => $config->get('validation.allow_negative') ?? FALSE,
    ];

    $form['validation']['require_unit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Require unit'),
      '#description' => $this->t('Unit field must be filled. Recommended: enabled.'),
      '#default_value' => $config->get('validation.require_unit') ?? TRUE,
      '#disabled' => TRUE,
    ];

    $form['validation']['require_type'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Require type'),
      '#description' => $this->t('Type field must be filled (optional).'),
      '#default_value' => $config->get('validation.require_type') ?? FALSE,
    ];

    $form['validation']['require_nature'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Require nature'),
      '#description' => $this->t('Nature field must be filled (optional).'),
      '#default_value' => $config->get('validation.require_nature') ?? FALSE,
    ];

    $form['validation']['require_qualification'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Require qualification'),
      '#description' => $this->t('Qualification field must be filled (optional).'),
      '#default_value' => $config->get('validation.require_qualification') ?? FALSE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $this->config('ps_surface.settings')
      ->set('validation.allow_negative', $form_state->getValue('allow_negative'))
      ->set('validation.require_type', $form_state->getValue('require_type'))
      ->set('validation.require_nature', $form_state->getValue('require_nature'))
      ->set('validation.require_qualification', $form_state->getValue('require_qualification'))
      ->save();

    $this->messenger()->addStatus($this->t('Surface settings have been saved.'));
  }

}
