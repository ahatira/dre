<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Budget info popover content on offer detail pages.
 */
final class OfferBudgetPopoverSettingsForm extends ConfigFormBase {

  use OfferSettingsFormTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_budget_popover_settings_form';
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
    $config = $this->offerSettingsConfig();

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Content shown in the budget information popover on offer detail pages.') . '</p>',
    ];

    $form['tooltip_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Popover title'),
      '#default_value' => $config->get('tooltip_title') ?? '',
      '#required' => TRUE,
    ];

    $form['label_ht'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HT definition'),
      '#default_value' => $config->get('label_ht') ?? '',
      '#required' => TRUE,
    ];

    $form['label_hc'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HC definition'),
      '#default_value' => $config->get('label_hc') ?? '',
      '#required' => TRUE,
    ];

    $form['label_cc'] = [
      '#type' => 'textfield',
      '#title' => $this->t('CC definition'),
      '#default_value' => $config->get('label_cc') ?? '',
      '#required' => TRUE,
    ];

    $form['label_hd'] = [
      '#type' => 'textfield',
      '#title' => $this->t('HD definition'),
      '#default_value' => $config->get('label_hd') ?? '',
      '#required' => TRUE,
    ];

    $form['fees_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fees line prefix'),
      '#description' => $this->t('Prepended to the offer fees field or the default fees text.'),
      '#default_value' => $config->get('fees_prefix') ?? '',
      '#required' => TRUE,
    ];

    $form['default_fees_rental'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default rental fees text'),
      '#description' => $this->t('Used when the offer fees field is empty (rental).'),
      '#default_value' => $config->get('default_fees_rental') ?? '',
      '#required' => TRUE,
      '#rows' => 2,
    ];

    $form['default_fees_sale'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Default sale fees text'),
      '#description' => $this->t('Used when the offer fees field is empty (sale).'),
      '#default_value' => $config->get('default_fees_sale') ?? '',
      '#required' => TRUE,
      '#rows' => 2,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->saveOfferSettingsKeys([
      'tooltip_title',
      'label_ht',
      'label_hc',
      'label_cc',
      'label_hd',
      'fees_prefix',
      'default_fees_rental',
      'default_fees_sale',
    ], $form_state);
    parent::submitForm($form, $form_state);
  }

}
