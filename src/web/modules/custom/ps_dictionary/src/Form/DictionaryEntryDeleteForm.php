<?php

declare(strict_types=1);

namespace Drupal\ps_dictionary\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

final class DictionaryEntryDeleteForm extends EntityDeleteForm {

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $entity = $this->getEntity();
    $label = $entity->label();
    $type = method_exists($entity, 'getType') ? $entity->getType() : NULL;

    $entity->delete();

    $this->messenger()->addStatus($this->t('The dictionary entry %label has been deleted.', ['%label' => $label]));

    if (is_string($type) && $type !== '') {
      $form_state->setRedirect('ps_dictionary.entry_collection', ['ps_dictionary_type' => $type]);
      return;
    }

    $form_state->setRedirect('ps_dictionary.type_collection');
  }

}
