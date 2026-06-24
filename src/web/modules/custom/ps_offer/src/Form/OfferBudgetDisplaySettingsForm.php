<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Budget display labels on offer cards and detail pages.
 */
final class OfferBudgetDisplaySettingsForm extends ConfigFormBase {

  use OfferSettingsFormTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_budget_display_settings_form';
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
      '#markup' => '<p>' . $this->t('Labels used when rendering offer prices on cards, search results and detail headers.') . '</p>',
    ];

    $form['on_request'] = [
      '#type' => 'textfield',
      '#title' => $this->t('On request label'),
      '#default_value' => $config->get('on_request') ?? '',
      '#required' => TRUE,
    ];

    $form['budget_from_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Budget from prefix'),
      '#description' => $this->t('Used in compare views when a minimum budget is shown (e.g. "From").'),
      '#default_value' => $config->get('budget_from_prefix') ?? '',
      '#required' => TRUE,
    ];

    $form['label_ttc'] = [
      '#type' => 'textfield',
      '#title' => $this->t('TTC label'),
      '#default_value' => $config->get('label_ttc') ?? '',
      '#required' => TRUE,
    ];

    $form['price_information'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Info icon aria-label'),
      '#default_value' => $config->get('price_information') ?? '',
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->saveOfferSettingsKeys([
      'on_request',
      'budget_from_prefix',
      'label_ttc',
      'price_information',
    ], $form_state);
    parent::submitForm($form, $form_state);
  }

}
