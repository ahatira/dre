<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\FileInterface;
use Drupal\ps_form\Form\Helper\ManagedFileFormValueHelper;
use Drupal\webform\WebformInterface;

/**
 * Reads confirmation email hero settings from webform third-party settings.
 */
final class ContactWebformEmailHeroSettings {

  public const THIRD_PARTY_MODULE = 'ps_form';

  public const SETTING_FID = 'email_hero_fid';

  public const SETTING_STYLE = 'email_hero_image_style';

  /**
   * Default image style for email hero banners (2.35:1 focal crop).
   */
  public const DEFAULT_HERO_IMAGE_STYLE = 'ps_form_email_hero';

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Returns the configured hero file id for a hub webform.
   */
  public function getHeroFileId(string $webformId): ?int {
    $webform = $this->loadWebform($webformId);
    if ($webform === NULL) {
      return NULL;
    }

    $fid = (int) $webform->getThirdPartySetting(self::THIRD_PARTY_MODULE, self::SETTING_FID, 0);
    if ($fid <= 0) {
      $heroFile = $webform->getThirdPartySetting(self::THIRD_PARTY_MODULE, 'hero_file');
      if (is_array($heroFile)) {
        $fid = ManagedFileFormValueHelper::extractManagedFileFid($heroFile['upload'] ?? NULL);
      }
    }

    return $fid > 0 ? $fid : NULL;
  }

  /**
   * Returns the image style id used for a webform hero in emails.
   */
  public function getHeroImageStyleId(string $webformId): string {
    $webform = $this->loadWebform($webformId);
    if ($webform === NULL) {
      return self::DEFAULT_HERO_IMAGE_STYLE;
    }

    $styleId = trim((string) $webform->getThirdPartySetting(self::THIRD_PARTY_MODULE, self::SETTING_STYLE, ''));
    if ($styleId !== '' && $this->entityTypeManager->getStorage('image_style')->load($styleId) !== NULL) {
      return $styleId;
    }

    return self::DEFAULT_HERO_IMAGE_STYLE;
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

  /**
   * Loads a webform config entity by id.
   */
  private function loadWebform(string $webformId): ?WebformInterface {
    $webform = $this->entityTypeManager->getStorage('webform')->load($webformId);
    return $webform instanceof WebformInterface ? $webform : NULL;
  }

}
