<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Service;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Sends the comparison table by email.
 */
final class CompareEmailSender {

  use StringTranslationTrait;

  public function __construct(
    private readonly CompareEmailBuilder $emailBuilder,
    private readonly CompareManagerInterface $compareManager,
    private readonly MailManagerInterface $mailManager,
    private readonly LanguageManagerInterface $languageManager,
    private readonly RendererInterface $renderer,
    private readonly CsrfTokenGenerator $csrfToken,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Sends the comparison to the given email address (JSON API with CSRF).
   */
  public function send(string $recipient, string $csrfHeader, string $message = ''): bool {
    if (!$this->compareManager->canOpenComparisonPage()) {
      return FALSE;
    }

    if (!$this->csrfToken->validate($csrfHeader, 'ps_compare.email')) {
      return FALSE;
    }

    return $this->sendToRecipient($recipient, $message);
  }

  /**
   * Sends the comparison from a validated Form API submission.
   */
  public function sendFromForm(string $recipient, string $message = ''): bool {
    if (!$this->compareManager->canOpenComparisonPage()) {
      return FALSE;
    }

    return $this->sendToRecipient($recipient, $message);
  }

  /**
   * Validates recipient and dispatches comparison email.
   */
  private function sendToRecipient(string $recipient, string $message = ''): bool {
    $recipient = trim($recipient);
    if ($recipient === '' || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
      return FALSE;
    }

    $settings = $this->configFactory->get('ps_compare.settings');
    $shareEnabled = (bool) ($settings->get('display_share_button') ?? $settings->get('display_email_share') ?? TRUE);
    if (!$shareEnabled) {
      return FALSE;
    }

    $build = $this->emailBuilder->buildRenderArray($message !== '' ? $message : NULL);
    $body = (string) $this->renderer->renderPlain($build);
    if ($body === '') {
      return FALSE;
    }

    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $siteName = (string) ($this->configFactory->get('system.site')->get('name') ?? '');
    $subject = (string) $this->t('@site — Property comparison', [
      '@site' => $siteName !== '' ? $siteName : 'Property Search',
    ]);

    $result = $this->mailManager->mail(
      'ps_compare',
      'comparison',
      $recipient,
      $langcode,
      [
        'subject' => $subject,
        'body' => $body,
      ],
    );

    return !empty($result['result']);
  }

}
