<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\Markup;
use Drupal\ps_form\Service\ContactEmailConfirmationBuilder;
use Drupal\ps_form\Service\ContactEmailFooterBuilder;
use Drupal\ps_form\Service\ContactEmailSettings;
use Drupal\webform\WebformSubmissionInterface;

/**
 * Contact confirmation email tokens, theme, and wrapper preprocessing.
 */
final class ContactEmailHooks {

  public function __construct(
    private readonly ContactEmailSettings $emailSettings,
    private readonly ContactEmailConfirmationBuilder $confirmationBuilder,
    private readonly ContactEmailFooterBuilder $footerBuilder,
  ) {}

  /**
   * Registers the confirmation body theme and variables.
   */
  #[Hook('theme')]
  public function theme(): array {
    return [
      'ps_contact_email_confirmation_body' => [
        'variables' => [
          'greeting' => '',
          'intro_text' => '',
          'recap_intro' => '',
          'recap_rows' => [],
          'closing_text' => '',
          'signoff_text' => '',
        ],
        'template' => 'ps-contact-email-confirmation-body',
      ],
    ];
  }

  /**
   * Registers ps_form email tokens for webform handlers.
   */
  #[Hook('token_info')]
  public function tokenInfo(): array {
    return [
      'types' => [
        'ps_form' => [
          'name' => 'PS Form',
          'description' => 'Property Search form email tokens.',
          'needs-data' => 'webform_submission',
        ],
      ],
      'tokens' => [
        'ps_form' => [
          'contact_confirmation_body' => [
            'name' => 'Contact confirmation body',
            'description' => 'Styled HTML body for hub contact confirmation emails.',
          ],
        ],
      ],
    ];
  }

  /**
   * Replaces ps_form email tokens.
   */
  #[Hook('tokens')]
  public function tokens(string $type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata): array {
    if ($type !== 'ps_form') {
      return [];
    }

    $replacements = [];
    $submission = $data['webform_submission'] ?? NULL;
    if (!$submission instanceof WebformSubmissionInterface) {
      return [];
    }

    foreach ($tokens as $name => $original) {
      if ($name !== 'contact_confirmation_body') {
        continue;
      }
      $html = $this->confirmationBuilder->buildHtml($submission);
      $replacements[$original] = $html instanceof MarkupInterface ? $html : Markup::create((string) $html);
    }

    return $replacements;
  }

  /**
   * Adds hero, display title, and rich footer to contact confirmation wraps.
   */
  #[Hook('preprocess_email_wrap')]
  public function preprocessEmailWrap(array &$variables): void {
    if (($variables['type'] ?? '') !== 'webform') {
      return;
    }

    $subType = (string) ($variables['sub_type'] ?? '');
    if (!str_ends_with($subType, '_email_confirmation')) {
      return;
    }

    $webformId = substr($subType, 0, -strlen('_email_confirmation'));
    if (!$this->emailSettings->isHubConfirmationWebform($webformId)) {
      return;
    }

    $variables['ps_contact_confirmation'] = TRUE;
    $variables['email_display_title'] = $this->emailSettings->getDisplayTitle();
    $variables['email_hide_subject_title'] = TRUE;
    $variables['email_hide_default_signoff'] = TRUE;

    $heroUrl = $this->confirmationBuilder->getHeroImageUrl($webformId);
    if ($heroUrl !== NULL) {
      $variables['ps_contact_hero_url'] = $heroUrl;
      $variables['ps_contact_hero_alt'] = $this->emailSettings->getDisplayTitle();
    }

    $variables += $this->footerBuilder->buildFooterVariables();
  }

  /**
   * Parses webform id from symfony_mailer sub_type for confirmation emails.
   */
  public static function parseConfirmationWebformId(string $subType): ?string {
    if (!str_ends_with($subType, '_email_confirmation')) {
      return NULL;
    }
    $webformId = substr($subType, 0, -strlen('_email_confirmation'));
    return $webformId !== '' ? $webformId : NULL;
  }

}
