<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

final class DictionaryEntryForm extends EntityForm {

  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $entity = $this->entity;
    $types = [];
    $dictionaryTypes = \Drupal::entityTypeManager()->getStorage('ps_dictionary_type')->loadMultiple();
    foreach ($dictionaryTypes as $id => $type) {
      $types[$id] = $type->label();
    }

    $route_type = \Drupal::routeMatch()->getParameter('ps_dictionary_type');
    $route_type_id = NULL;
    if ($route_type instanceof \Drupal\ps_dictionary\Entity\DictionaryType) {
      $route_type_id = $route_type->id();
    }
    elseif (is_string($route_type) && isset($types[$route_type])) {
      $route_type_id = $route_type;
    }
    $default_type = $route_type_id ?? ($entity->get('type') ?: NULL);

    $form['type'] = [
      '#type' => 'select',
      '#title' => $this->t('Dictionary type'),
      '#options' => $types,
      '#default_value' => $default_type,
      '#required' => TRUE,
      '#disabled' => !$entity->isNew() || $route_type_id !== NULL,
    ];

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Code'),
      '#default_value' => $entity->get('code') ?: $this->getDefaultCodeFromQuery(),
      '#maxlength' => 32,
      '#required' => TRUE,
      '#disabled' => !$entity->isNew(),
      '#description' => $this->t('Canonical business code, for example BUR or RENT.'),
    ];

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $entity->label(),
      '#maxlength' => 255,
      '#required' => TRUE,
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $entity->getDescription(),
      '#description' => $this->t('Optional description to document the purpose of this dictionary entry.'),
      '#rows' => 3,
    ];

    $form['icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon CSS class'),
      '#default_value' => $entity->getIcon(),
      '#maxlength' => 128,
      '#description' => $this->t('CSS class for the icon (e.g. <code>icon-bureau</code>). Leave empty to use a default placeholder.'),
    ];

    $form['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $entity->get('weight') ?? 0,
      '#required' => TRUE,
    ];

    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $type = (string) $form_state->getValue('type');
    $code = mb_strtoupper(trim((string) $form_state->getValue('code')));

    if ($code === '') {
      $form_state->setErrorByName('code', $this->t('Code is required.'));
      return;
    }

    if ($this->entity->isNew()) {
      $id = $type . '.' . mb_strtolower($code);
      if (\Drupal::entityTypeManager()->getStorage('ps_dictionary_entry')->load($id) !== NULL) {
        $form_state->setErrorByName('code', $this->t('An entry with this type/code already exists.'));
      }
    }
  }

  /**
   * Gets default code value from query parameters (for quick-add from offer form).
   */
  private function getDefaultCodeFromQuery(): string {
    $request = \Drupal::request();
    $code = $request->query->get('code');
    return is_string($code) ? trim($code) : '';
  }

  /**
   * Assigns the newly created dictionary entry to the offer field.
   */
  private function assignDictionaryToOffer(int $node_id, string $field_name, string $dictionary_id): void {
    try {
      $node_storage = \Drupal::entityTypeManager()->getStorage('node');
      $node = $node_storage->load($node_id);
      
      if (!$node || $node->bundle() !== 'offer') {
        return;
      }
      
      if (!$node->hasField($field_name)) {
        return;
      }
      
      // Extract just the code from the dictionary ID (e.g., "asset_type.xxx" -> "XXX").
      // The ps_dictionary field stores uppercase codes, not full IDs.
      $parts = explode('.', $dictionary_id);
      $code = end($parts);
      
      $node->set($field_name, mb_strtoupper($code));
      
      // Clear the raw code field now that we have assigned the dictionary.
      $raw_field_name = $field_name . '_raw';
      if ($node->hasField($raw_field_name)) {
        $node->set($raw_field_name, NULL);
      }
      
      $node->save();
      
      $this->messenger()->addStatus($this->t('The dictionary entry has been assigned to the offer.'));
    }
    catch (\Exception $e) {
      \Drupal::logger('ps_dictionary')->error('Failed to assign dictionary to offer: @message', ['@message' => $e->getMessage()]);
      $this->messenger()->addWarning($this->t('The dictionary entry was created but could not be automatically assigned to the offer. Please assign it manually.'));
    }
  }

  public function save(array $form, FormStateInterface $form_state): int {
    $entity = $this->entity;

    if ($entity->isNew()) {
      $type = (string) $form_state->getValue('type');
      $code = mb_strtoupper(trim((string) $form_state->getValue('code')));
      $entity->set('type', $type);
      $entity->set('code', $code);
      $entity->set('id', $type . '.' . mb_strtolower($code));
    }

    $entity->set('label', (string) $form_state->getValue('label'));
    $entity->set('weight', (int) $form_state->getValue('weight'));
    $entity->set('icon', trim((string) $form_state->getValue('icon', '')) ?: NULL);

    $status = $entity->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created dictionary entry %label.', ['%label' => $entity->label()]));
    }
    else {
      $this->messenger()->addStatus($this->t('Updated dictionary entry %label.', ['%label' => $entity->label()]));
    }

    // If created from offer form, assign to offer and redirect back.
    $request = \Drupal::request();
    $offer_node_id = $request->query->get('offer_node');
    $offer_field = $request->query->get('offer_field');
    
    if ($status === SAVED_NEW && $offer_node_id && $offer_field) {
      $this->assignDictionaryToOffer((int) $offer_node_id, (string) $offer_field, $entity->id());
      $form_state->setRedirect('entity.node.edit_form', ['node' => $offer_node_id]);
    }
    else {
      $form_state->setRedirect('ps_dictionary.entry_collection', ['ps_dictionary_type' => $entity->getType()]);
    }
    
    return $status;
  }

}
