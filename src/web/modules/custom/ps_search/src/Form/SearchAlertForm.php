<?php

declare(strict_types=1);

namespace Drupal\ps_search\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Admin form for Search alert entities.
 */
final class SearchAlertForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    $status = parent::save($form, $form_state);
    $this->messenger()->addStatus($this->t('Search alert saved.'));
    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
    return $status;
  }

}
