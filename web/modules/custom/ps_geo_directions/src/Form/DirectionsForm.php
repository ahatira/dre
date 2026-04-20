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
    $form['#attached']['library'][] = 'ps_geo_directions/directions';
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
