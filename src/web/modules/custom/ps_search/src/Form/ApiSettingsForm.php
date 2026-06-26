<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for API rate limits and server-side cache TTLs.
 */
final class ApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_search_api_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'ps_search.api_rate_limit_settings',
      'ps_search.api_cache_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $rate = $this->config('ps_search.api_rate_limit_settings');
    $cache = $this->config('ps_search.api_cache_settings');

    $form['intro'] = [
      '#markup' => '<p>' . $this->t('Rate limits and server-side cache TTLs for public JSON and HTMX endpoints under /api/ps/* (markers, isochrone, location suggest, count and filter fragments).') . '</p>',
    ];

    $form['rate_limit'] = [
      '#type' => 'details',
      '#title' => $this->t('Rate limiting'),
      '#open' => TRUE,
    ];

    $form['rate_limit']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable IP rate limiting on public API routes'),
      '#default_value' => $rate->get('enabled') ?? TRUE,
    ];

    $form['rate_limit']['window_seconds'] = [
      '#type' => 'number',
      '#title' => $this->t('Window (seconds)'),
      '#default_value' => $rate->get('window_seconds') ?? 60,
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 3600,
    ];

    $form['rate_limit']['default_max_requests'] = [
      '#type' => 'number',
      '#title' => $this->t('Default max requests per window'),
      '#default_value' => $rate->get('default_max_requests') ?? 120,
      '#required' => TRUE,
      '#min' => 1,
      '#max' => 10000,
    ];

    $routeLimits = $rate->get('routes') ?? [];
    $form['rate_limit']['routes'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Per-route limits (requests per window)'),
    ];
    foreach ([
      'markers' => $this->t('Markers JSON'),
      'isochrone' => $this->t('Isochrone'),
      'location_suggest' => $this->t('Location suggest'),
      'location_data' => $this->t('Location data'),
      'count' => $this->t('Result count'),
      'htmx' => $this->t('HTMX fragments'),
    ] as $key => $label) {
      $form['rate_limit']['routes'][$key] = [
        '#type' => 'number',
        '#title' => $label,
        '#default_value' => $routeLimits[$key] ?? 60,
        '#required' => TRUE,
        '#min' => 1,
        '#max' => 10000,
      ];
    }

    $form['cache'] = [
      '#type' => 'details',
      '#title' => $this->t('Server cache'),
      '#open' => TRUE,
    ];

    $form['cache']['markers_ttl'] = [
      '#type' => 'number',
      '#title' => $this->t('Markers JSON HTTP cache max-age (seconds)'),
      '#default_value' => $cache->get('markers_ttl') ?? 60,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 86400,
    ];

    $form['cache']['isochrone_ttl'] = [
      '#type' => 'number',
      '#title' => $this->t('Isochrone server cache TTL (seconds)'),
      '#default_value' => $cache->get('isochrone_ttl') ?? 86400,
      '#required' => TRUE,
      '#min' => 60,
      '#max' => 604800,
      '#description' => $this->t('Default 86400 (24h). Applies to computed isochrone payloads before external provider calls.'),
    ];

    $form['cache']['isochrone_coordinate_precision'] = [
      '#type' => 'number',
      '#title' => $this->t('Isochrone cache coordinate precision (decimal places)'),
      '#default_value' => $cache->get('isochrone_coordinate_precision') ?? 4,
      '#required' => TRUE,
      '#min' => 2,
      '#max' => 6,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('ps_search.api_rate_limit_settings')
      ->set('enabled', (bool) $form_state->getValue(['rate_limit', 'enabled']))
      ->set('window_seconds', (int) $form_state->getValue(['rate_limit', 'window_seconds']))
      ->set('default_max_requests', (int) $form_state->getValue(['rate_limit', 'default_max_requests']))
      ->set('routes', [
        'markers' => (int) $form_state->getValue(['rate_limit', 'routes', 'markers']),
        'isochrone' => (int) $form_state->getValue(['rate_limit', 'routes', 'isochrone']),
        'location_suggest' => (int) $form_state->getValue(['rate_limit', 'routes', 'location_suggest']),
        'location_data' => (int) $form_state->getValue(['rate_limit', 'routes', 'location_data']),
        'count' => (int) $form_state->getValue(['rate_limit', 'routes', 'count']),
        'htmx' => (int) $form_state->getValue(['rate_limit', 'routes', 'htmx']),
      ])
      ->save();

    $this->config('ps_search.api_cache_settings')
      ->set('markers_ttl', (int) $form_state->getValue(['cache', 'markers_ttl']))
      ->set('isochrone_ttl', (int) $form_state->getValue(['cache', 'isochrone_ttl']))
      ->set('isochrone_coordinate_precision', (int) $form_state->getValue(['cache', 'isochrone_coordinate_precision']))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
