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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['poi_types'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Show points of interest:'),
      '#options' => [
        'transports' => $this->t('Transports'),
        'parkings' => $this->t('Parkings'),
        'restaurants' => $this->t('Restaurants'),
        'hotels' => $this->t('Hotels'),
      ],
      '#default_value' => [],
      '#attributes' => [
        'class' => ['ps-geo-directions-poi-types', 'ps-nearby-places-checkbox'],
      ],
    ];

    // Correspondance entre les clés et les types Google Places.
    $google_places_types = [
      'transports' => 'transit_station',
      'parkings' => 'parking',
      'restaurants' => 'restaurant',
      'hotels' => 'lodging',
    ];
    // Rayon de recherche par défaut (en mètres).
    $radius = 800;
    // Injecter dans drupalSettings pour le JS.
    $form['#attached']['drupalSettings']['ps_nearby_places'] = [
      'categories' => array_values($google_places_types),
      'category_map' => $google_places_types,
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
