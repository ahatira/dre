<?php

declare(strict_types=1);

namespace Drupal\ps_email\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Builds email header branding assets from ps_theme.
 */
final class EmailBrandingBuilder {

  /**
   * PNG logo for CID embedding (Outlook/Gmail).
   *
   * Place manually at ps_theme/assets/images/logo/header-logo-email.png.
   */
  private const HEADER_LOGO_PNG_RELATIVE = '/assets/images/logo/header-logo-email.png';

  /**
   * Fallback SVG — same asset as block--system-branding-block (162×31).
   */
  private const HEADER_LOGO_SVG_RELATIVE = '/assets/images/logo/header-logo.svg';

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly ExtensionList $themeExtensionList,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
  ) {}

  /**
   * Returns the site slogan configured in system.site.
   */
  public function getSiteSlogan(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage()->getId();
    $slogan = $this->languageManager->getLanguageConfigOverride($langcode, 'system.site')->get('slogan');
    if (!is_string($slogan) || $slogan === '') {
      $slogan = $this->configFactory->get('system.site')->get('slogan');
    }
    return is_string($slogan) ? trim($slogan) : '';
  }

  /**
   * Returns the site-relative public path to the header logo (PNG preferred).
   */
  public function getHeaderLogoRelativePath(): ?string {
    $themePath = $this->themeExtensionList->getPath('ps_theme');
    if ($themePath === '') {
      return NULL;
    }

    foreach ([self::HEADER_LOGO_PNG_RELATIVE, self::HEADER_LOGO_SVG_RELATIVE] as $relative) {
      $relativePath = $themePath . $relative;
      if (is_readable(DRUPAL_ROOT . '/' . $relativePath)) {
        return $relativePath;
      }
    }

    return NULL;
  }

  /**
   * Returns the MIME type of the resolved header logo file.
   */
  public function getHeaderLogoMimeType(): ?string {
    $path = $this->getHeaderLogoPath();
    if ($path === NULL) {
      return NULL;
    }

    return str_ends_with(strtolower($path), '.png') ? 'image/png' : 'image/svg+xml';
  }

  /**
   * Returns the attachment filename for the header logo.
   */
  public function getHeaderLogoFilename(): ?string {
    $path = $this->getHeaderLogoPath();
    if ($path === NULL) {
      return NULL;
    }

    return basename($path);
  }

  /**
   * Returns the absolute public URL of the header logo for CID embedding.
   */
  public function getHeaderLogoUrl(): ?string {
    $relativePath = $this->getHeaderLogoRelativePath();
    if ($relativePath === NULL) {
      return NULL;
    }

    return $this->fileUrlGenerator->generateAbsoluteString($relativePath);
  }

  /**
   * Returns the absolute filesystem path to the header logo.
   */
  public function getHeaderLogoPath(): ?string {
    $relativePath = $this->getHeaderLogoRelativePath();
    if ($relativePath === NULL) {
      return NULL;
    }

    return DRUPAL_ROOT . '/' . $relativePath;
  }

}
