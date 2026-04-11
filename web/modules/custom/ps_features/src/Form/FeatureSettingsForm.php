<?php

declare(strict_types=1);

namespace Drupal\ps_features\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Feature settings.
 */
class FeatureSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_features.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_features_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_features.settings');

    $form['allow_unknown_features'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Allow unknown features'),
      '#description' => $this->t('If enabled, features not defined in configuration will be silently ignored rather than causing errors.'),
      '#default_value' => $config->get('allow_unknown_features') ?? FALSE,
    ];

    $form['default_required'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Make features required by default'),
      '#description' => $this->t('If enabled, newly created features will be marked as required by default.'),
      '#default_value' => $config->get('default_required') ?? FALSE,
    ];

    $form['packs_fallback_strategy'] = [
      '#type' => 'select',
      '#title' => $this->t('Packs fallback strategy'),
      '#description' => $this->t('Applied if no feature pack matches.'),
      '#options' => [
        'none' => $this->t('None (no additional features)'),
        'default_pack' => $this->t('Use default pack'),
      ],
      '#default_value' => $config->get('packs_fallback_strategy') ?? 'none',
    ];

    $form['compare_sections'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Comparison sections'),
      '#description' => $this->t('One machine name per line for ordering groups.'),
      '#default_value' => implode("\n", (array) $config->get('compare_sections') ?: []),
      '#rows' => 5,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_features.settings')
      ->set('allow_unknown_features', (bool) $form_state->getValue('allow_unknown_features'))
      ->set('default_required', (bool) $form_state->getValue('default_required'))
      ->set('packs_fallback_strategy', (string) $form_state->getValue('packs_fallback_strategy'))
      ->set('compare_sections', $this->sanitizeSections((string) $form_state->getValue('compare_sections')))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Sanitizes comparison sections textarea input into an array of machine names.
   *
   * @param string $raw
   *   Raw textarea value.
   *
   * @return array<int, string>
   *   Cleaned section identifiers.
   */
  private function sanitizeSections(string $raw): array {
    $lines = preg_split('/\r?\n/', $raw) ?: [];
    $clean = [];
    foreach ($lines as $line) {
      $id = strtolower(trim($line));
      if ($id === '') {
        continue;
      }
      $id = preg_replace('/[^a-z0-9_]/', '_', $id) ?? $id;
      $clean[] = $id;
    }
    return array_values(array_unique($clean));
  }

}
