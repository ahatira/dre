<?php

declare(strict_types=1);

namespace Drupal\ps_context\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ps_context\LabelProfileKeys;

/**
 * Delete form for label profiles with seed-profile warnings.
 */
final class PsContextLabelProfileDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildForm($form, $form_state);

    /** @var \Drupal\ps_context\Entity\PsContextLabelProfileInterface $entity */
    $entity = $this->entity;
    if (LabelProfileKeys::isSeed($entity->id())) {
      $this->messenger()->addWarning($this->t(
        'You are deleting seed profile %label. Search filters and hero wording derive labels from active profiles.',
        ['%label' => $entity->label()],
      ));
    }

    return $form;
  }

}
