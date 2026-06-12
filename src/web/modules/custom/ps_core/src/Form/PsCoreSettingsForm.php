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

    $form['urgency_help'] = [
      '#type' => 'details',
      '#title' => $this->t('Site contact — urgency help'),
      '#description' => $this->t('Phone and opening hours shown in offcanvas forms (search alert, contact, etc.).'),
      '#open' => TRUE,
    ];

    $form['urgency_help']['urgency_help_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display urgency help block'),
      '#default_value' => (bool) ($config->get('urgency_help_enabled') ?? TRUE),
    ];

    $form['urgency_help']['urgency_help_lead'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Lead text'),
      '#description' => $this->t('Text before the phone number, e.g. “In a hurry? Call us at”.'),
      '#default_value' => (string) ($config->get('urgency_help_lead') ?? 'In a hurry? Call us at'),
      '#maxlength' => 255,
      '#states' => [
        'visible' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['urgency_help']['urgency_help_phone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone number (display)'),
      '#default_value' => (string) ($config->get('urgency_help_phone') ?? ''),
      '#maxlength' => 64,
      '#states' => [
        'visible' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
        'required' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['urgency_help']['urgency_help_phone_link'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Phone link (tel: URI)'),
      '#description' => $this->t('Optional. Leave empty to derive from the display number (FR numbers starting with 0).'),
      '#default_value' => (string) ($config->get('urgency_help_phone_link') ?? ''),
      '#maxlength' => 64,
      '#states' => [
        'visible' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['urgency_help']['urgency_help_hours'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Opening hours'),
      '#default_value' => (string) ($config->get('urgency_help_hours') ?? ''),
      '#maxlength' => 255,
      '#states' => [
        'visible' => [
          ':input[name="urgency_help_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->configFactory->getEditable('ps_core.settings')
      ->set('cache_default_ttl', (int) $form_state->getValue('cache_default_ttl'))
      ->set('enable_audit_logging', (bool) $form_state->getValue('enable_audit_logging'))
      ->set('audit_retention_days', (int) $form_state->getValue('audit_retention_days'))
      ->set('conflict_window_seconds', (int) $form_state->getValue('conflict_window_seconds'))
      ->set('urgency_help_enabled', (bool) $form_state->getValue('urgency_help_enabled'))
      ->set('urgency_help_lead', trim((string) $form_state->getValue('urgency_help_lead')))
      ->set('urgency_help_phone', trim((string) $form_state->getValue('urgency_help_phone')))
      ->set('urgency_help_phone_link', trim((string) $form_state->getValue('urgency_help_phone_link')))
      ->set('urgency_help_hours', trim((string) $form_state->getValue('urgency_help_hours')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
