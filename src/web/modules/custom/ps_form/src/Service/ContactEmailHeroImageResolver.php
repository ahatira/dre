<?php

declare(strict_types=1);

namespace Drupal\ps_form\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Image\ImageFactory;
use Drupal\crop\Entity\Crop;
use Drupal\file\FileInterface;
use Drupal\focal_point\FocalPointManagerInterface;
use Drupal\image\Entity\ImageStyle;

/**
 * Builds styled hero derivatives and persists focal point crops for emails.
 */
final class ContactEmailHeroImageResolver {

  /**
   * Cinema banner ratio used by the default hero image style (width / height).
   */
  public const ASPECT_RATIO = 2.35;

  public function __construct(
    private readonly FileUrlGeneratorInterface $fileUrlGenerator,
    private readonly FileSystemInterface $fileSystem,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly FocalPointManagerInterface $focalPointManager,
    private readonly ImageFactory $imageFactory,
    private readonly ContactWebformEmailHeroSettings $heroSettings,
  ) {}

  /**
   * Returns the absolute URL of the styled hero derivative for email output.
   */
  public function getStyledAbsoluteUrl(FileInterface $file, string $webformId): string {
    $style = ImageStyle::load($this->heroSettings->getHeroImageStyleId($webformId));
    if ($style === NULL) {
      return $this->fileUrlGenerator->generateAbsoluteString($file->getFileUri());
    }

    $sourceUri = $file->getFileUri();
    $derivativeUri = $style->buildUri($sourceUri);
    if (!file_exists($derivativeUri)) {
      $style->createDerivative($sourceUri, $derivativeUri);
    }

    return $this->fileUrlGenerator->generateAbsoluteString($derivativeUri);
  }

  /**
   * Saves focal point crop data for a hero file.
   */
  public function saveFocalPoint(FileInterface $file, string $focalPoint, string $webformId): void {
    if (!$this->focalPointManager->validateFocalPoint($focalPoint)) {
      return;
    }

    $image = $this->imageFactory->get($file->getFileUri());
    if (!$image->isValid()) {
      return;
    }

    [$x, $y] = array_map('floatval', explode(',', $focalPoint));
    $cropType = (string) $this->configFactory->get('focal_point.settings')->get('crop_type');
    $crop = $this->focalPointManager->getCropEntity($file, $cropType);
    $this->focalPointManager->saveCropEntity($x, $y, $image->getWidth(), $image->getHeight(), $crop);
    $this->flushStyledDerivative($file, $webformId);
  }

  /**
   * Returns the focal point string (x,y percents) for a hero file.
   */
  public function getFocalPointValue(FileInterface $file): string {
    $cropType = (string) $this->configFactory->get('focal_point.settings')->get('crop_type');
    $crop = Crop::findCrop($file->getFileUri(), $cropType);
    if ($crop !== NULL) {
      $image = $this->imageFactory->get($file->getFileUri());
      if ($image->isValid()) {
        $anchor = $this->focalPointManager->absoluteToRelative(
          (int) $crop->x->value,
          (int) $crop->y->value,
          $image->getWidth(),
          $image->getHeight(),
        );
        return $anchor['x'] . ',' . $anchor['y'];
      }
    }

    $default = $this->configFactory->get('focal_point.settings')->get('default_value');
    return is_string($default) && $default !== '' ? $default : '50,50';
  }

  /**
   * Deletes the styled derivative so the next render picks up crop changes.
   */
  public function flushStyledDerivative(FileInterface $file, string $webformId): void {
    $style = ImageStyle::load($this->heroSettings->getHeroImageStyleId($webformId));
    if ($style === NULL) {
      return;
    }

    $derivativeUri = $style->buildUri($file->getFileUri());
    if (is_string($derivativeUri) && file_exists($derivativeUri)) {
      $this->fileSystem->delete($derivativeUri);
    }
  }

}
