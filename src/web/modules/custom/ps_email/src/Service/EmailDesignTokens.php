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
    private readonly EmailDesignTokenSync $emailDesignTokenSync,
  ) {}

  /**
   * Returns all tokens as preprocess variables for email templates.
   *
   * @return array<string, mixed>
   *   Token variables (email_* keys).
   */
  public function getPreprocessVariables(): array {
    $config = $this->configFactory->get(self::CONFIG_NAME);
    if ($config->isNew()) {
      $defaults = $this->emailDesignTokenSync->getMergedDefaults();
    }
    else {
      $defaults = [];
    }

    $get = static function (string $key) use ($config, $defaults): string {
      $value = $config->get($key);
      if ($value === NULL && isset($defaults[$key])) {
        $value = $defaults[$key];
      }
      return (string) ($value ?? '');
    };

    $getInt = static function (string $key) use ($config, $defaults): int {
      $value = $config->get($key);
      if ($value === NULL && isset($defaults[$key])) {
        $value = $defaults[$key];
      }
      return (int) ($value ?? 0);
    };

    return [
      'email_primary_color' => $get('primary_color'),
      'email_primary_hover_color' => $get('primary_hover_color'),
      'email_primary_strong_color' => $get('primary_strong_color'),
      'email_secondary_color' => $get('secondary_color'),
      'email_text_color' => $get('text_color'),
      'email_muted_color' => $get('muted_color'),
      'email_background_color' => $get('background_color'),
      'email_surface_color' => $get('surface_color'),
      'email_border_color' => $get('border_color'),
      'email_footer_dark_color' => $get('footer_dark_color'),
      'email_exclusive_badge_color' => $get('exclusive_badge_color'),
      'email_header_divider_color' => $get('header_divider_color'),
      'email_font_family' => $get('font_family'),
      'email_heading_font_family' => $get('heading_font_family'),
      'email_font_size_base' => $get('font_size_base'),
      'email_line_height_base' => $get('line_height_base'),
      'email_button_radius' => $get('button_radius'),
      'email_spacing_unit' => $getInt('spacing_unit'),
      'email_max_width' => $getInt('max_width'),
      'email_logo_width' => $getInt('logo_width'),
      'email_logo_height' => $getInt('logo_height'),
    ];
  }

  /**
   * Returns the primary brand color for emails.
   */
  public function getPrimaryColor(): string {
    return (string) $this->configFactory->get(self::CONFIG_NAME)->get('primary_color');
  }

}
