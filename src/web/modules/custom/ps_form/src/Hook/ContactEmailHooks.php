<?php

declare(strict_types=1);

namespace Drupal\ps_form\Hook;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_email\Service\ContactWebformEmailSettings;
use Drupal\ps_form\Service\ContactEmailHeroImageResolver;
use Drupal\webform\WebformInterface;

/**
 * Contact webform email wrapper preprocessing (hero, confirmation shell flags).
 */
final class ContactEmailHooks {

  /**
   * Webform email handler suffixes wired to Symfony Mailer sub_type.
   *
   * @var list<string>
   */
  private const WEBFORM_HANDLER_SUFFIXES = [
    'email_confirmation',
    'email_notification',
    'email_agent',
  ];

  public function __construct(
    private readonly ContactWebformEmailSettings $contactWebformEmailSettings,
    private readonly ContactEmailHeroImageResolver $heroImageResolver,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Applies hub confirmation shell flags (hero, in-body H1) for webform emails.
   */
  #[Hook('preprocess_email_wrap')]
  public function preprocessEmailWrap(array &$variables): void {
    if (($variables['type'] ?? '') !== 'webform') {
      return;
    }

    $parsed = self::parseWebformHandlerSubType((string) ($variables['sub_type'] ?? ''));
    if ($parsed === NULL) {
      return;
    }

    ['webform_id' => $webformId, 'handler_id' => $handlerId] = $parsed;
    if (!$this->contactWebformEmailSettings->isContactWebform($webformId)) {
      return;
    }

    if ($handlerId === 'email_confirmation' && $this->contactWebformEmailSettings->isHubConfirmationWebform($webformId)) {
      $variables['ps_contact_confirmation'] = TRUE;

      $heroUrl = $this->heroImageResolver->getHeroImageUrl($webformId);
      if ($heroUrl !== NULL) {
        $variables['ps_contact_hero_url'] = $heroUrl;
        $variables['ps_contact_hero_alt'] = $this->loadWebformLabel($webformId);
      }
    }
  }

  /**
   * Parses webform id and handler id from symfony_mailer sub_type.
   *
   * @return array{webform_id: string, handler_id: string}|null
   *   Parsed ids or NULL when sub_type is not a webform handler.
   */
  public static function parseWebformHandlerSubType(string $subType): ?array {
    foreach (self::WEBFORM_HANDLER_SUFFIXES as $handlerId) {
      $suffix = '_' . $handlerId;
      if (!str_ends_with($subType, $suffix)) {
        continue;
      }

      $webformId = substr($subType, 0, -strlen($suffix));
      if ($webformId === '') {
        return NULL;
      }

      return [
        'webform_id' => $webformId,
        'handler_id' => $handlerId,
      ];
    }

    return NULL;
  }

  /**
   * Parses webform id from symfony_mailer sub_type for confirmation emails.
   */
  public static function parseConfirmationWebformId(string $subType): ?string {
    $parsed = self::parseWebformHandlerSubType($subType);
    if ($parsed === NULL || $parsed['handler_id'] !== 'email_confirmation') {
      return NULL;
    }

    return $parsed['webform_id'];
  }

  /**
   * Loads a webform label for hero alt text.
   */
  private function loadWebformLabel(string $webformId): string {
    $webform = $this->entityTypeManager->getStorage('webform')->load($webformId);
    return $webform instanceof WebformInterface ? (string) $webform->label() : $webformId;
  }

}
