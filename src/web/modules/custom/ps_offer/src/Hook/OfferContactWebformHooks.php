<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Hook;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\NodeInterface;
use Drupal\webform\WebformSubmissionForm;

/**
 * Offer contact webform alterations.
 */
final class OfferContactWebformHooks {

  /**
   * Pre-fills the message with offer surface, reference and locality.
   */
  #[Hook('form_webform_submission_offer_contact_form_alter')]
  public function formAlter(array &$form, FormStateInterface $form_state): void {
    if (!isset($form['elements']['project']['message'])) {
      return;
    }

    $form_object = $form_state->getFormObject();
    if (!$form_object instanceof WebformSubmissionForm) {
      return;
    }

    $source = $form_object->getEntity()->getSourceEntity();
    if (!$source instanceof NodeInterface || $source->bundle() !== 'offer') {
      return;
    }

    $form['elements']['project']['message']['#default_value'] = $this->buildDefaultMessage($source);
  }

  /**
   * Builds the default contact message for an offer.
   */
  private function buildDefaultMessage(NodeInterface $offer): string {
    $reference = $offer->hasField('field_reference') && !$offer->get('field_reference')->isEmpty()
      ? trim((string) $offer->get('field_reference')->value)
      : '';

    $surface = $this->formatSurfaceTotal($offer);
    $locality = $this->formatLocality($offer);

    $parts = ['Hello, I would like more information about the offer'];
    if ($surface !== '') {
      $parts[] = 'of ' . $surface;
    }
    if ($reference !== '') {
      $parts[] = "with reference '{$reference}'";
    }
    if ($locality !== '') {
      $parts[] = 'in ' . $locality;
    }
    $parts[] = 'Thank you';

    return implode(' ', $parts) . '.';
  }

  /**
   * Returns the TOTAL surface label for an offer.
   */
  private function formatSurfaceTotal(NodeInterface $offer): string {
    if (!$offer->hasField('field_surfaces') || $offer->get('field_surfaces')->isEmpty()) {
      return '';
    }

    foreach ($offer->get('field_surfaces') as $item) {
      if ((string) ($item->qualification ?? '') !== 'TOTAL') {
        continue;
      }
      $value = $item->value ?? NULL;
      if ($value === NULL || (float) $value <= 0) {
        return '';
      }
      $unit = strtolower((string) ($item->unit_code ?? 'M2')) === 'ha' ? 'ha' : 'm²';
      return number_format((float) $value, 0, ',', ' ') . $unit;
    }

    return '';
  }

  /**
   * Returns the offer locality for the default message.
   */
  private function formatLocality(NodeInterface $offer): string {
    if (!$offer->hasField('field_address') || $offer->get('field_address')->isEmpty()) {
      return '';
    }

    $address = $offer->get('field_address')->first();
    if ($address === NULL) {
      return '';
    }

    return trim((string) ($address->locality ?? ''));
  }

}
