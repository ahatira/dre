<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Drupal\ps_form\Service\ContactEmailHeroImageResolver;
use Drupal\webform\WebformInterface;

/**
 * Extends ps_email MJML preview variables for contact hub templates.
 */
final class EmailMjmlPreviewHooks {

  public function __construct(
    private readonly ContactEmailHeroImageResolver $heroImageResolver,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Adds contact hub confirmation sample data to MJML preview.
   */
  #[Hook('ps_email_mjml_preview_variables_alter')]
  public function mjmlPreviewVariablesAlter(array &$variables, string $templateName, ?string $langcode): void {
    if ($templateName !== 'email-contact-preview') {
      return;
    }

    $variables['ps_contact_confirmation'] = TRUE;
    $variables['body'] ??= Markup::create(
      '<h1 style="margin:0 0 24px;font-size:22px;font-weight:700;line-height:1.3;color:#333333;text-align:center;">Your request has been sent</h1>'
      . '<p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#333333;">Hello Jane,</p>'
      . '<p style="margin:0 0 16px;font-size:14px;line-height:1.6;color:#333333;">Your request has been successfully submitted.</p>',
    );

    $webformId = ContactWebformEmailSettings::HUB_WEBFORM_IDS[0];
    $heroUrl = $this->heroImageResolver->getHeroImageUrl($webformId);
    if ($heroUrl !== NULL) {
      $variables['ps_contact_hero_url'] = $heroUrl;
      $variables['ps_contact_hero_alt'] = $this->loadWebformLabel($webformId);
    }
  }

  /**
   * Loads a webform label for hero alt text.
   */
  private function loadWebformLabel(string $webformId): string {
    $webform = $this->entityTypeManager->getStorage('webform')->load($webformId);
    return $webform instanceof WebformInterface ? (string) $webform->label() : $webformId;
  }

}
