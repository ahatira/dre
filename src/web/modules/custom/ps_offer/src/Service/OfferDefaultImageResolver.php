<?php

declare(strict_types=1);

namespace Drupal\ps_offer\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ThemeExtensionList;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\file\FileInterface;
use Drupal\image\Entity\ImageStyle;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Resolves the site-wide default offer image configured in the back office.
 */
final class OfferDefaultImageResolver {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    private readonly ThemeExtensionList $themeExtensionList,
    private readonly RequestStack $requestStack,
    TranslationInterface $stringTranslation,
  ) {
    $this->stringTranslation = $stringTranslation;
  }

  /**
   * Returns the configured default image file URI, if any.
   */
  public function getFileUri(): ?string {
    $file = $this->loadConfiguredFile();
    if ($file === NULL) {
      return NULL;
    }

    return $file->getFileUri();
  }

  /**
   * Builds a styled URL for the configured default image.
   *
   * Falls back to the theme SVG placeholder when no image is configured.
   */
  public function buildUrl(?string $imageStyle = NULL, bool $relative = TRUE): string {
    $uri = $this->getFileUri();
    if ($uri === NULL) {
      return $this->themePlaceholderUrl($relative);
    }

    if ($imageStyle !== NULL && $imageStyle !== '') {
      $style = ImageStyle::load($imageStyle);
      if ($style !== NULL) {
        $url = $style->buildUrl($uri);
        return $relative ? $this->fileUrlGenerator->transformRelative($url) : $url;
      }
    }

    return $relative
      ? $this->fileUrlGenerator->generateString($uri)
      : $this->fileUrlGenerator->generateAbsoluteString($uri);
  }

  /**
   * Returns configured alt text or a generic fallback label.
   */
  public function getAlt(): string {
    $alt = trim((string) ($this->configFactory->get('ps_offer.settings')->get('default_image_alt') ?? ''));
    return $alt !== '' ? $alt : (string) $this->t('Offer image');
  }

  /**
   * Cache tags for the configured default image settings.
   *
   * @return list<string>
   *   Config cache tags.
   */
  public function getCacheTags(): array {
    return ['config:ps_offer.settings'];
  }

  /**
   * Whether a custom default image is configured in the back office.
   */
  public function hasConfiguredImage(): bool {
    return $this->getFileUri() !== NULL;
  }

  /**
   * Loads the configured default image file entity.
   */
  private function loadConfiguredFile(): ?FileInterface {
    $fid = (int) ($this->configFactory->get('ps_offer.settings')->get('default_image_fid') ?? 0);
    if ($fid <= 0) {
      return NULL;
    }

    $file = $this->entityTypeManager->getStorage('file')->load($fid);
    if (!$file instanceof FileInterface) {
      return NULL;
    }

    return $file->access('view') ? $file : NULL;
  }

  /**
   * Returns the theme SVG placeholder URL.
   */
  private function themePlaceholderUrl(bool $relative): string {
    $themePath = $this->themeExtensionList->getPath('ps_theme');
    $path = '/' . $themePath . '/assets/images/offer-placeholder.svg';

    if ($relative) {
      return $path;
    }

    $request = $this->requestStack->getCurrentRequest();
    $baseUrl = $request !== NULL ? $request->getSchemeAndHttpHost() : '';
    return $baseUrl . $path;
  }

}
