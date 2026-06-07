<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Site-wide settings for offer budget and surface display labels.
 */
final class OfferBudgetSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_budget_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_offer.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_offer.settings');

    $form['popover'] = [
      '#type' => 'details',
      '#title' => $this->t('Budget info popover'),
      '#open' => TRUE,
    ];

    $form['popover']['tooltip_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Popover title'),
      '#default_value' => $config->get('tooltip_title') ?? '',
      '#required' => TRUE,
    ];

    $form['popover']['label_ht'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HT definition'),
      '#default_value' => $config->get('label_ht') ?? '',
      '#required' => TRUE,
    ];

    $form['popover']['label_hc'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HC definition'),
      '#default_value' => $config->get('label_hc') ?? '',
      '#required' => TRUE,
    ];

    $form['popover']['label_cc'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CC definition'),
      '#default_value' => $config->get('label_cc') ?? '',
      '#required' => TRUE,
    ];

    $form['popover']['label_hd'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HD definition'),
      '#default_value' => $config->get('label_hd') ?? '',
      '#required' => TRUE,
    ];

    $form['popover']['fees_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fees line prefix'),
      '#description' => $this->t('Prepended to the offer fees field or the default fees text.'),
      '#default_value' => $config->get('fees_prefix') ?? '',
      '#required' => TRUE,
    ];

    $form['popover']['default_fees_rental'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default rental fees text'),
      '#description' => $this->t('Used when the offer fees field is empty (rental).'),
      '#default_value' => $config->get('default_fees_rental') ?? '',
      '#required' => TRUE,
      '#rows' => 2,
    ];

    $form['popover']['default_fees_sale'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default sale fees text'),
      '#description' => $this->t('Used when the offer fees field is empty (sale).'),
      '#default_value' => $config->get('default_fees_sale') ?? '',
      '#required' => TRUE,
      '#rows' => 2,
    ];

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Budget display labels'),
      '#open' => TRUE,
    ];

    $form['display']['on_request'] = [
      '#type' => 'textfield',
      '#title' => $this->t('On request label'),
      '#default_value' => $config->get('on_request') ?? '',
      '#required' => TRUE,
    ];

    $form['display']['label_ttc'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TTC label'),
      '#default_value' => $config->get('label_ttc') ?? '',
      '#required' => TRUE,
    ];

    $form['display']['price_information'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Info icon aria-label'),
      '#default_value' => $config->get('price_information') ?? '',
      '#required' => TRUE,
    ];

    $form['surface'] = [
      '#type' => 'details',
      '#title' => $this->t('Surface display labels'),
      '#open' => TRUE,
    ];

    $form['surface']['surface_divisible_template'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Divisible surface template'),
      '#description' => $this->t('Use @surface as placeholder for the minimum lot (e.g. "Divisible from @surface").'),
      '#default_value' => $config->get('surface_divisible_template') ?? '',
      '#required' => TRUE,
    ];

    $form['surface']['surface_capacity_unit'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Capacity unit label'),
      '#description' => $this->t('Used for coworking offers (e.g. seats, postes).'),
      '#default_value' => $config->get('surface_capacity_unit') ?? '',
      '#required' => TRUE,
    ];

    $form['surface']['surface_kpi_separator'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Surface KPI separator'),
      '#description' => $this->t('Separator between total surface and divisibility suffix.'),
      '#default_value' => $config->get('surface_kpi_separator') ?? '',
      '#required' => TRUE,
      '#size' => 10,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $editable = $this->configFactory->getEditable('ps_offer.settings');
    foreach ([
      'tooltip_title',
      'label_ht',
      'label_hc',
      'label_cc',
      'label_hd',
      'fees_prefix',
      'default_fees_rental',
      'default_fees_sale',
      'on_request',
      'label_ttc',
      'price_information',
      'surface_divisible_template',
      'surface_capacity_unit',
      'surface_kpi_separator',
    ] as $key) {
      $editable->set($key, trim((string) $form_state->getValue($key)));
    }
    $editable->save();

    parent::submitForm($form, $form_state);
  }

}
