<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigInstallerInterface;
use Drupal\Core\Extension\ExtensionList;

/**
 * Syncs email design tokens from Stellar (framework_css_re) SCSS variables.
 */
final class EmailDesignTokenSync {

  private const CONFIG_NAME = 'ps_email.email_tokens';

  private const SCSS_RELATIVE = 'themes/custom/ui_suite_bnp/work/styles/scss/framework/re/abstracts/_variables.scss';

  /**
   * Maps Stellar CSS custom properties to ps_email.email_tokens config keys.
   *
   * @var array<string, string>
   */
  private const STELLAR_MAP = [
    'primary_color' => '--re-color-primary',
    'primary_hover_color' => '--re-color-primary-hover',
    'primary_strong_color' => '--re-color-primary-strong',
    'text_color' => '--re-color-text-strong',
    'muted_color' => '--re-color-text-muted',
    'background_color' => '--re-color-surface-muted',
    'surface_color' => '--re-color-surface',
    'border_color' => '--re-color-border-soft',
    'footer_dark_color' => '--re-color-heading',
    'font_family' => '--re-font-family-body',
    'heading_font_family' => '--re-font-family-heading',
    'font_size_base' => '--re-font-size-14',
    'line_height_base' => '--re-line-height-body',
  ];

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly ExtensionList $themeExtensionList,
  ) {}

  /**
   * Returns install defaults merged with parsed Stellar tokens.
   *
   * @return array<string, mixed>
   *   Token config values.
   */
  public function getMergedDefaults(): array {
    $defaults = $this->getInstallDefaults();
    $stellar = $this->parseStellarTokens();

    foreach (self::STELLAR_MAP as $configKey => $cssVar) {
      if (isset($stellar[$cssVar])) {
        $defaults[$configKey] = $stellar[$cssVar];
      }
    }

    if (!isset($defaults['exclusive_badge_color'])) {
      $defaults['exclusive_badge_color'] = '#C5A26D';
    }
    if (!isset($defaults['button_radius'])) {
      $defaults['button_radius'] = '0';
    }
    if (!isset($defaults['header_divider_color'])) {
      $defaults['header_divider_color'] = '#cccccc';
    }

    return $defaults;
  }

  /**
   * Persists Stellar-derived tokens into ps_email.email_tokens config.
   */
  public function syncToConfig(bool $overwriteExisting = FALSE): void {
    $merged = $this->getMergedDefaults();
    $editable = $this->configFactory->getEditable(self::CONFIG_NAME);

    foreach ($merged as $key => $value) {
      if ($overwriteExisting || $editable->get($key) === NULL) {
        $editable->set($key, $value);
      }
    }

    $editable->save();
  }

  /**
   * Parses CSS custom properties from the Stellar variables SCSS file.
   *
   * @return array<string, string>
   *   CSS variable name => value.
   */
  public function parseStellarTokens(): array {
    $path = $this->resolveScssPath();
    if ($path === NULL || !is_readable($path)) {
      return [];
    }

    $contents = file_get_contents($path);
    if ($contents === FALSE) {
      return [];
    }

    $tokens = [];
    if (preg_match_all('/(--re-[a-z0-9-]+)\s*:\s*([^;]+);/i', $contents, $matches, PREG_SET_ORDER)) {
      foreach ($matches as $match) {
        $tokens[$match[1]] = trim($match[2], " \t\n\r\0\x0B'\"");
      }
    }

    return $tokens;
  }

  /**
   * @return array<string, mixed>
   */
  private function getInstallDefaults(): array {
    $path = DRUPAL_ROOT . '/modules/custom/ps_email/config/install/' . self::CONFIG_NAME . '.yml';
    if (!is_readable($path)) {
      return [];
    }

    $parsed = \Drupal\Component\Serialization\Yaml::decode((string) file_get_contents($path));
    return is_array($parsed) ? $parsed : [];
  }

  private function resolveScssPath(): ?string {
    $themePath = $this->themeExtensionList->getPath('ui_suite_bnp');
    if ($themePath === '') {
      return NULL;
    }

    $relative = $themePath . '/' . self::SCSS_RELATIVE;
    $full = DRUPAL_ROOT . '/' . $relative;

    return is_readable($full) ? $full : NULL;
  }

}
