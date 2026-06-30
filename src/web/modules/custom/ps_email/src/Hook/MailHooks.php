<?php

declare(strict_types=1);

namespace Drupal\ps_email\Hook;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\ps_email\Processor\EmailLogoAttachmentProcessor;
use Drupal\ps_email\Service\EmailBrandingBuilder;
use Drupal\ps_email\Service\EmailDesignTokens;
use Drupal\symfony_mailer\EmailInterface;

/**
 * Central mail hooks for Property Search.
 */
final class MailHooks {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EmailBrandingBuilder $emailBrandingBuilder,
    private readonly EmailDesignTokens $emailDesignTokens,
    private readonly EmailLogoAttachmentProcessor $emailLogoAttachmentProcessor,
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
   * Registers the header logo CID processor on every Symfony Mailer email.
   */
  #[Hook('mailer_init')]
  public function mailerInit(EmailInterface $email): void {
    $email->addProcessor($this->emailLogoAttachmentProcessor);
  }

  /**
   * Adds site-wide variables to the generic email wrapper.
   */
  #[Hook('preprocess_email_wrap')]
  public function preprocessEmailWrap(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
  }

  /**
   * Adds design tokens and sample branding for MJML shell preview (devel).
   */
  #[Hook('preprocess_email_shell_preview')]
  public function preprocessEmailShellPreview(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
    $variables += $this->buildShellPreviewDefaults();
  }

  /**
   * Adds sample data for MJML partials showcase (devel).
   */
  #[Hook('preprocess_email_partials_preview')]
  public function preprocessEmailPartialsPreview(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
    $variables += $this->buildShellPreviewDefaults();
    $variables += $this->buildPartialsPreviewSamples();
  }

  /**
   * Adds design tokens and branding for contact MJML preview (devel).
   */
  #[Hook('preprocess_email_contact_preview')]
  public function preprocessEmailContactPreview(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
  }

  /**
   * Adds design tokens and branding for compare MJML preview (devel).
   */
  #[Hook('preprocess_email_compare_preview')]
  public function preprocessEmailComparePreview(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
    $variables['subject'] ??= (string) $this->t('Property comparison');
    $variables['intro_message'] ??= (string) $this->t('Here is the property comparison you requested.');
  }

  /**
   * Adds design tokens and branding for search alert MJML preview (devel).
   */
  #[Hook('preprocess_email_search_alert_preview')]
  public function preprocessEmailSearchAlertPreview(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
    $variables['subject'] ??= (string) $this->t('3 new properties for your alert');
  }

  /**
   * Adds design tokens and branding for import alert MJML preview (devel).
   */
  #[Hook('preprocess_email_import_alert_preview')]
  public function preprocessEmailImportAlertPreview(array &$variables): void {
    $variables += $this->emailDesignTokens->getPreprocessVariables();
    $variables += $this->buildBrandingVariables();
    $variables['subject'] ??= (string) $this->t('CRM import failed: sample-file.xml');
  }

  /**
   * Default variables for MJML shell / partials preview templates.
   *
   * @return array<string, mixed>
   *   Preview defaults.
   */
  private function buildShellPreviewDefaults(): array {
    return [
      'subject' => (string) $this->t('Your request has been sent'),
      'body' => Markup::create('<p>' . (string) $this->t('Sample email body for MJML preview.') . '</p>'),
      'is_html' => TRUE,
      'email_display_title' => (string) $this->t('Your request has been sent'),
      'ps_contact_confirmation' => FALSE,
      'email_hide_default_signoff' => FALSE,
    ];
  }

  /**
   * Sample partials data for email-partials-preview (devel).
   *
   * @return array<string, mixed>
   *   Partial showcase variables.
   */
  private function buildPartialsPreviewSamples(): array {
    $siteUrl = Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString();

    return [
      'preview_subject' => (string) $this->t('Email partials preview'),
      'preview_alert_title' => (string) $this->t('Success'),
      'preview_alert_message' => Markup::create('<p>' . (string) $this->t('Your request has been registered successfully.') . '</p>'),
      'preview_card_title' => (string) $this->t('Property summary'),
      'preview_card_body' => Markup::create(
        '<p><strong>' . (string) $this->t('Office space — Paris 8') . '</strong></p>'
        . '<p>' . (string) $this->t('Surface: 450 m² · Rent: on request') . '</p>'
      ),
      'preview_card_footer_url' => $siteUrl,
      'preview_card_footer_label' => (string) $this->t('View on Property Search'),
      'preview_button_url' => $siteUrl . '/compare/share/example-token',
      'preview_button_label' => (string) $this->t('View comparison'),
      'preview_info_title' => (string) $this->t('Note'),
      'preview_info_message' => Markup::create('<p>' . (string) $this->t('Table partial uses the same columns/sections model as ps_compare.') . '</p>'),
      'preview_table_columns' => [
        ['header' => Markup::create('<strong>Offer A</strong>')],
        ['header' => Markup::create('<strong>Offer B</strong>')],
      ],
      'preview_table_sections' => [
        [
          'label' => (string) $this->t('Key figures'),
          'rows' => [
            [
              'label' => (string) $this->t('Surface'),
              'cells' => ['450 m²', '320 m²'],
            ],
            [
              'label' => (string) $this->t('City'),
              'cells' => ['Paris', 'Lyon'],
            ],
          ],
        ],
      ],
    ];
  }

  /**
   * Builds shared site branding variables for email templates.
   *
   * @return array<string, mixed>
   *   Branding variables.
   */
  private function buildBrandingVariables(): array {
    $siteConfig = $this->configFactory->get('system.site');
    $siteName = (string) ($siteConfig->get('name') ?? 'Property Search');

    return [
      'site_name' => $siteName,
      'site_slogan' => $this->emailBrandingBuilder->getSiteSlogan(),
      'site_url' => Url::fromRoute('<front>', [], ['absolute' => TRUE])->toString(),
      'email_team_name' => (string) $this->t('The @team team', ['@team' => $siteName]),
      'email_logo_url' => $this->emailBrandingBuilder->getHeaderLogoUrl(),
    ];
  }

}
