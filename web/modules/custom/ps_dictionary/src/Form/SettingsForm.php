<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for ps_dictionary module.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_dictionary.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_dictionary_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_dictionary.settings');

    $form['import_behavior'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Import Behavior'),
      '#description' => $this->t('Configure how unknown codes are handled during imports.'),
    ];

    $form['import_behavior']['allow_unknown_codes'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow unknown codes'),
      '#default_value' => $config->get('allow_unknown_codes') ?? FALSE,
      '#description' => $this->t('Log warning if unknown codes encountered (does not create entries).'),
    ];

    $form['import_behavior']['auto_create_on_unknown'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto-create on unknown'),
      '#default_value' => $config->get('auto_create_on_unknown') ?? FALSE,
      '#description' => $this->t('Automatically create entries for unknown codes.'),
    ];

    $form['import_behavior']['default_status_new_items'] = [
      '#type' => 'radios',
      '#title' => $this->t('Default status for auto-created entries'),
      '#options' => [
        'active' => $this->t('Active'),
        'inactive' => $this->t('Inactive'),
      ],
      '#default_value' => $config->get('default_status_new_items') ?? 'inactive',
      '#states' => [
        'visible' => [
          ':input[name="auto_create_on_unknown"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['deprecation'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Deprecation Policy'),
      '#description' => $this->t('How to handle deprecated entries.'),
    ];

    $form['deprecation']['deprecated_policy'] = [
      '#type' => 'radios',
      '#title' => $this->t('Policy'),
      '#options' => [
        'soft' => $this->t('Soft - Deprecated entries remain visible but marked'),
        'hard' => $this->t('Hard - Deprecated entries excluded from searches and forms'),
      ],
      '#default_value' => $config->get('deprecated_policy') ?? 'soft',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_dictionary.settings')
      ->set('allow_unknown_codes', $form_state->getValue('allow_unknown_codes'))
      ->set('auto_create_on_unknown', $form_state->getValue('auto_create_on_unknown'))
      ->set('default_status_new_items', $form_state->getValue('default_status_new_items'))
      ->set('deprecated_policy', $form_state->getValue('deprecated_policy'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
