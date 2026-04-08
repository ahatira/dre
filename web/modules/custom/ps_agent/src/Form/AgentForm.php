<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Agent edit forms.
 */
final class AgentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $status = parent::save($form, $form_state);

    $message = $status === SAVED_UPDATED
      ? $this->t('The agent %label has been updated.', ['%label' => $this->entity->label()])
      : $this->t('The agent %label has been created.', ['%label' => $this->entity->label()]);
    $this->messenger()->addStatus($message);

    $form_state->setRedirect('entity.agent.collection');

    return $status;
  }

}
