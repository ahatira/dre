<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Reads global email shell settings (footer, legal) from ps_email.shell.
 */
final class EmailShellSettings {

  private const CONFIG_NAME = 'ps_email.shell';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Returns whether site footer blocks should be reused when fields are empty.
   */
  public function reuseSiteFooter(): bool {
    return (bool) $this->configFactory->get(self::CONFIG_NAME)->get('reuse_site_footer');
  }

  /**
   * Returns a translatable shell string for the current content language.
   */
  public function getText(string $key, ?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $value = $this->languageManager
      ->getLanguageConfigOverride($langcode, self::CONFIG_NAME)
      ->get($key);
    if (!is_string($value) || trim($value) === '') {
      $value = $this->configFactory->get(self::CONFIG_NAME)->get($key);
    }
    return is_string($value) ? trim($value) : '';
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

}
