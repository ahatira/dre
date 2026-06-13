<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Card Push — search results placement (calculator SDC).
 */
final class PushSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_search_push_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_search.push_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_search.push_settings');

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('Search results placement — renders the <em>search-push-card</em> SDC after a configurable result index.') . '</p>',
      '#weight' => -20,
    ];

    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Card Push on search results'),
      '#description' => $this->t('When enabled, a promotional card is inserted in the search results list (disabled by default).'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['after_result'] = [
      '#type' => 'number',
      '#title' => $this->t('Insert after result number'),
      '#description' => $this->t('The card appears after this result index when total results exceed this value (default: 1).'),
      '#default_value' => $config->get('after_result') ?? 1,
      '#min' => 1,
      '#max' => 100,
      '#required' => TRUE,
    ];

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config->get('title'),
      '#maxlength' => 255,
    ];

    $form['body'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Body text'),
      '#default_value' => $config->get('body'),
      '#rows' => 4,
    ];

    $form['cta_label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button label'),
      '#default_value' => $config->get('cta_label'),
      '#maxlength' => 128,
    ];

    $form['cta_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button URL'),
      '#description' => $this->t('Internal path (e.g. /calculator) or absolute URL. Required when Card Push is enabled.'),
      '#default_value' => $config->get('cta_url'),
      '#maxlength' => 2048,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    if (!(bool) $form_state->getValue('enabled')) {
      return;
    }

    foreach (['title', 'cta_label', 'cta_url'] as $field) {
      if (trim((string) $form_state->getValue($field)) === '') {
        $form_state->setErrorByName($field, $this->t('This field is required when Card Push is enabled for search results.'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_search.push_settings')
      ->set('enabled', (bool) $form_state->getValue('enabled'))
      ->set('after_result', max(1, (int) $form_state->getValue('after_result')))
      ->set('title', trim((string) $form_state->getValue('title')))
      ->set('body', trim((string) $form_state->getValue('body')))
      ->set('cta_label', trim((string) $form_state->getValue('cta_label')))
      ->set('cta_url', trim((string) $form_state->getValue('cta_url')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
