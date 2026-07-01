<?php

declare(strict_types=1);

namespace Drupal\ps_offer_webform\Hook;

use Drupal\Core\DependencyInjection\DependencySerializationTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\NodeInterface;
use Drupal\ps_offer_webform\Service\OfferContactSnapshotBuilder;
use Drupal\ps_offer_webform\Service\OfferContactSnapshotFields;
use Drupal\webform\WebformSubmissionForm;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Captures offer snapshot hidden fields on offer webform submissions.
 */
final class OfferContactSnapshotWebformHooks {

  use DependencySerializationTrait;
  use StringTranslationTrait;

  public function __construct(
    private readonly OfferContactSnapshotBuilder $snapshotBuilder,
  ) {}

  /**
   * Pre-fills offer snapshot hidden fields from the source offer node.
   */
  #[Hook('form_webform_submission_offer_contact_form_alter')]
  #[Hook('form_webform_submission_offer_contact_add_form_alter')]
  #[Hook('form_webform_submission_schedule_visit_form_alter')]
  #[Hook('form_webform_submission_schedule_visit_add_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    $this->applySnapshotDefaults($form, $form_state);
    if (!$this->hasSnapshotValidator($form)) {
      $form['#validate'][] = [$this, 'validateSnapshotSource'];
    }
  }

  /**
   * Locks offer snapshot values at submission time.
   */
  #[Hook('webform_submission_presave')]
  public function presave(WebformSubmissionInterface $webform_submission): void {
    if (!$this->isOfferWebform($webform_submission->getWebform()->id())) {
      return;
    }

    $source = $webform_submission->getSourceEntity();
    if (!$source instanceof NodeInterface || $source->bundle() !== 'offer') {
      return;
    }

    $langcode = $webform_submission->language()->getId();
    $snapshot = $this->snapshotBuilder->buildFromNode($source, $langcode);
    if ($snapshot === []) {
      return;
    }

    $data = $webform_submission->getData();
    foreach ($snapshot as $key => $value) {
      $data[$key] = $value;
    }
    $webform_submission->setData($data);
  }

  /**
   * Ensures the submission is linked to an offer before saving.
   */
  public function validateSnapshotSource(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $submission = $form_object->getEntity();
    $source = $submission->getSourceEntity();
    if (!$source instanceof NodeInterface || $source->bundle() !== 'offer') {
      $form_state->setErrorByName('', $this->t('This form must be submitted from an offer page.'));
      return;
    }

    $langcode = $submission->language()->getId();
    $snapshot = $this->snapshotBuilder->buildFromNode($source, $langcode);
    if (!OfferContactSnapshotFields::isComplete($snapshot)) {
      $form_state->setErrorByName('', $this->t('Offer details could not be captured. Please reload the page and try again.'));
    }
  }

  /**
   * @param array<string, mixed> $form
   */
  private function applySnapshotDefaults(array &$form, FormStateInterface $form_state): void {
    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $source = $form_object->getEntity()->getSourceEntity();
    if (!$source instanceof NodeInterface || $source->bundle() !== 'offer') {
      return;
    }

    if (!isset($form['elements']) || !is_array($form['elements'])) {
      return;
    }

    $langcode = $form_object->getEntity()->language()->getId();
    $snapshot = $this->snapshotBuilder->buildFromNode($source, $langcode);
    foreach ($snapshot as $key => $value) {
      if (!isset($form['elements'][$key]) || !is_array($form['elements'][$key])) {
        continue;
      }
      $form['elements'][$key]['#default_value'] = $value;
    }
  }

  private function isOfferWebform(string $webformId): bool {
    return in_array($webformId, OfferContactSnapshotFields::WEBFORM_IDS, TRUE);
  }

  /**
   * @param array<string, mixed> $form
   */
  private function hasSnapshotValidator(array $form): bool {
    foreach ($form['#validate'] ?? [] as $validator) {
      if (is_array($validator) && ($validator[1] ?? NULL) === 'validateSnapshotSource') {
        return TRUE;
      }
    }

    return FALSE;
  }

}
