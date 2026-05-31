<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_agent\Service\AgentValidationManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Agent add/edit forms.
 */
final class AgentForm extends ContentEntityForm {

  protected AgentValidationManager $validationManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    /** @var self $instance */
    $instance = parent::create($container);
    $instance->validationManager = $container->get('ps_agent.validation_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $email = $this->extractFieldValue($form_state->getValue('email'));
    $phone = $this->extractFieldValue($form_state->getValue('phone'));

    $violations = $this->validationManager->validateContactValues($email, $phone);
    foreach ($violations as $field_name => $message) {
      $form_state->setErrorByName($field_name, $message);
    }
  }

  /**
   * Extracts a scalar field value from form state data.
   */
  private function extractFieldValue(mixed $raw_value): string {
    if (is_array($raw_value)) {
      return (string) ($raw_value[0]['value'] ?? '');
    }
    return (string) $raw_value;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $status = parent::save($form, $form_state);
    $message = $status === SAVED_NEW
      ? $this->t('Created agent %label.', ['%label' => $this->entity->label()])
      : $this->t('Updated agent %label.', ['%label' => $this->entity->label()]);
    $this->messenger()->addStatus($message);
    $form_state->setRedirect('entity.ps_agent.collection');
    return $status;
  }

}
