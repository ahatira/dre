<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\file\FileInterface;

/**
 * Reads contact confirmation email settings from ps_form.settings.
 */
final class ContactEmailSettings {

  private const CONFIG_KEY = 'contact_email_confirmation';

  /**
   * Hub webform ids eligible for styled confirmation emails.
   *
   * @var list<string>
   */
  public const HUB_WEBFORM_IDS = ContactNeedRouter::DEFAULT_HUB_ENABLED_WEBFORM_IDS;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Returns whether a webform uses the styled confirmation email.
   */
  public function isHubConfirmationWebform(string $webformId): bool {
    return in_array($webformId, self::HUB_WEBFORM_IDS, TRUE);
  }

  /**
   * Returns a translatable email string for the current content language.
   */
  public function getText(string $key, ?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $value = $this->languageManager
      ->getLanguageConfigOverride($langcode, 'ps_form.settings')
      ->get(self::CONFIG_KEY . '.' . $key);
    if (!is_string($value) || trim($value) === '') {
      $value = $this->configFactory->get('ps_form.settings')->get(self::CONFIG_KEY . '.' . $key);
    }
    return is_string($value) ? trim($value) : '';
  }

  /**
   * Returns the display title shown in the email body (distinct from subject).
   */
  public function getDisplayTitle(?string $langcode = NULL): string {
    $title = $this->getText('display_title', $langcode);
    return $title !== '' ? $title : 'Your request has been sent';
  }

  /**
   * Returns the greeting prefix before the submitter first name.
   */
  public function getGreetingPrefix(?string $langcode = NULL): string {
    $prefix = $this->getText('greeting_prefix', $langcode);
    return $prefix !== '' ? $prefix : 'Hello';
  }

  /**
   * Returns the hero file id configured for a hub webform.
   */
  public function getHeroFileId(string $webformId): ?int {
    $heroes = $this->configFactory->get('ps_form.settings')->get(self::CONFIG_KEY . '.webform_heroes');
    if (!is_array($heroes) || !isset($heroes[$webformId])) {
      return NULL;
    }
    $fid = (int) $heroes[$webformId];
    return $fid > 0 ? $fid : NULL;
  }

  /**
   * Returns whether site footer blocks should be reused.
   */
  public function reuseSiteFooter(): bool {
    return (bool) $this->configFactory->get('ps_form.settings')->get(self::CONFIG_KEY . '.reuse_site_footer');
  }

  /**
   * Returns a scalar footer setting with optional language override.
   */
  public function getFooterScalar(string $key, ?string $langcode = NULL): string {
    return $this->getText('footer_' . $key, $langcode);
  }

  /**
   * Returns legal markup HTML for the email footer.
   */
  public function getLegalMarkup(?string $langcode = NULL): string {
    return $this->getText('legal_markup', $langcode);
  }

  /**
   * Loads the hero file entity for a webform, if configured and permanent.
   */
  public function loadHeroFile(string $webformId): ?FileInterface {
    $fid = $this->getHeroFileId($webformId);
    if ($fid === NULL) {
      return NULL;
    }
    $file = $this->entityTypeManager->getStorage('file')->load($fid);
    if (!$file instanceof FileInterface || !$file->isPermanent()) {
      return NULL;
    }
    return $file;
  }

}
