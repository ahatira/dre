<?php

declare(strict_types=1);

namespace Drupal\ps_email\Hook;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\ps_email\Processor\EmailLogoAttachmentProcessor;
use Drupal\ps_email\Processor\EmailPlainTextFromHtmlProcessor;
use Drupal\ps_email\Service\EmailMjmlPreviewVariablesBuilder;
use Drupal\symfony_mailer\EmailInterface;

/**
 * Central mail hooks for Property Search.
 */
final class MailHooks {

  public function __construct(
    private readonly EmailLogoAttachmentProcessor $emailLogoAttachmentProcessor,
    private readonly EmailPlainTextFromHtmlProcessor $emailPlainTextFromHtmlProcessor,
    private readonly EmailMjmlPreviewVariablesBuilder $emailMjmlPreviewVariablesBuilder,
  ) {}

  /**
   * Ensures HTML mail bodies are not escaped by Symfony Mailer.
   */
  #[Hook('mail_alter')]
  public function mailAlter(array &$message): void {
    $contentType = $message['headers']['Content-Type'] ?? '';
    if (!is_string($contentType) || stripos($contentType, 'text/html') === FALSE) {
      return;
    }

    if (!isset($message['body']) || !is_array($message['body'])) {
      return;
    }

    foreach ($message['body'] as $index => $part) {
      if ($part instanceof MarkupInterface || !is_string($part)) {
        continue;
      }
      $message['body'][$index] = Markup::create($part);
    }
  }

  /**
   * Registers email processors on every Symfony Mailer email.
   */
  #[Hook('mailer_init')]
  public function mailerInit(EmailInterface $email): void {
    $email->addProcessor($this->emailLogoAttachmentProcessor);
    $email->addProcessor($this->emailPlainTextFromHtmlProcessor);
  }

  /**
   * Adds site-wide variables to the generic email wrapper.
   */
  #[Hook('preprocess_email_wrap')]
  public function preprocessEmailWrap(array &$variables): void {
    $langcode = NULL;
    if (isset($variables['language']) && is_object($variables['language']) && method_exists($variables['language'], 'getId')) {
      $langcode = $variables['language']->getId();
    }
    $this->emailMjmlPreviewVariablesBuilder->applySharedShellVariables($variables, $langcode);
  }

  /**
   * Adds design tokens and sample branding for MJML shell preview (devel).
   */
  #[Hook('preprocess_email_shell_preview')]
  public function preprocessEmailShellPreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-shell-preview');
  }

  /**
   * Adds sample data for MJML partials showcase (devel).
   */
  #[Hook('preprocess_email_partials_preview')]
  public function preprocessEmailPartialsPreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-partials-preview');
  }

  /**
   * Adds design tokens and branding for contact MJML preview (devel).
   */
  #[Hook('preprocess_email_contact_preview')]
  public function preprocessEmailContactPreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-contact-preview');
  }

  /**
   * Adds design tokens and branding for compare MJML preview (devel).
   */
  #[Hook('preprocess_email_compare_preview')]
  public function preprocessEmailComparePreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-compare-preview');
  }

  /**
   * Adds design tokens and branding for search alert MJML preview (devel).
   */
  #[Hook('preprocess_email_search_alert_preview')]
  public function preprocessEmailSearchAlertPreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-search-alert-preview');
  }

  /**
   * Adds design tokens and branding for import alert MJML preview (devel).
   */
  #[Hook('preprocess_email_import_alert_preview')]
  public function preprocessEmailImportAlertPreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-import-alert-preview');
  }

  /**
   * Adds design tokens and sample offer cards for MJML preview (devel).
   */
  #[Hook('preprocess_email_offer_cards_preview')]
  public function preprocessEmailOfferCardsPreview(array &$variables): void {
    $variables += $this->emailMjmlPreviewVariablesBuilder->buildForTemplate('email-offer-cards-preview');
  }

}
