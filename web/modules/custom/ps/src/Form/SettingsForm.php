<?php

declare(strict_types=1);

namespace Drupal\ps\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for PropertySearch.
 */
final class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps.settings');

    $form['validation'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Validation'),
    ];

    $form['validation']['strictMode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Strict Mode'),
      '#default_value' => $config->get('validation.strictMode') ?? TRUE,
    ];

    $form['validation']['enableLogging'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Logging'),
      '#default_value' => $config->get('validation.enableLogging') ?? TRUE,
    ];

    $form['notifications'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Notifications'),
    ];

    $form['notifications']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Notifications'),
      '#default_value' => $config->get('notifications.enabled') ?? TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps.settings')
      ->set('validation.strictMode', $form_state->getValue('strictMode'))
      ->set('validation.enableLogging', $form_state->getValue('enableLogging'))
      ->set('notifications.enabled', $form_state->getValue('enabled'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
