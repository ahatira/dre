<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Reads exportable design tokens for PS email templates (Twig and MJML).
 */
final class EmailDesignTokens {

  private const CONFIG_NAME = 'ps_email.email_tokens';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns all tokens as preprocess variables for email templates.
   *
   * @return array<string, mixed>
   *   Token variables (email_* keys).
   */
  public function getPreprocessVariables(): array {
    $config = $this->configFactory->get(self::CONFIG_NAME);

    return [
      'email_primary_color' => (string) $config->get('primary_color'),
      'email_text_color' => (string) $config->get('text_color'),
      'email_muted_color' => (string) $config->get('muted_color'),
      'email_background_color' => (string) $config->get('background_color'),
      'email_surface_color' => (string) $config->get('surface_color'),
      'email_footer_dark_color' => (string) $config->get('footer_dark_color'),
      'email_font_family' => (string) $config->get('font_family'),
      'email_font_size_base' => (string) $config->get('font_size_base'),
      'email_line_height_base' => (string) $config->get('line_height_base'),
      'email_spacing_unit' => (int) $config->get('spacing_unit'),
      'email_max_width' => (int) $config->get('max_width'),
      'email_logo_width' => (int) $config->get('logo_width'),
      'email_logo_height' => (int) $config->get('logo_height'),
    ];
  }

  /**
   * Returns the primary brand color for emails.
   */
  public function getPrimaryColor(): string {
    return (string) $this->configFactory->get(self::CONFIG_NAME)->get('primary_color');
  }

}
