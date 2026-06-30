<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Reads contact confirmation email copy per hub webform from ps_email.contact.
 */
final class ContactWebformEmailSettings {

  private const CONFIG_NAME = 'ps_email.contact';

  /**
   * Hub webform ids with styled confirmation emails.
   *
   * @var list<string>
   */
  public const HUB_WEBFORM_IDS = [
    'find_property',
    'entrust_search',
    'get_advice',
    'entrust_property',
    'invest_sell',
    'other_request',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Returns configured hub webform ids.
   *
   * @return list<string>
   *   Webform machine names.
   */
  public function getWebformIds(): array {
    $webforms = $this->configFactory->get(self::CONFIG_NAME)->get('webforms');
    if (!is_array($webforms) || $webforms === []) {
      return self::HUB_WEBFORM_IDS;
    }
    return array_values(array_intersect(array_keys($webforms), self::HUB_WEBFORM_IDS));
  }

  /**
   * Returns whether a webform uses the styled confirmation email.
   */
  public function isHubConfirmationWebform(string $webformId): bool {
    return in_array($webformId, self::HUB_WEBFORM_IDS, TRUE);
  }

  /**
   * Returns a translatable copy string for one webform.
   */
  public function getText(string $webformId, string $key, ?string $langcode = NULL): string {
    if (!$this->isHubConfirmationWebform($webformId)) {
      return '';
    }

    $path = 'webforms.' . $webformId . '.' . $key;
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $value = $this->languageManager
      ->getLanguageConfigOverride($langcode, self::CONFIG_NAME)
      ->get($path);
    if (!is_string($value) || trim($value) === '') {
      $value = $this->configFactory->get(self::CONFIG_NAME)->get($path);
    }
    return is_string($value) ? trim($value) : '';
  }

  /**
   * Returns the display title shown in the email body for one webform.
   */
  public function getDisplayTitle(string $webformId, ?string $langcode = NULL): string {
    $title = $this->getText($webformId, 'display_title', $langcode);
    return $title !== '' ? $title : 'Your request has been sent';
  }

  /**
   * Returns the greeting prefix before the submitter first name.
   */
  public function getGreetingPrefix(string $webformId, ?string $langcode = NULL): string {
    $prefix = $this->getText($webformId, 'greeting_prefix', $langcode);
    return $prefix !== '' ? $prefix : 'Hello';
  }

  /**
   * Returns the copy array for one webform (for config forms).
   *
   * @return array<string, mixed>
   *   Webform copy settings.
   */
  public function getWebformCopy(string $webformId): array {
    $webforms = $this->configFactory->get(self::CONFIG_NAME)->get('webforms');
    if (!is_array($webforms) || !isset($webforms[$webformId]) || !is_array($webforms[$webformId])) {
      return [];
    }
    return $webforms[$webformId];
  }

}
