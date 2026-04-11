<?php

declare(strict_types=1);

namespace Drupal\ps_division\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a confirmation form for deleting a Division entity.
 */
final class DivisionDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): string {
    /** @var \Drupal\ps_division\Entity\DivisionInterface $entity */
    $entity = $this->getEntity();
    return $this->t('Are you sure you want to delete the division %name?', [
      '%name' => $entity->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return new Url('ps_division.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText(): string {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    /** @var \Drupal\ps_division\Entity\DivisionInterface $entity */
    $entity = $this->getEntity();
    $entity->delete();

    $this->messenger()->addStatus($this->t('Division %label has been deleted.', [
      '%label' => $entity->label(),
    ]));

    $this->logger('ps_division')->info('Deleted division: %label', [
      '%label' => $entity->label(),
    ]);

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
