<?php

declare(strict_types=1);

namespace Drupal\bnp_editor\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for BNP Editor settings.
 */
final class BnpEditorSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'bnp_editor_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['bnp_editor.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('bnp_editor.settings');

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General Settings'),
      '#open' => TRUE,
    ];

    $form['general']['enable_custom_plugins'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable custom CKEditor plugins'),
      '#description' => $this->t('Enable custom plugins developed for BNP platform.'),
      '#default_value' => $config->get('enable_custom_plugins') ?? TRUE,
    ];

    $form['general']['enable_media_embed'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable media embed'),
      '#description' => $this->t('Allow embedding media items in rich text content.'),
      '#default_value' => $config->get('enable_media_embed') ?? TRUE,
    ];

    $form['plugins'] = [
      '#type' => 'details',
      '#title' => $this->t('Plugin Configuration'),
      '#open' => FALSE,
    ];

    $form['plugins']['allowed_protocols'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Allowed link protocols'),
      '#description' => $this->t('Enter one protocol per line (e.g., http, https, mailto, tel).'),
      '#default_value' => $config->get('allowed_protocols') ?? "http\nhttps\nmailto\ntel",
      '#rows' => 5,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('bnp_editor.settings')
      ->set('enable_custom_plugins', $form_state->getValue('enable_custom_plugins'))
      ->set('enable_media_embed', $form_state->getValue('enable_media_embed'))
      ->set('allowed_protocols', $form_state->getValue('allowed_protocols'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
