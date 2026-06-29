<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Builds the HTML body fragment for hub contact confirmation emails.
 */
final class ContactEmailConfirmationBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly ContactEmailSettings $emailSettings,
    private readonly ContactEmailSubmissionRecapFormatter $recapFormatter,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
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
    if (!$this->emailSettings->isHubConfirmationWebform($webformId)) {
      return [];
    }

    $firstname = trim((string) $submission->getElementData('firstname'));
    $greeting = $firstname !== ''
      ? (string) $this->t('@prefix @name,', [
        '@prefix' => $this->emailSettings->getGreetingPrefix(),
        '@name' => $firstname,
      ])
      : (string) $this->t('@prefix,', ['@prefix' => $this->emailSettings->getGreetingPrefix()]);

    return [
      '#theme' => 'ps_contact_email_confirmation_body',
      '#greeting' => $greeting,
      '#intro_text' => $this->emailSettings->getText('intro_text') ?: (string) $this->t('Your request has been successfully submitted. We will get back to you as soon as possible.'),
      '#recap_intro' => $this->emailSettings->getText('recap_intro') ?: (string) $this->t('For reference, your request is as follows:'),
      '#recap_rows' => $this->recapFormatter->buildRows($submission),
      '#closing_text' => $this->emailSettings->getText('closing_text') ?: (string) $this->t('Thank you for your interest in BNP Paribas Real Estate.'),
      '#signoff_text' => $this->emailSettings->getText('signoff_text') ?: (string) $this->t('Best regards, Your Customer Service team, BNP Paribas Real Estate.'),
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

  /**
   * Returns the absolute hero image URL for a webform, if configured.
   */
  public function getHeroImageUrl(string $webformId): ?string {
    $file = $this->emailSettings->loadHeroFile($webformId);
    if ($file === NULL) {
      return NULL;
    }
    return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
  }

}
