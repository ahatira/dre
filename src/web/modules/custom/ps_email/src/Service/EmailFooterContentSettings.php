<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Reads ps_email.footer config (legal WYSIWYG + corporate textarea).
 */
final class EmailFooterContentSettings {

  private const CONFIG_NAME = 'ps_email.footer';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Returns processed HTML for the GDPR legal field.
   */
  public function getProcessedLegalHtml(?string $langcode = NULL): string {
    $body = $this->getTextFormat('legal', $langcode);
    $value = trim($body['value'] ?? '');
    $format = (string) ($body['format'] ?? 'email_html');
    if ($value === '') {
      return '';
    }

    return (string) check_markup($value, $format);
  }

  /**
   * Returns the corporate identifiers plain text for the active content language.
   */
  public function getCorporateLine(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getDefaultLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    $override = $this->languageManager->getLanguageConfigOverride($langcode, self::CONFIG_NAME);
    $value = $override->get('corporate');
    if (!is_string($value) || trim($value) === '') {
      $value = $this->configFactory->get(self::CONFIG_NAME)->get('corporate');
    }

    return is_string($value) ? trim($value) : '';
  }

  /**
   * @return array{value: string, format: string}
   *   Raw text format field value.
   */
  public function getTextFormat(string $field, ?string $langcode = NULL): array {
    $langcode ??= $this->languageManager->getDefaultLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    $override = $this->languageManager->getLanguageConfigOverride($langcode, self::CONFIG_NAME);
    $value = $override->get($field);
    if (!is_array($value) || trim((string) ($value['value'] ?? '')) === '') {
      $value = $this->configFactory->get(self::CONFIG_NAME)->get($field);
    }

    if (!is_array($value)) {
      return ['value' => '', 'format' => 'email_html'];
    }

    return [
      'value' => (string) ($value['value'] ?? ''),
      'format' => (string) ($value['format'] ?? 'email_html'),
    ];
  }

}
