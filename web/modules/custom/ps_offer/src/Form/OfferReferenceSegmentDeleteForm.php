<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Delete form for Offer Reference Segment config entities.
 */
final class OfferReferenceSegmentDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): string {
    return (string) $this->t('Are you sure you want to delete segment @label?', [
      '@label' => $this->entity->label(),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl(): Url {
    return Url::fromRoute('entity.ps_offer_reference_segment.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $label = $this->entity->label();
    $this->entity->delete();

    $this->messenger()->addStatus($this->t('Deleted segment %label.', [
      '%label' => $label,
    ]));

    $form_state->setRedirect('entity.ps_offer_reference_segment.collection');
  }

}
