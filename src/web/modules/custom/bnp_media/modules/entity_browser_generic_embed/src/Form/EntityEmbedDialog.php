<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Generic embed dialog form placeholder.
 */
final class EntityEmbedDialog extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'entity_browser_generic_embed_dialog';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['message'] = [
      '#markup' => $this->t('Entity browser generic embed dialog placeholder.'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
  }

}
