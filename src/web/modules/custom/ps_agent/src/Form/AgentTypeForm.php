<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Entity\BundleEntityFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for adding/editing Agent types.
 */
final class AgentTypeForm extends BundleEntityFormBase {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_agent\Entity\AgentType $entity */
    $entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\ps_agent\Entity\AgentType::load',
      ],
      '#disabled' => !$entity->isNew(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $result = parent::save($form, $form_state);

    $message_args = ['%label' => $this->entity->label()];
    $message = $result === SAVED_NEW
      ? $this->t('The agent type %label has been created.', $message_args)
      : $this->t('The agent type %label has been updated.', $message_args);
    $this->messenger()->addStatus($message);

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $result;
  }

}
