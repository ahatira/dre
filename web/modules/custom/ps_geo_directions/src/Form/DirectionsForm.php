<?php
namespace Drupal\ps_geo_directions\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class DirectionsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ps_geo_directions_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $formatter_config = []) {
    // Récupère la config dynamique passée par le formatter (ou fallback)
    $config = is_array($formatter_config) ? $formatter_config : [];
    $all_options = [
      'transports' => $this->t('Transports'),
      'parkings' => $this->t('Parkings'),
      'restaurants' => $this->t('Restaurants'),
      'hotels' => $this->t('Hotels'),
    ];
    $all_types = [
      'transports' => 'transit_station',
      'parkings' => 'parking',
      'restaurants' => 'restaurant',
      'hotels' => 'lodging',
    ];
    $enabled_types = isset($config['poi_types']) && is_array($config['poi_types']) ? $config['poi_types'] : array_keys($all_options);
    $radius = isset($config['poi_radius']) ? (int)$config['poi_radius'] : 800;
    // Filtre dynamiquement les options et le mapping
    $filtered_options = array_intersect_key($all_options, array_flip($enabled_types));
    $filtered_types = array_intersect_key($all_types, array_flip($enabled_types));
    $form['poi_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Show points of interest:'),
      '#options' => $filtered_options,
      '#default_value' => [], // Aucun filtre coché par défaut
      '#attributes' => [
        'class' => ['ps-geo-directions-poi-types', 'ps-nearby-places-checkbox'],
      ],
    ];
    // Injecte la config dynamique dans drupalSettings
    $form['#attached']['drupalSettings']['ps_nearby_places'] = [
      'categories' => array_values($enabled_types),
      'category_map' => $filtered_types,
      'radius' => $radius,
    ];
    $form['address'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Starting point'),
      '#attributes' => [
        'placeholder' => $this->t('Enter an address'),
        'class' => ['ps-geo-directions-address'],
        'autocomplete' => 'off',
      ],
      '#prefix' => '<span class="ps-geo-directions-icon" aria-hidden="true">📍</span>',
      '#suffix' => '<span class="ps-geo-directions-clear" tabindex="0" role="button" aria-label="Clear">×</span>',
    ];
      $form['travel_mode'] = [
        '#type' => 'radios',
        '#title' => $this->t('Mode de transport'),
        '#options' => [
          'DRIVING' => $this->t('Voiture'),
          'TRANSIT' => $this->t('Transport'),
          'WALKING' => $this->t('Marche'),
          'BICYCLING' => $this->t('Vélo'),
        ],
        '#default_value' => 'DRIVING',
        '#attributes' => [
          'class' => ['ps-geo-directions-mode'],
        ],
        '#wrapper_attributes' => [
          'class' => ['ps-geo-directions-mode-wrapper'],
        ],
      ];
    $form['#attached']['library'][] = 'ps_geo_directions/directions';
    // Attach origin and debug to drupalSettings for JS.
    // La variable 'origin' est injectée dynamiquement via le formatter dans drupalSettings.
    $form['#attributes']['class'][] = 'ps-geo-directions-form';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Pas de soumission, tout est JS.
  }
}
