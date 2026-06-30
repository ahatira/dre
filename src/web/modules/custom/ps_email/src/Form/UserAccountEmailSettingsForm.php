<?php

declare(strict_types=1);

namespace Drupal\ps_email\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Core user account email wording (password reset, status notifications).
 */
final class UserAccountEmailSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_email_user_account_email_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['user.mail'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('user.mail');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t(
        'Subject and body templates for Drupal core user account emails. Tokens such as [user:name] and [user:one-time-login-url] are supported.',
      ) . '</p>',
    ];

    $fields = [
      'password_reset' => $this->t('Password reset'),
      'register_admin_created' => $this->t('Account created by administrator'),
      'register_no_approval_required' => $this->t('Registration (no approval required)'),
      'register_pending_approval' => $this->t('Registration (pending approval)'),
      'status_activated' => $this->t('Account activated'),
      'status_blocked' => $this->t('Account blocked'),
      'cancel_confirm' => $this->t('Account cancellation confirm'),
    ];

    foreach ($fields as $key => $label) {
      $form[$key] = [
        '#type' => 'details',
        '#title' => $label,
        '#open' => $key === 'password_reset',
        'subject' => [
          '#type' => 'textfield',
          '#title' => $this->t('Subject'),
          '#default_value' => (string) $config->get($key . '_subject'),
          '#maxlength' => 180,
        ],
        'body' => [
          '#type' => 'textarea',
          '#title' => $this->t('Body'),
          '#default_value' => (string) $config->get($key . '_body'),
          '#rows' => 6,
        ],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $editable = $this->config('user.mail');
    foreach (array_keys($form) as $key) {
      if (!is_array($form[$key]) || !isset($form[$key]['#type']) || $form[$key]['#type'] !== 'details') {
        continue;
      }
      $editable
        ->set($key . '_subject', trim((string) $form_state->getValue([$key, 'subject'])))
        ->set($key . '_body', trim((string) $form_state->getValue([$key, 'body'])));
    }
    $editable->save();

    parent::submitForm($form, $form_state);
    $form_state->setRedirect('ps_email.user_account');
  }

}
