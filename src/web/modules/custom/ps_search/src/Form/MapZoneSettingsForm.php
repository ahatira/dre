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

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Default search zone (center, radius, marker limits) and PS-owned Google Map shell on the search page. The Google Maps JavaScript API key is read from Geofield Map settings (/admin/config/system/geofield-map).') . '</p>',
    ];

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

    $form['map_shell'] = [
      '#type' => 'details',
      '#title' => $this->t('Map shell (PS native cartography)'),
      '#open' => FALSE,
      '#description' => $this->t('Settings for the PS-owned Google Map shell on the search page. The Google Maps JavaScript API key is read from Geofield Map settings (/admin/config/system/geofield-map).'),
    ];

    $form['map_shell']['default_zoom'] = [
      '#type' => 'number',
      '#title' => $this->t('Default map zoom'),
      '#default_value' => $config->get('default_zoom') ?? 6,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 22,
    ];

    $form['map_shell']['zoom_min'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum zoom'),
      '#default_value' => $config->get('zoom_min') ?? 1,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 22,
    ];

    $form['map_shell']['zoom_max'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum zoom'),
      '#default_value' => $config->get('zoom_max') ?? 22,
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 22,
    ];

    $form['map_shell']['gesture_handling'] = [
      '#type' => 'select',
      '#title' => $this->t('Gesture handling'),
      '#default_value' => $config->get('gesture_handling') ?? 'cooperative',
      '#required' => TRUE,
      '#options' => [
        'cooperative' => $this->t('Cooperative (Ctrl + scroll to zoom)'),
        'auto' => $this->t('Auto'),
        'greedy' => $this->t('Greedy'),
        'none' => $this->t('None'),
      ],
      '#description' => $this->t('Cooperative is recommended on search pages: the map does not capture the mouse wheel until the user holds Ctrl (or ⌘ on macOS) while scrolling.'),
    ];

    $form['map_shell']['marker_clustering'] = [
      '#type' => 'details',
      '#title' => $this->t('Client-side marker clustering'),
      '#open' => TRUE,
      '#description' => $this->t('Controls MarkerClusterer on the search map (price bubbles grouped at low zoom). Server-side grid clusters for dense zones are configured separately below.'),
    ];

    $form['map_shell']['marker_clustering']['marker_cluster_max_zoom'] = [
      '#type' => 'number',
      '#title' => $this->t('Cluster max zoom'),
      '#default_value' => $config->get('marker_cluster_max_zoom') ?? 16,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 22,
      '#description' => $this->t('Individual price markers are shown above this zoom level. Clusters appear at this zoom and below (Google Maps zoom 0–22). Recommended: 16.'),
    ];

    $form['map_shell']['marker_clustering']['marker_cluster_skip_below'] = [
      '#type' => 'number',
      '#title' => $this->t('Skip clustering up to this marker count'),
      '#default_value' => $config->get('marker_cluster_skip_below') ?? 10,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 1000,
      '#description' => $this->t('When the map displays this many markers or fewer, always show individual price markers (no cluster bubble). Set to 0 to always cluster when zoom allows. Recommended: 10.'),
    ];

    $form['map_shell']['marker_clustering']['cluster_options'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Advanced cluster options (JSON)'),
      '#default_value' => $config->get('cluster_options') ?? '',
      '#rows' => 6,
      '#description' => $this->t('Optional MarkerClusterer overrides (gridSize, styles, minimumClusterSize, …). Max zoom is always taken from the field above. Leave empty for BNPPRE defaults.'),
    ];

    $form['map_shell']['google_map_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Cloud Map ID'),
      '#default_value' => $config->get('google_map_id') ?? '',
      '#description' => $this->t('Optional. Required for Advanced Markers (future migration). Create a Map ID in Google Cloud Console.'),
    ];

    $form['map_shell']['lazy_load'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Lazy-load map shell'),
      '#default_value' => $config->get('lazy_load') ?? FALSE,
      '#description' => $this->t('Reserved for a future release (IntersectionObserver). Currently ignored.'),
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
      '#description' => $this->t('Safety cap for the /api/ps/markers JSON endpoint.'),
    ];

    $form['markers_cluster_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable server-side grid clusters for dense zones'),
      '#default_value' => $config->get('markers_cluster_enabled') ?? TRUE,
      '#description' => $this->t('When zone_count exceeds the markers cap, return grid clusters instead of silently truncating individual markers.'),
    ];

    $form['markers_cluster_cells'] = [
      '#type' => 'number',
      '#title' => $this->t('Grid cluster target cells'),
      '#default_value' => $config->get('markers_cluster_cells') ?? 64,
      '#required' => TRUE,
      '#min' => 4,
      '#max' => 256,
      '#description' => $this->t('Approximate number of grid cells used to aggregate markers (e.g. 64 ≈ 8×8).'),
      '#states' => [
        'visible' => [
          ':input[name="markers_cluster_enabled"]' => ['checked' => TRUE],
        ],
      ],
    ];

    $form['isochrone'] = [
      '#type' => 'details',
      '#title' => $this->t('Distance zone isochrone'),
      '#open' => TRUE,
    ];

    $orsEnabled = (bool) ($config->get('ors_enabled') ?? FALSE);

    $form['isochrone']['ors_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable OpenRouteService provider'),
      '#default_value' => $orsEnabled,
      '#description' => $this->t('When disabled, ORS is hidden from the provider list and cannot be used, even if a key is stored.'),
    ];

    $providerOptions = [
      'approximation' => $this->t('Approximation (offline circle)'),
      'google' => $this->t('Google Routes (radial sampling)'),
    ];
    if ($orsEnabled) {
      $providerOptions['ors'] = $this->t('OpenRouteService');
    }

    $currentProvider = (string) ($config->get('isochrone_provider') ?? 'approximation');
    if (!$orsEnabled && $currentProvider === 'ors') {
      $currentProvider = 'approximation';
    }

    $form['isochrone']['isochrone_provider'] = [
      '#type' => 'select',
      '#title' => $this->t('Isochrone provider'),
      '#default_value' => $currentProvider,
      '#required' => TRUE,
      '#options' => $providerOptions,
      '#description' => $this->t('External providers require API keys. On failure, fallback uses the offline approximation when enabled.'),
    ];

    $form['isochrone']['isochrone_fallback'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Fallback to approximation when external provider fails'),
      '#default_value' => $config->get('isochrone_fallback') ?? TRUE,
    ];

    $form['isochrone']['ors_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('OpenRouteService API key'),
      '#default_value' => $config->get('ors_api_key') ?? '',
      '#description' => $this->t('Required when provider is OpenRouteService. Get a key at openrouteservice.org.'),
      '#access' => $orsEnabled,
    ];

    $form['isochrone']['google_routes_api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Google Routes API key (optional override)'),
      '#default_value' => $config->get('google_routes_api_key') ?? '',
      '#description' => $this->t('Leave empty to reuse the Geofield Map Google Maps API key. The Google Cloud project must enable the Routes API (ComputeRoutes) in addition to Maps JavaScript API.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $orsEnabled = (bool) $form_state->getValue('ors_enabled');
    $provider = (string) $form_state->getValue('isochrone_provider');
    if (!$orsEnabled && $provider === 'ors') {
      $provider = 'approximation';
    }

    $clusterOptions = trim((string) $form_state->getValue('cluster_options'));
    if ($clusterOptions !== '') {
      try {
        json_decode($clusterOptions, TRUE, 512, JSON_THROW_ON_ERROR);
      }
      catch (\JsonException $exception) {
        $form_state->setErrorByName('cluster_options', $this->t('Advanced cluster options must be valid JSON.'));
        return;
      }
    }

    $this->config('ps_search.map_zone_settings')
      ->set('default_center_lat', (float) $form_state->getValue('default_center_lat'))
      ->set('default_center_lng', (float) $form_state->getValue('default_center_lng'))
      ->set('default_radius_km', (float) $form_state->getValue('default_radius_km'))
      ->set('default_zoom', (int) $form_state->getValue('default_zoom'))
      ->set('zoom_min', (int) $form_state->getValue('zoom_min'))
      ->set('zoom_max', (int) $form_state->getValue('zoom_max'))
      ->set('gesture_handling', (string) $form_state->getValue('gesture_handling'))
      ->set('marker_cluster_max_zoom', (int) $form_state->getValue('marker_cluster_max_zoom'))
      ->set('marker_cluster_skip_below', (int) $form_state->getValue('marker_cluster_skip_below'))
      ->set('cluster_options', $clusterOptions)
      ->set('google_map_id', trim((string) $form_state->getValue('google_map_id')))
      ->set('lazy_load', (bool) $form_state->getValue('lazy_load'))
      ->set('list_pager_threshold', (int) $form_state->getValue('list_pager_threshold'))
      ->set('markers_max', (int) $form_state->getValue('markers_max'))
      ->set('markers_cluster_enabled', (bool) $form_state->getValue('markers_cluster_enabled'))
      ->set('markers_cluster_cells', (int) $form_state->getValue('markers_cluster_cells'))
      ->set('isochrone_provider', $provider)
      ->set('isochrone_fallback', (bool) $form_state->getValue('isochrone_fallback'))
      ->set('ors_enabled', $orsEnabled)
      ->set('ors_api_key', trim((string) $form_state->getValue('ors_api_key')))
      ->set('google_routes_api_key', trim((string) $form_state->getValue('google_routes_api_key')))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
