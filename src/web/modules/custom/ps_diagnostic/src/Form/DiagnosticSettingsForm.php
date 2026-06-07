<?php

declare(strict_types=1);

namespace Drupal\ps_diagnostic\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

final class DiagnosticSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames(): array {
    return ['ps_diagnostic.settings'];
  }

  public function getFormId(): string {
    return 'ps_diagnostic_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_diagnostic.settings');

    $form['navigation'] = [
      '#type' => 'container',
      'title' => ['#markup' => '<h2>' . $this->t('Navigation') . '</h2>'],
      'links' => [
        '#theme' => 'item_list',
        '#items' => [
          Link::createFromRoute($this->t('Diagnostics overview'), 'ps_diagnostic.admin_structure')->toRenderable(),
          Link::createFromRoute($this->t('Diagnostic types'), 'entity.ps_diagnostic_type.collection')->toRenderable(),
        ],
      ],
    ];

    $form['default_validity_months'] = [
      '#type' => 'number',
      '#title' => $this->t('Default validity duration (months)'),
      '#default_value' => (int) ($config->get('default_validity_months') ?? 120),
      '#min' => 1,
      '#max' => 600,
      '#required' => TRUE,
    ];

    $form['allow_manual_class'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow manual class entry'),
      '#default_value' => (bool) $config->get('allow_manual_class'),
    ];

    $form['allow_empty_value'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow empty diagnostic value'),
      '#default_value' => (bool) $config->get('allow_empty_value'),
    ];

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Offer detail display'),
      '#open' => TRUE,
    ];

    $form['display']['section_settings_link'] = [
      '#type' => 'item',
      '#title' => $this->t('Section title and icon'),
      '#markup' => Link::createFromRoute($this->t('Configure offer section headings'), 'ps_offer.section_settings')->toString(),
    ];

    $fallback_mode = (string) ($config->get('fallback_message_mode') ?? 'single');
    $form['display']['fallback_message_mode'] = [
      '#type' => 'radios',
      '#title' => $this->t('Disabled diagnostic message mode'),
      '#options' => [
        'single' => $this->t('Single message (BNPPRE style)'),
        'detailed' => $this->t('Detailed message per reason'),
      ],
      '#default_value' => in_array($fallback_mode, ['single', 'detailed'], TRUE) ? $fallback_mode : 'single',
      '#description' => $this->t('When a diagnostic cannot be displayed, choose one shared message or a specific message per status.'),
    ];

    $form['display']['fallback_message_single'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Single fallback message'),
      '#default_value' => (string) ($config->get('fallback_message_single') ?? 'Energy label not provided by the owner.'),
      '#description' => $this->t('Shown for all disabled diagnostics when single message mode is selected.'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#states' => [
        'visible' => [
          ':input[name="fallback_message_mode"]' => ['value' => 'single'],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $display = (array) $form_state->getValue('display');

    $fallback_mode = (string) ($display['fallback_message_mode'] ?? 'single');
    if (!in_array($fallback_mode, ['single', 'detailed'], TRUE)) {
      $fallback_mode = 'single';
    }

    $this->configFactory->getEditable('ps_diagnostic.settings')
      ->set('default_validity_months', (int) $form_state->getValue('default_validity_months'))
      ->set('allow_manual_class', (bool) $form_state->getValue('allow_manual_class'))
      ->set('allow_empty_value', (bool) $form_state->getValue('allow_empty_value'))
      ->set('fallback_message_mode', $fallback_mode)
      ->set('fallback_message_single', trim((string) ($display['fallback_message_single'] ?? 'Energy label not provided by the owner.')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  public function actions(array $form, FormStateInterface $form_state): array {
    $actions = parent::actions($form, $form_state);
    $actions['back_to_structure'] = [
      '#type' => 'link',
      '#title' => $this->t('Back to diagnostics structure'),
      '#url' => Url::fromRoute('ps_diagnostic.admin_structure'),
      '#attributes' => ['class' => ['button', 'button--secondary']],
    ];
    return $actions;
  }

}
