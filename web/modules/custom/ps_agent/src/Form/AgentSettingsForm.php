<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Agent settings form.
 *
 * Configuration form for agent module settings including field protection
 * rules for CRM synchronization.
 *
 * @ingroup ps_agent
 */
final class AgentSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_agent.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_agent_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_agent.settings');

    $form['fieldset_protection'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('BO-Protected Fields'),
      '#description' => $this->t('These fields will be preserved during CRM imports.'),
    ];

    $form['fieldset_protection']['bo_protected_fields'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Select fields to protect'),
      '#options' => [
        'email' => $this->t('Email'),
        'phone' => $this->t('Phone'),
        'mobile' => $this->t('Mobile'),
        'internal_notes' => $this->t('Internal Notes'),
      ],
      '#default_value' => $config->get('bo_protected_fields') ?? [
        'email' => 'email',
        'phone' => 'phone',
        'mobile' => 'mobile',
        'internal_notes' => 'internal_notes',
      ],
    ];

    $form['fieldset_crm'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('CRM Import Settings'),
    ];

    $form['fieldset_crm']['crm_import_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable CRM Import'),
      '#default_value' => $config->get('crm_import_enabled') ?? TRUE,
    ];

    $form['fieldset_crm']['crm_update_on_external_id'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update agents by external ID'),
      '#description' => $this->t('If enabled, agents with matching external IDs will be updated instead of creating new ones.'),
      '#default_value' => $config->get('crm_update_on_external_id') ?? TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_agent.settings')
      ->set('bo_protected_fields', array_filter($form_state->getValue('bo_protected_fields')))
      ->set('crm_import_enabled', $form_state->getValue('crm_import_enabled'))
      ->set('crm_update_on_external_id', $form_state->getValue('crm_update_on_external_id'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
