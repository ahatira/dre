<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\ps_search\Entity\SearchAlertInterface;

/**
 * Admin settings for search alert notifications.
 */
final class AlertSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_search_alert_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_search.alert_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_search.alert_settings');

    $alertsLink = Link::createFromRoute($this->t('Content → Search alerts'), 'entity.search_alert.collection')->toString();
    $globalLink = Link::createFromRoute($this->t('Contact forms'), 'ps_form.contact_settings')->toString();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Cron digest emails for property search alert subscriptions. Browse stored subscriptions under @alerts. The urgency contact block in the offcanvas form is configured under @contact.', [
        '@alerts' => $alertsLink,
        '@contact' => $globalLink,
      ]) . '</p>',
    ];

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable search alert cron notifications'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['default_frequence'] = [
      '#type' => 'select',
      '#title' => $this->t('Default frequency for new alerts'),
      '#options' => [
        SearchAlertInterface::FREQUENCE_DAILY => $this->t('Daily'),
        SearchAlertInterface::FREQUENCE_WEEKLY => $this->t('Weekly'),
      ],
      '#default_value' => $config->get('default_frequence') ?? SearchAlertInterface::FREQUENCE_WEEKLY,
    ];

    $form['batch_size'] = [
      '#type' => 'number',
      '#title' => $this->t('Alerts processed per cron run'),
      '#default_value' => $config->get('batch_size') ?? 50,
      '#min' => 1,
      '#max' => 500,
    ];

    $form['from_mail'] = [
      '#type' => 'email',
      '#title' => $this->t('From email'),
      '#default_value' => $config->get('from_mail'),
    ];

    $form['from_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From name'),
      '#default_value' => $config->get('from_name'),
      '#maxlength' => 255,
    ];

    $form['bcc_mail'] = [
      '#type' => 'email',
      '#title' => $this->t('BCC email'),
      '#default_value' => $config->get('bcc_mail'),
    ];

    $form['retention_days'] = [
      '#type' => 'number',
      '#title' => $this->t('Retention for anonymous alerts (days)'),
      '#default_value' => $config->get('retention_days') ?? 365,
      '#min' => 30,
      '#max' => 3650,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_search.alert_settings')
      ->set('enabled', (bool) $form_state->getValue('enabled'))
      ->set('default_frequence', (string) $form_state->getValue('default_frequence'))
      ->set('batch_size', max(1, (int) $form_state->getValue('batch_size')))
      ->set('from_mail', trim((string) $form_state->getValue('from_mail')))
      ->set('from_name', trim((string) $form_state->getValue('from_name')))
      ->set('bcc_mail', trim((string) $form_state->getValue('bcc_mail')))
      ->set('retention_days', max(30, (int) $form_state->getValue('retention_days')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
