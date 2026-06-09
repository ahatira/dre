<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for map zone defaults, pager threshold and markers cap.
 */
final class MapZoneSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_search.map_zone_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_search_map_zone_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_search.map_zone_settings');

    $form['default_center_lat'] = [
      '#type' => 'number',
      '#title' => $this->t('Default map center latitude'),
      '#default_value' => $config->get('default_center_lat') ?? 46.603354,
      '#required' => TRUE,
      '#step' => 'any',
      '#min' => -90,
      '#max' => 90,
      '#description' => $this->t('Used when no locality is selected and no explicit map bounds are set.'),
    ];

    $form['default_center_lng'] = [
      '#type' => 'number',
      '#title' => $this->t('Default map center longitude'),
      '#default_value' => $config->get('default_center_lng') ?? 1.888334,
      '#required' => TRUE,
      '#step' => 'any',
      '#min' => -180,
      '#max' => 180,
    ];

    $form['default_radius_km'] = [
      '#type' => 'number',
      '#title' => $this->t('Default search zone radius (km)'),
      '#default_value' => $config->get('default_radius_km') ?? 50,
      '#required' => TRUE,
      '#step' => 'any',
      '#min' => 0.1,
      '#max' => 500,
      '#description' => $this->t('Radius around the default center or locality centroid for the initial map zone.'),
    ];

    $form['list_pager_threshold'] = [
      '#type' => 'number',
      '#title' => $this->t('Load-all list threshold'),
      '#default_value' => $config->get('list_pager_threshold') ?? 100,
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 1000,
      '#description' => $this->t('When a zone has this many results or fewer, the list loads every offer in one page (no infinite scroll).'),
    ];

    $form['markers_max'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum markers per zone'),
      '#default_value' => $config->get('markers_max') ?? 500,
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 1000,
      '#description' => $this->t('Safety cap for the /ps-search/markers JSON endpoint.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_search.map_zone_settings')
      ->set('default_center_lat', (float) $form_state->getValue('default_center_lat'))
      ->set('default_center_lng', (float) $form_state->getValue('default_center_lng'))
      ->set('default_radius_km', (float) $form_state->getValue('default_radius_km'))
      ->set('list_pager_threshold', (int) $form_state->getValue('list_pager_threshold'))
      ->set('markers_max', (int) $form_state->getValue('markers_max'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
