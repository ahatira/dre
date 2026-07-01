<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_offer_webform\Service\ScheduleVisitAvailabilities;

/**
 * Schedule visit webform enhancements (availability calendar).
 */
final class ScheduleVisitWebformHooks {

  use StringTranslationTrait;

  /**
   * Attaches availability calendar assets and validation.
   */
  #[Hook('form_webform_submission_schedule_visit_form_alter')]
  #[Hook('form_webform_submission_schedule_visit_add_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    $form['#attached']['library'][] = 'ps_offer_webform/schedule_visit_availabilities';

    if (!$this->hasAvailabilityValidator($form)) {
      $form['#validate'][] = [$this, 'validateAvailabilities'];
    }
  }

  /**
   * Ensures availability dates are valid before wizard advance or submit.
   */
  public function validateAvailabilities(array &$form, FormStateInterface $form_state): void {
    $key = ScheduleVisitAvailabilities::ELEMENT_KEY;
    $value = $form_state->getValue($key);
    if (!is_string($value)) {
      $form_state->setErrorByName($key, $this->t('Please select at least one availability date.'));
      return;
    }

    try {
      ScheduleVisitAvailabilities::assertValid($value);
    }
    catch (\InvalidArgumentException $exception) {
      $form_state->setErrorByName($key, $this->t('@message', ['@message' => $exception->getMessage()]));
    }
  }

  /**
   * Checks whether the availability validator is already registered.
   *
   * @param array<string, mixed> $form
   *   The form array.
   *
   * @return bool
   *   TRUE when the validator is present.
   */
  private function hasAvailabilityValidator(array $form): bool {
    foreach ($form['#validate'] ?? [] as $validator) {
      if (is_array($validator) && ($validator[1] ?? NULL) === 'validateAvailabilities') {
        return TRUE;
      }
    }

    return FALSE;
  }

}
