<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

final class DictionaryTypeForm extends EntityForm {

  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    $entity = $this->entity;

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
      '#description' => $this->t('Optional description to document the purpose of this dictionary type.'),
      '#rows' => 3,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\\Drupal\\ps_dictionary\\Entity\\DictionaryType::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    return $form;
  }

  public function save(array $form, FormStateInterface $form_state): int {
    $entity = $this->entity;
    $status = $entity->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Created dictionary type %label.', ['%label' => $entity->label()]));
    }
    else {
      $this->messenger()->addStatus($this->t('Updated dictionary type %label.', ['%label' => $entity->label()]));
    }

    $form_state->setRedirectUrl($entity->toUrl('collection'));
    return $status;
  }

}
