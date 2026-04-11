<?php

declare(strict_types=1);

namespace Drupal\ps_division\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Division Type config entity create/edit forms.
 */
final class DivisionTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\ps_division\Entity\DivisionTypeInterface $type */
    $type = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $type->label(),
      '#description' => $this->t('The human-readable name of this division type.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#machine_name' => [
        'exists' => [$this, 'exist'],
        'source' => ['label'],
      ],
      '#disabled' => !$type->isNew(),
    ];

    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $type->getDescription(),
      '#description' => $this->t('A brief description of this division type.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state): int {
    /** @var \Drupal\ps_division\Entity\DivisionTypeInterface $type */
    $type = $this->entity;
    $status = $type->save();

    if ($status === SAVED_NEW) {
      $this->messenger()->addStatus($this->t('Division type %label created.', [
        '%label' => $type->label(),
      ]));
    }
    else {
      $this->messenger()->addStatus($this->t('Division type %label updated.', [
        '%label' => $type->label(),
      ]));
    }

    $form_state->setRedirectUrl($type->toUrl('collection'));

    return $status;
  }

  /**
   * Determines if the division type already exists.
   *
   * @param string $id
   *   The division type ID.
   *
   * @return bool
   *   TRUE if the division type exists, FALSE otherwise.
   */
  public function exist(string $id): bool {
    $entity = $this->entityTypeManager
      ->getStorage('division_type')
      ->getQuery()
      ->condition('id', $id)
      ->accessCheck(FALSE)
      ->execute();
    return (bool) $entity;
  }

}
