<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for PS Compare module.
 */
final class CompareSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_compare_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_compare.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_compare.settings');

    $form['limits'] = [
      '#type' => 'details',
      '#title' => $this->t('Comparison limits'),
      '#open' => TRUE,
    ];

    $form['limits']['max_items'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum compared items'),
      '#default_value' => $config->get('max_items') ?? 4,
      '#min' => 1,
      '#max' => 10,
      '#required' => TRUE,
    ];

    $form['limits']['min_items'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum items to open comparison page'),
      '#default_value' => $config->get('min_items') ?? 2,
      '#min' => 1,
      '#max' => 10,
      '#required' => TRUE,
    ];

    $targets = $config->get('enabled_targets') ?? ['node.offer'];
    $form['limits']['enabled_targets'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Enabled targets'),
      '#description' => $this->t('One target per line using entity_type.bundle format (e.g. node.offer).'),
      '#default_value' => implode("\n", $targets),
      '#required' => TRUE,
    ];

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Compare page display'),
      '#description' => $this->t('Configure UX options for the comparison table and modal.'),
      '#open' => TRUE,
    ];

    $displayFields = [
      'display_show_summary' => $this->t('Show summary banner'),
      'display_section_nav' => $this->t('Show section navigation'),
      'display_collapsible_sections' => $this->t('Enable collapsible sections'),
      'display_collapsible_feature_only' => $this->t('Collapsible feature groups only'),
      'display_mobile_cards' => $this->t('Mobile card layout'),
      'display_sticky_cta' => $this->t('Sticky view-property bar'),
      'display_price_info' => $this->t('Show price info popover'),
      'display_merge_energy' => $this->t('Merge DPE and GES on one row'),
      'display_share_button' => $this->t('Show share comparison button'),
    ];

    foreach ($displayFields as $key => $label) {
      $form['display'][$key] = [
        '#type' => 'checkbox',
        '#title' => $label,
        '#default_value' => (bool) ($config->get($key) ?? TRUE),
      ];
    }

    $form['display']['display_collapsible_feature_only']['#states'] = [
      'visible' => [
        ':input[name="display_collapsible_sections"]' => ['checked' => TRUE],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $targets = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $form_state->getValue('enabled_targets')) ?: [])));

    $this->config('ps_compare.settings')
      ->set('max_items', (int) $form_state->getValue('max_items'))
      ->set('min_items', (int) $form_state->getValue('min_items'))
      ->set('enabled_targets', $targets)
      ->set('display_show_summary', (bool) $form_state->getValue('display_show_summary'))
      ->set('display_section_nav', (bool) $form_state->getValue('display_section_nav'))
      ->set('display_collapsible_sections', (bool) $form_state->getValue('display_collapsible_sections'))
      ->set('display_collapsible_feature_only', (bool) $form_state->getValue('display_collapsible_feature_only'))
      ->set('display_mobile_cards', (bool) $form_state->getValue('display_mobile_cards'))
      ->set('display_sticky_cta', (bool) $form_state->getValue('display_sticky_cta'))
      ->set('display_price_info', (bool) $form_state->getValue('display_price_info'))
      ->set('display_merge_energy', (bool) $form_state->getValue('display_merge_energy'))
      ->set('display_share_button', (bool) $form_state->getValue('display_share_button'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
