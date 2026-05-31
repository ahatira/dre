<?php

declare(strict_types=1);

namespace Drupal\ps_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

final class PsCoreSettingsForm extends ConfigFormBase {

  public function getFormId(): string {
    return 'ps_core_settings_form';
  }

  protected function getEditableConfigNames(): array {
    return ['ps_core.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_core.settings');

    $form['cache'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Cache settings'),
    ];

    $form['cache']['cache_default_ttl'] = [
      '#type' => 'number',
      '#title' => $this->t('Default cache TTL (seconds)'),
      '#default_value' => (int) ($config->get('cache_default_ttl') ?? 3600),
      '#min' => 60,
      '#max' => 86400,
      '#step' => 1,
      '#required' => TRUE,
    ];

    $form['audit'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Audit settings'),
    ];

    $form['audit']['enable_audit_logging'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable audit logging'),
      '#default_value' => (bool) ($config->get('enable_audit_logging') ?? TRUE),
    ];

    $form['audit']['audit_retention_days'] = [
      '#type' => 'number',
      '#title' => $this->t('Audit retention (days)'),
      '#default_value' => (int) ($config->get('audit_retention_days') ?? 365),
      '#min' => 1,
      '#max' => 3650,
      '#step' => 1,
      '#required' => TRUE,
    ];

    $form['conflict'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Conflict detection settings'),
    ];

    $form['conflict']['conflict_window_seconds'] = [
      '#type' => 'number',
      '#title' => $this->t('Conflict detection window (seconds)'),
      '#default_value' => (int) ($config->get('conflict_window_seconds') ?? 300),
      '#min' => 0,
      '#step' => 1,
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->configFactory->getEditable('ps_core.settings')
      ->set('cache_default_ttl', (int) $form_state->getValue('cache_default_ttl'))
      ->set('enable_audit_logging', (bool) $form_state->getValue('enable_audit_logging'))
      ->set('audit_retention_days', (int) $form_state->getValue('audit_retention_days'))
      ->set('conflict_window_seconds', (int) $form_state->getValue('conflict_window_seconds'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
