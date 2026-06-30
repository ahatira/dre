<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\node\NodeInterface;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Drupal\ps_email\Service\OfferEmailCardHtmlRenderer;
use Drupal\ps_theme\Utility\OfferEmailCardPropsBuilder;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Injects offer email cards into offer webform confirmation emails.
 */
final class OfferEmailHooks {

  public function __construct(
    private readonly ContactWebformEmailSettings $contactWebformEmailSettings,
    private readonly OfferEmailCardHtmlRenderer $offerEmailCardHtmlRenderer,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Appends a vertical offer card to visitor confirmation emails.
   */
  #[Hook('mail_alter')]
  public function mailAlter(array &$message): void {
    if (($message['module'] ?? '') !== 'webform') {
      return;
    }

    $key = (string) ($message['key'] ?? '');
    if (!str_ends_with($key, '_email_confirmation')) {
      return;
    }

    $webformId = substr($key, 0, -strlen('_email_confirmation'));
    if (!$this->contactWebformEmailSettings->isOfferWebform($webformId)) {
      return;
    }

    $submission = $message['params']['webform_submission'] ?? NULL;
    if (!$submission instanceof WebformSubmissionInterface) {
      return;
    }

    $source = $submission->getSourceEntity();
    if (!$source instanceof NodeInterface || $source->bundle() !== 'offer') {
      return;
    }

    $langcode = (string) ($message['langcode'] ?? $submission->language()->getId());
    $props = OfferEmailCardPropsBuilder::build($source, $langcode);
    $cardHtml = $this->offerEmailCardHtmlRenderer->renderVertical($props);
    if ($cardHtml === '') {
      return;
    }

    if (!isset($message['body'][0])) {
      $message['body'][0] = '';
    }

    $existing = $message['body'][0];
    $existingString = $existing instanceof Markup ? (string) $existing : (string) $existing;
    $message['body'][0] = Markup::create($existingString . $cardHtml);
  }

}
