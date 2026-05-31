<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Generic media helper form placeholder.
 */
final class MediaForm extends FormBase {

  use BulkCreationEntityFormTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'entity_browser_generic_embed_media_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['input'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Bulk input'),
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Process'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->normalizeRows((string) $form_state->getValue('input'));
  }

}
