<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnppre\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Hooks for Content Moderation support.
 */
class ContentModeration {

  /**
   * Moderation state size so that it is not too wide.
   */
  public const int SIZE = 15;

  /**
   * Implements hook_form_FORM_ID_alter().
   *
   * Ensure the moderation state is properly aligned.
   */
  #[Hook('form_content_moderation_entity_moderation_form_alter')]
  public function alter(array &$form, FormStateInterface $formState, string $form_id): void {
    if (!isset($form['current']['#markup'], $form['current']['#title'])) {
      return;
    }

    $form['current'] = [
      '#type' => 'textfield',
      '#title' => $form['current']['#title'],
      '#attributes' => [
        'class' => [
          'form-control-plaintext',
        ],
        'readonly' => TRUE,
      ],
      '#value' => $form['current']['#markup'],
      '#size' => static::SIZE,
    ];
  }

}
