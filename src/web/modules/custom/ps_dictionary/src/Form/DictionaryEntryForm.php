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
      '#default_value' => $entity->get('code') ?: '',
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

    $form_state->setRedirect('ps_dictionary.entry_collection', ['ps_dictionary_type' => $entity->getType()]);
    return $status;
  }

}
