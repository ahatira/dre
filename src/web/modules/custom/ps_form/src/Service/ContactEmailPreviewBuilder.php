<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Theme\ThemeInitializationInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\ps_form\ValueObject\ContactEmailPreviewMail;
use Drupal\webform\Entity\WebformSubmission;
use Drupal\webform\WebformInterface;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Builds a rendered confirmation email preview for admin.
 */
final class ContactEmailPreviewBuilder {

  /**
   * Sample submission payloads per hub webform (preview only).
   *
   * @var array<string, array<string, mixed>>
   */
  private const SAMPLE_DATA = [
    'find_property' => [
      'transaction_type' => 'LOC',
      'search_type' => 'BUR',
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'company_name' => 'Preview Company Ltd',
      'job_title' => 'Facilities Manager',
      'prof_phone' => '+33123456789',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview — find a property.',
    ],
    'entrust_search' => [
      'transaction_type' => 'LOC',
      'search_type' => 'BUR',
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'company_name' => 'Preview Company Ltd',
      'job_title' => 'Facilities Manager',
      'prof_phone' => '+33123456789',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview — entrust search.',
    ],
    'get_advice' => [
      'consulting_type' => ['strategy'],
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'company_name' => 'Preview Company Ltd',
      'job_title' => 'Facilities Manager',
      'prof_phone' => '+33123456789',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview — consulting.',
    ],
    'entrust_property' => [
      'tf_assetpostalcode' => '75002',
      'totale_surface' => '500',
      'transaction_type' => 'LOC',
      'search_type' => 'BUR',
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'company_name' => 'Preview Company Ltd',
      'job_title' => 'Facilities Manager',
      'prof_phone' => '+33123456789',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview — entrust property.',
    ],
    'invest_sell' => [
      'transaction_type' => 'VEN',
      'search_type' => 'BUR',
      'totale_surface' => '1200',
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'company_name' => 'Preview Company Ltd',
      'job_title' => 'Facilities Manager',
      'prof_phone' => '+33123456789',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview — invest / sell.',
    ],
    'other_request' => [
      'other_need' => ['services'],
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'company_name' => 'Preview Company Ltd',
      'prof_phone' => '+33123456789',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview — other request.',
    ],
  ];

  /**
   * Front theme that provides the full email-wrap template.
   */
  private const PREVIEW_THEME = 'ps_theme';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ContactEmailSettings $emailSettings,
    private readonly ContactEmailConfirmationBuilder $confirmationBuilder,
    private readonly RendererInterface $renderer,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly DateFormatterInterface $dateFormatter,
    private readonly TimeInterface $time,
    private readonly ThemeManagerInterface $themeManager,
    private readonly ThemeInitializationInterface $themeInitialization,
  ) {}

  /**
   * Returns hub webform ids available for preview.
   *
   * @return list<string>
   *   Webform ids.
   */
  public function getWebformIds(): array {
    return ContactEmailSettings::HUB_WEBFORM_IDS;
  }

  /**
   * Builds a render array for the full wrapped confirmation email preview.
   *
   * @return array<string, mixed>|null
   *   Render array, or NULL when the webform is missing.
   */
  public function buildPreviewRenderArray(string $webformId): ?array {
    if (!$this->emailSettings->isHubConfirmationWebform($webformId)) {
      return NULL;
    }

    $webform = $this->entityTypeManager->getStorage('webform')->load($webformId);
    if (!$webform instanceof WebformInterface) {
      return NULL;
    }

    $submission = $this->createSampleSubmission($webform, $webformId);
    $body = $this->confirmationBuilder->buildHtml($submission);
    $subject = (string) ($webform->getHandler('email_confirmation')?->getSetting('subject') ?? $this->emailSettings->getDisplayTitle());
    $tag = 'webform.' . $webformId . '_email_confirmation';

    return [
      '#theme' => 'email_wrap',
      '#email' => new ContactEmailPreviewMail($subject, $tag),
      '#body' => $body,
      '#is_html' => TRUE,
      '#cache' => [
        'max-age' => 0,
        'contexts' => ['languages:language_interface'],
      ],
    ];
  }

  /**
   * Returns preview metadata for the admin chrome (subject, display title).
   *
   * @return array{
   *   subject: string,
   *   display_title: string,
   *   from_name: string,
   *   from_email: string,
   *   to_name: string,
   *   to_email: string,
   *   sent_at: string,
   *   }|null
   *   Preview metadata, or NULL when the webform is unavailable.
   */
  public function getPreviewMetadata(string $webformId): ?array {
    if (!$this->emailSettings->isHubConfirmationWebform($webformId)) {
      return NULL;
    }

    $webform = $this->entityTypeManager->getStorage('webform')->load($webformId);
    if (!$webform instanceof WebformInterface) {
      return NULL;
    }

    $subject = (string) ($webform->getHandler('email_confirmation')?->getSetting('subject') ?? '');
    if ($subject === '') {
      $subject = $this->emailSettings->getDisplayTitle();
    }

    $sample = self::SAMPLE_DATA[$webformId] ?? [];
    $firstName = trim((string) ($sample['firstname'] ?? 'Alex'));
    $lastName = trim((string) ($sample['lastname'] ?? 'Preview'));
    $toEmail = trim((string) ($sample['prof_email_address'] ?? 'alex.preview@example.com'));
    $toName = trim($firstName . ' ' . $lastName);

    $siteConfig = $this->configFactory->get('system.site');
    $fromName = (string) ($siteConfig->get('name') ?? 'Property Search');
    $fromEmail = (string) ($siteConfig->get('mail') ?? 'noreply@example.com');

    return [
      'subject' => $subject,
      'display_title' => $this->emailSettings->getDisplayTitle(),
      'from_name' => $fromName,
      'from_email' => $fromEmail,
      'to_name' => $toName,
      'to_email' => $toEmail,
      'sent_at' => $this->dateFormatter->format($this->time->getRequestTime(), 'custom', 'D, j M Y, H:i'),
    ];
  }

  /**
   * Renders the isolated confirmation email HTML document for iframe preview.
   */
  public function renderPreviewHtml(string $webformId): ?string {
    $build = $this->buildPreviewRenderArray($webformId);
    if ($build === NULL) {
      return NULL;
    }

    return $this->renderWithFrontTheme($build);
  }

  /**
   * Renders using ps_theme so email-wrap.html.twig is used on admin routes.
   */
  private function renderWithFrontTheme(array $build): string {
    $activeTheme = $this->themeManager->getActiveTheme();
    $this->themeManager->setActiveTheme(
      $this->themeInitialization->getActiveThemeByName(self::PREVIEW_THEME),
    );

    try {
      return (string) $this->renderer->renderPlain($build);
    }
    finally {
      $this->themeManager->setActiveTheme($activeTheme);
    }
  }

  /**
   * Creates an unsaved sample submission for preview rendering.
   */
  private function createSampleSubmission(WebformInterface $webform, string $webformId): WebformSubmissionInterface {
    $data = self::SAMPLE_DATA[$webformId] ?? [
      'firstname' => 'Alex',
      'lastname' => 'Preview',
      'prof_email_address' => 'alex.preview@example.com',
      'qualification_comment' => 'Sample submission for email preview.',
    ];

    $submission = WebformSubmission::create([
      'webform_id' => $webform->id(),
      'data' => $data,
    ]);
    $submission->setWebform($webform);
    $submission->set('in_draft', FALSE);

    return $submission;
  }

}
