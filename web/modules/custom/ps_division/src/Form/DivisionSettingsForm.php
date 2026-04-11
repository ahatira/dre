<?php

declare(strict_types=1);

namespace Drupal\ps_division\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Division module settings.
 */
final class DivisionSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_division.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_division_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_division.settings');

    $form['import_settings'] = [
      '#type' => 'details',
      '#title' => $this->t('Import Settings'),
      '#open' => TRUE,
    ];

    $form['import_settings']['default_type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default Division Type'),
      '#default_value' => $config->get('import_settings.default_type') ?? 'division',
      '#description' => $this->t('Default division type machine name for imports.'),
      '#required' => TRUE,
    ];

    $form['import_settings']['auto_aggregate'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto-aggregate surfaces on save'),
      '#default_value' => $config->get('import_settings.auto_aggregate') ?? TRUE,
      '#description' => $this->t('Automatically calculate aggregate surfaces when division is saved.'),
    ];

    $form['import_settings']['cache_aggregates'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Cache aggregate calculations'),
      '#default_value' => $config->get('import_settings.cache_aggregates') ?? TRUE,
      '#description' => $this->t('Enable caching for expensive aggregate surface calculations.'),
    ];

    $form['business_rules'] = [
      '#type' => 'details',
      '#title' => $this->t('Business Rules'),
      '#open' => TRUE,
    ];

    $form['business_rules']['require_parent'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Require parent entity ID'),
      '#default_value' => $config->get('business_rules.require_parent') ?? FALSE,
      '#description' => $this->t('Make parent entity ID mandatory for all divisions.'),
    ];

    $form['business_rules']['validate_codes'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Validate dictionary codes'),
      '#default_value' => $config->get('business_rules.validate_codes') ?? TRUE,
      '#description' => $this->t('Validate all type/nature/unit codes against ps_dictionary.'),
    ];

    $form['business_rules']['aggregate_on_save'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Aggregate surfaces on entity save'),
      '#default_value' => $config->get('business_rules.aggregate_on_save') ?? TRUE,
      '#description' => $this->t('Trigger surface aggregation when division entity is saved.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_division.settings')
      ->set('import_settings.default_type', $form_state->getValue('default_type'))
      ->set('import_settings.auto_aggregate', (bool) $form_state->getValue('auto_aggregate'))
      ->set('import_settings.cache_aggregates', (bool) $form_state->getValue('cache_aggregates'))
      ->set('business_rules.require_parent', (bool) $form_state->getValue('require_parent'))
      ->set('business_rules.validate_codes', (bool) $form_state->getValue('validate_codes'))
      ->set('business_rules.aggregate_on_save', (bool) $form_state->getValue('aggregate_on_save'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
