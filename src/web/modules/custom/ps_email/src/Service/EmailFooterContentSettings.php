<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Reads the classic email footer WYSIWYG config (ps_email.footer).
 */
final class EmailFooterContentSettings {

  private const CONFIG_NAME = 'ps_email.footer';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Returns processed HTML for a footer text field.
   */
  public function getProcessedHtml(string $field, ?string $langcode = NULL): string {
    $body = $this->getTextFormat($field, $langcode);
    $value = trim($body['value'] ?? '');
    $format = (string) ($body['format'] ?? 'email_html');
    if ($value === '') {
      return '';
    }

    return (string) check_markup($value, $format);
  }

  /**
   * Returns processed footer zone HTML keyed by column.
   *
   * @return array{left: string, right: string, legal: string}
   *   Processed footer column HTML per zone.
   */
  public function getProcessedZones(?string $langcode = NULL): array {
    return [
      'left' => $this->getProcessedHtml('footer_left', $langcode),
      'right' => $this->getProcessedHtml('footer_right', $langcode),
      'legal' => $this->getProcessedHtml('legal', $langcode),
    ];
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
