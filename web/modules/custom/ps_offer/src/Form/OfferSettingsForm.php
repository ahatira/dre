<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Offer module settings form.
 */
class OfferSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'ps_offer.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_offer.settings');

    $form['offer_settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Offer Settings'),
      '#description' => $this->t('Configure property offer behavior and display.'),
    ];

    $form['offer_settings']['divisible_default'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Offers are divisible by default'),
      '#description' => $this->t('When checked, new offers will be marked as divisible by default.'),
      '#default_value' => $config->get('divisible_default') ?? FALSE,
    ];

    $form['offer_settings']['auto_publish'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Auto-publish offers on import'),
      '#description' => $this->t('Automatically publish offers when imported from CRM.'),
      '#default_value' => $config->get('auto_publish') ?? FALSE,
    ];

    $form['offer_settings']['results_per_page'] = [
      '#type' => 'number',
      '#title' => $this->t('Default results per page'),
      '#description' => $this->t('Number of offers to display per page in collections.'),
      '#default_value' => $config->get('results_per_page') ?? 20,
      '#min' => 1,
      '#max' => 100,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_offer.settings')
      ->set('divisible_default', $form_state->getValue('divisible_default'))
      ->set('auto_publish', $form_state->getValue('auto_publish'))
      ->set('results_per_page', $form_state->getValue('results_per_page'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
