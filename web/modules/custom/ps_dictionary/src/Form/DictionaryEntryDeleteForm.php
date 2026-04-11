<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Delete form for Dictionary Entry.
 */
class DictionaryEntryDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): string {
    return (string) $this->t('Are you sure you want to delete entry @label?', [
      '@label' => $this->entity->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entity */
    $entity = $this->entity;
    return Url::fromRoute('entity.ps_dictionary_type.entries', [
      'ps_dictionary_type' => $entity->getType(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\ps_dictionary\Entity\DictionaryEntryInterface $entity */
    $entity = $this->entity;
    $type = $entity->getType();

    $entity->delete();
    $this->messenger()->addStatus($this->t('Deleted entry %label.', [
      '%label' => $entity->label(),
    ]));

    $form_state->setRedirect('entity.ps_dictionary_type.entries', [
      'ps_dictionary_type' => $type,
    ]);
  }

}
