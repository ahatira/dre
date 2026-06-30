<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Builds the HTML body fragment for hub contact confirmation emails.
 */
final class ContactEmailConfirmationBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly ContactWebformEmailSettings $contactWebformEmailSettings,
    private readonly ContactEmailSubmissionRecapFormatter $recapFormatter,
    private readonly RendererInterface $renderer,
  ) {}

  /**
   * Builds a render array for the confirmation body fragment.
   *
   * @return array<string, mixed>
   *   Render array themed with ps_contact_email_confirmation_body.
   */
  public function buildRenderArray(WebformSubmissionInterface $submission): array {
    $webformId = $submission->getWebform()->id();
    if (!$this->contactWebformEmailSettings->isHubConfirmationWebform($webformId)) {
      return [];
    }

    $firstname = trim((string) $submission->getElementData('firstname'));
    $greeting = $firstname !== ''
      ? (string) $this->t('@prefix @name,', [
        '@prefix' => $this->contactWebformEmailSettings->getGreetingPrefix($webformId),
        '@name' => $firstname,
      ])
      : (string) $this->t('@prefix,', ['@prefix' => $this->contactWebformEmailSettings->getGreetingPrefix($webformId)]);

    return [
      '#theme' => 'ps_contact_email_confirmation_body',
      '#greeting' => $greeting,
      '#intro_text' => $this->contactWebformEmailSettings->getText($webformId, 'intro_text') ?: (string) $this->t('Your request has been successfully submitted. We will get back to you as soon as possible.'),
      '#recap_intro' => $this->contactWebformEmailSettings->getText($webformId, 'recap_intro') ?: (string) $this->t('For reference, your request is as follows:'),
      '#recap_rows' => $this->recapFormatter->buildRows($submission),
      '#closing_text' => $this->contactWebformEmailSettings->getText($webformId, 'closing_text') ?: (string) $this->t('Thank you for your interest in BNP Paribas Real Estate.'),
      '#signoff_text' => $this->contactWebformEmailSettings->getText($webformId, 'signoff_text') ?: (string) $this->t('Best regards, Your Customer Service team, BNP Paribas Real Estate.'),
    ];
  }

  /**
   * Renders the confirmation body HTML fragment for token replacement.
   */
  public function buildHtml(WebformSubmissionInterface $submission): MarkupInterface|string {
    $build = $this->buildRenderArray($submission);
    if ($build === []) {
      return '';
    }
    return $this->renderer->renderPlain($build);
  }

}
