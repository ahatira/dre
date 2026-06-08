<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_core\Form\IconAutocompleteHelperTrait;

/**
 * Site-wide settings for the offer detail interactive map.
 */
final class OfferMapSettingsForm extends ConfigFormBase {

  use IconAutocompleteHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'ps_offer_map_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['ps_offer.map_settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('ps_offer.map_settings');

    $form['general'] = [
      '#type' => 'details',
      '#title' => $this->t('General'),
      '#open' => TRUE,
    ];

    $form['general']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable offer detail map'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['general']['poi_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable POI filters'),
      '#default_value' => $config->get('poi_enabled'),
    ];

    $form['general']['travel_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable travel time tools'),
      '#description' => $this->t('Only shown when the offer displays its precise address.'),
      '#default_value' => $config->get('travel_enabled'),
    ];

    $form['display'] = [
      '#type' => 'details',
      '#title' => $this->t('Map display'),
      '#open' => TRUE,
    ];

    $form['display']['default_zoom_exact'] = [
      '#type' => 'number',
      '#title' => $this->t('Default zoom (exact address)'),
      '#min' => 1,
      '#max' => 21,
      '#default_value' => $config->get('default_zoom_exact'),
      '#required' => TRUE,
    ];

    $form['display']['default_zoom_approx'] = [
      '#type' => 'number',
      '#title' => $this->t('Default zoom (approximate location)'),
      '#min' => 1,
      '#max' => 21,
      '#default_value' => $config->get('default_zoom_approx'),
      '#required' => TRUE,
    ];

    $form['display']['default_zoom_approx_large_city'] = [
      '#type' => 'number',
      '#title' => $this->t('Default zoom (approximate location, large city)'),
      '#min' => 1,
      '#max' => 21,
      '#default_value' => $config->get('default_zoom_approx_large_city'),
      '#required' => TRUE,
    ];

    $form['display']['circle_radius_m'] = [
      '#type' => 'number',
      '#title' => $this->t('Approximate area circle radius (meters)'),
      '#min' => 100,
      '#default_value' => $config->get('circle_radius_m'),
      '#required' => TRUE,
    ];

    $form['display']['circle_radius_large_cities_m'] = [
      '#type' => 'number',
      '#title' => $this->t('Approximate area circle radius in large cities (meters)'),
      '#min' => 100,
      '#default_value' => $config->get('circle_radius_large_cities_m'),
      '#required' => TRUE,
    ];

    $form['display']['circle_color'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Approximate area circle color'),
      '#description' => $this->t('CSS color value, for example #00915A.'),
      '#default_value' => $config->get('circle_color'),
      '#required' => TRUE,
      '#maxlength' => 32,
    ];

    $form['display']['large_city_localities'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Large city localities'),
      '#description' => $this->t('One city per line. Used for approximate map zoom and circle radius.'),
      '#default_value' => $config->get('large_city_localities'),
      '#rows' => 4,
    ];

    $form['poi'] = [
      '#type' => 'details',
      '#title' => $this->t('Points of interest'),
      '#open' => FALSE,
    ];

    $form['poi']['poi_search_radius_m'] = [
      '#type' => 'number',
      '#title' => $this->t('POI search radius (meters)'),
      '#min' => 100,
      '#default_value' => $config->get('poi_search_radius_m'),
      '#required' => TRUE,
    ];

    $form['poi']['poi_filter_icons'] = [
      '#type' => 'details',
      '#title' => $this->t('POI filter icons'),
      '#description' => $this->t('Icons displayed next to POI filter checkboxes on the offer map.'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['poi']['poi_filter_icons']['poi_icon_transport'] = $this->buildIconPickerElement(
      $this->t('Transports'),
      $this->getIconDefault($config->get('poi_icon_transport'), 'bnp_custom:transport'),
      ['required' => TRUE],
    );

    $form['poi']['poi_filter_icons']['poi_icon_parkings'] = $this->buildIconPickerElement(
      $this->t('Parkings'),
      $this->getIconDefault($config->get('poi_icon_parkings'), 'bnp_custom:parking-borders'),
      ['required' => TRUE],
    );

    $form['poi']['poi_filter_icons']['poi_icon_restaurants'] = $this->buildIconPickerElement(
      $this->t('Restaurants'),
      $this->getIconDefault($config->get('poi_icon_restaurants'), 'bnp_custom:restaurant'),
      ['required' => TRUE],
    );

    $form['poi']['poi_filter_icons']['poi_icon_hotels'] = $this->buildIconPickerElement(
      $this->t('Hotels'),
      $this->getIconDefault($config->get('poi_icon_hotels'), 'bnp_custom:hotel'),
      ['required' => TRUE],
    );

    $form['poi']['poi_marker_icons'] = [
      '#type' => 'details',
      '#title' => $this->t('POI map marker icons'),
      '#description' => $this->t('Pin icons displayed on the map for each POI category.'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['poi']['poi_marker_icons']['poi_marker_icon_transport'] = $this->buildIconPickerElement(
      $this->t('Transports'),
      $this->getIconDefault($config->get('poi_marker_icon_transport'), 'bnp_custom:poi-transport'),
      ['required' => TRUE],
    );

    $form['poi']['poi_marker_icons']['poi_marker_icon_parkings'] = $this->buildIconPickerElement(
      $this->t('Parkings'),
      $this->getIconDefault($config->get('poi_marker_icon_parkings'), 'bnp_custom:poi-parking'),
      ['required' => TRUE],
    );

    $form['poi']['poi_marker_icons']['poi_marker_icon_restaurants'] = $this->buildIconPickerElement(
      $this->t('Restaurants'),
      $this->getIconDefault($config->get('poi_marker_icon_restaurants'), 'bnp_custom:poi-restaurant'),
      ['required' => TRUE],
    );

    $form['poi']['poi_marker_icons']['poi_marker_icon_hotels'] = $this->buildIconPickerElement(
      $this->t('Hotels'),
      $this->getIconDefault($config->get('poi_marker_icon_hotels'), 'bnp_custom:poi-hotel'),
      ['required' => TRUE],
    );

    $form['poi']['poi_marker_colors'] = [
      '#type' => 'details',
      '#title' => $this->t('POI map marker colors'),
      '#description' => $this->t('Pin colors applied to each POI category on the map.'),
      '#open' => TRUE,
    ];

    foreach ([
      'poi_marker_color_transport' => $this->t('Transports'),
      'poi_marker_color_parkings' => $this->t('Parkings'),
      'poi_marker_color_restaurants' => $this->t('Restaurants'),
      'poi_marker_color_hotels' => $this->t('Hotels'),
    ] as $key => $label) {
      $form['poi']['poi_marker_colors'][$key] = [
        '#type' => 'textfield',
        '#title' => $label,
        '#description' => $this->t('CSS color value, for example #0072CE.'),
        '#default_value' => $config->get($key),
        '#required' => TRUE,
        '#maxlength' => 32,
      ];
    }

    $form['travel'] = [
      '#type' => 'details',
      '#title' => $this->t('Travel time'),
      '#open' => FALSE,
    ];

    $form['travel']['travel_mode_icons'] = [
      '#type' => 'details',
      '#title' => $this->t('Travel mode icons'),
      '#description' => $this->t('Icons displayed on the travel mode selector when the offer shows its precise address.'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['travel']['travel_mode_icons']['travel_mode_icon_driving'] = $this->buildIconPickerElement(
      $this->t('Car'),
      $this->getIconDefault($config->get('travel_mode_icon_driving'), 'bnp_custom:car'),
      ['required' => TRUE],
    );

    $form['travel']['travel_mode_icons']['travel_mode_icon_transit'] = $this->buildIconPickerElement(
      $this->t('Public transport'),
      $this->getIconDefault($config->get('travel_mode_icon_transit'), 'bnp_custom:transport'),
      ['required' => TRUE],
    );

    $form['travel']['travel_mode_icons']['travel_mode_icon_walking'] = $this->buildIconPickerElement(
      $this->t('Walking'),
      $this->getIconDefault($config->get('travel_mode_icon_walking'), 'bnp_custom:walking'),
      ['required' => TRUE],
    );

    $form['travel']['travel_mode_icons']['travel_mode_icon_bicycling'] = $this->buildIconPickerElement(
      $this->t('Cycling'),
      $this->getIconDefault($config->get('travel_mode_icon_bicycling'), 'bnp_custom:bike'),
      ['required' => TRUE],
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $editable = $this->configFactory->getEditable('ps_offer.map_settings');
    foreach ([
      'enabled',
      'poi_enabled',
      'travel_enabled',
      'default_zoom_exact',
      'default_zoom_approx',
      'default_zoom_approx_large_city',
      'circle_radius_m',
      'circle_radius_large_cities_m',
      'circle_color',
      'large_city_localities',
      'poi_search_radius_m',
      'poi_marker_color_transport',
      'poi_marker_color_parkings',
      'poi_marker_color_restaurants',
      'poi_marker_color_hotels',
    ] as $key) {
      $editable->set($key, $form_state->getValue($key));
    }

    foreach ([
      'travel_mode_icon_driving' => 'bnp_custom:car',
      'travel_mode_icon_transit' => 'bnp_custom:transport',
      'travel_mode_icon_walking' => 'bnp_custom:walking',
      'travel_mode_icon_bicycling' => 'bnp_custom:bike',
    ] as $key => $fallback) {
      $editable->set(
        $key,
        $this->extractIconId($this->getSubmittedIconValue($form_state, $key, 'travel_mode_icons'), $fallback),
      );
    }

    foreach ([
      'poi_icon_transport' => 'bnp_custom:transport',
      'poi_icon_parkings' => 'bnp_custom:parking-borders',
      'poi_icon_restaurants' => 'bnp_custom:restaurant',
      'poi_icon_hotels' => 'bnp_custom:hotel',
    ] as $key => $fallback) {
      $editable->set(
        $key,
        $this->extractIconId($this->getSubmittedIconValue($form_state, $key, 'poi_filter_icons'), $fallback),
      );
    }

    foreach ([
      'poi_marker_icon_transport' => 'bnp_custom:poi-transport',
      'poi_marker_icon_parkings' => 'bnp_custom:poi-parking',
      'poi_marker_icon_restaurants' => 'bnp_custom:poi-restaurant',
      'poi_marker_icon_hotels' => 'bnp_custom:poi-hotel',
    ] as $key => $fallback) {
      $editable->set(
        $key,
        $this->extractIconId($this->getSubmittedIconValue($form_state, $key, 'poi_marker_icons'), $fallback),
      );
    }

    $editable->save();

    parent::submitForm($form, $form_state);
  }

}
