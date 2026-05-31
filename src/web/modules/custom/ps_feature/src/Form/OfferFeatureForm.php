<?php

namespace Drupal\ps_feature\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the Offer Feature entity edit forms.
 */
class OfferFeatureForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    /** @var \Drupal\ps_feature\Entity\OfferFeature $entity */
    $entity = $this->entity;

    // Get services.
    $definition_storage = $this->entityTypeManager->getStorage('fb_feature_definition');

    // Feature definition selector.
    $definitions = $definition_storage->loadMultiple();
    $definition_options = [];
    foreach ($definitions as $id => $definition) {
      $definition_options[$id] = $definition->label();
    }

    $form['feature_definition_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Feature definition'),
      '#description' => $this->t('Select the feature definition to use.'),
      '#options' => $definition_options,
      '#default_value' => $entity->getFeatureDefinitionId(),
      '#required' => TRUE,
      '#empty_option' => $this->t('- Select -'),
      '#weight' => 0,
    ];

    // Type driver (automatically filled based on definition).
    $type_labels = [
      'flag' => $this->t('Flag'),
      'yes_no' => $this->t('Yes/No'),
      'numeric' => $this->t('Numeric value'),
      'range' => $this->t('Range'),
      'text' => $this->t('Text'),
      'dictionary' => $this->t('Dictionary'),
      'list' => $this->t('List'),
      'date' => $this->t('Date'),
    ];

    $form['feature_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Data type'),
      '#description' => $this->t('Feature data type.'),
      '#options' => $type_labels,
      '#default_value' => $entity->getFeatureType(),
      '#required' => TRUE,
      '#weight' => 1,
    ];

    // Payload JSON field.
    $form['payload'] = [
      '#type' => 'textarea',
      '#title' => $this->t('JSON data'),
      '#description' => $this->t('Feature data in JSON format. Example: {"value": 250, "unit": "m2"}'),
      '#default_value' => $entity->get('payload')->value ?: '{}',
      '#required' => TRUE,
      '#rows' => 5,
      '#weight' => 2,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    // Validate JSON payload.
    $payload_string = $form_state->getValue('payload');
    $payload = json_decode($payload_string, TRUE);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $form_state->setErrorByName('payload', $this->t('Invalid JSON format: @error', [
        '@error' => json_last_error_msg(),
      ]));
      return;
    }

    // Store decoded payload for save.
    $form_state->setValue('payload', json_encode($payload, JSON_UNESCAPED_UNICODE));
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_feature\Entity\OfferFeature $entity */
    $entity = $this->entity;

    // Set values from form.
    $entity->set('feature_definition_id', $form_state->getValue('feature_definition_id'));
    $entity->set('feature_type', $form_state->getValue('feature_type'));
    $entity->set('payload', $form_state->getValue('payload'));

    $result = $entity->save();

    $message = $result == SAVED_NEW
      ? $this->t('Offer feature #@id created.', ['@id' => $entity->id()])
      : $this->t('Offer feature #@id updated.', ['@id' => $entity->id()]);
    $this->messenger()->addStatus($message);

    $form_state->setRedirect('entity.entity_offer_feature.collection');

    return $result;
  }

}
