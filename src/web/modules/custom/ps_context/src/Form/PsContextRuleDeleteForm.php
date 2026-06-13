<?php

declare(strict_types=1);

namespace Drupal\ps_context\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_context\ContextSeedRules;

/**
 * Delete form for context rules with seed-rule warnings.
 */
final class PsContextRuleDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\ps_context\Entity\PsContextRuleInterface $entity */
    $entity = $this->entity;
    if (ContextSeedRules::isSeed($entity->id())) {
      $this->messenger()->addWarning($this->t(
        'You are deleting seed rule %label. Validation, search filters, and offer display derive behavior from active matrix rules. Removing this rule may cause inconsistencies across the site.',
        ['%label' => $entity->label()],
      ));
    }

    return $form;
  }

}
