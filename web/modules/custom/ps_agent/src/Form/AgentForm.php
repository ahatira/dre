<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Agent entity form.
 *
 * Standard entity form for creating and editing agent entities with proper
 * field handling and validation.
 *
 * @ingroup ps_agent
 */
final class AgentForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $formState): array {
    $form = parent::form($form, $formState);

    $agent = $this->entity;

    // Improve type field display if not creating
    if (!$agent->isNew() && isset($form['type'])) {
      $form['type']['#disabled'] = TRUE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validate(array $form, FormStateInterface $formState): void {
    parent::validate($form, $formState);

    /** @var \Drupal\ps_agent\Entity\AgentInterface $agent */
    $agent = $this->entity;

    // Validate required fields
    if (!$agent->getFirstName()) {
      $formState->setErrorByName('first_name', $this->t('First Name is required.'));
    }

    if (!$agent->getLastName()) {
      $formState->setErrorByName('last_name', $this->t('Last Name is required.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $formState): int {
    /** @var \Drupal\ps_agent\Entity\AgentInterface $agent */
    $agent = $this->entity;
    $isNew = $agent->isNew();

    $result = $agent->save();

    $message = $isNew
      ? $this->t('Agent %label has been created.', ['%label' => $agent->label()])
      : $this->t('Agent %label has been updated.', ['%label' => $agent->label()]);

    $this->messenger()->addStatus($message);

    $formState->setRedirectUrl($agent->toUrl('collection'));

    return $result;
  }

}
