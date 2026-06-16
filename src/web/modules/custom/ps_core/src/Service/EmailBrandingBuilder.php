<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Language\LanguageManagerInterface;

/**
 * Builds email header branding assets from ps_theme.
 */
final class EmailBrandingBuilder {

  /**
   * Same asset as block--system-branding-block (162×31).
   */
  private const HEADER_LOGO_RELATIVE = '/assets/images/logo/header-logo.svg';

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
   * Returns the site-relative public path to the header logo.
   */
  public function getHeaderLogoRelativePath(): ?string {
    $themePath = $this->themeExtensionList->getPath('ps_theme');
    if ($themePath === '') {
      return NULL;
    }

    $relativePath = $themePath . self::HEADER_LOGO_RELATIVE;
    return is_readable(DRUPAL_ROOT . '/' . $relativePath) ? $relativePath : NULL;
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
   * Returns the absolute filesystem path to the header logo SVG.
   */
  public function getHeaderLogoPath(): ?string {
    $relativePath = $this->getHeaderLogoRelativePath();
    if ($relativePath === NULL) {
      return NULL;
    }

    return DRUPAL_ROOT . '/' . $relativePath;
  }

}
