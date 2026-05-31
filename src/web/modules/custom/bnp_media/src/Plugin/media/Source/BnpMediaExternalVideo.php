<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\MediaInterface;
use Drupal\media\MediaSourceBase;

/**
 * Provides a direct external video URL source (non-oEmbed).
 */
#[MediaSource(
  id: 'bnp_external_video',
  label: new TranslatableMarkup('BNP External video URL'),
  description: new TranslatableMarkup('Use direct external video URLs (for example MediaHub AssetLink).'),
  allowed_field_types: ['string'],
  default_thumbnail_filename: 'bnp_mediahub_video.svg',
)]
final class BnpMediaExternalVideo extends MediaSourceBase {

  /**
   * {@inheritdoc}
   */
  public function getMetadataAttributes(): array {
    return [
      'default_name' => $this->t('Default name'),
      'source_url' => $this->t('Source URL'),
      'thumbnail_uri' => $this->t('Thumbnail local URI'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getMetadata(MediaInterface $media, $attribute_name) {
    $source_url = (string) ($this->getSourceFieldValue($media) ?? '');

    switch ($attribute_name) {
      case 'default_name':
        if ($source_url !== '') {
          $host = (string) parse_url($source_url, PHP_URL_HOST);
          $path = trim((string) parse_url($source_url, PHP_URL_PATH), '/');
          $suffix = $path !== '' ? $path : substr(hash('sha1', $source_url), 0, 8);
          return trim($host . ' ' . $suffix);
        }
        return parent::getMetadata($media, 'default_name');

      case 'source_url':
        return $source_url;

      default:
        return parent::getMetadata($media, $attribute_name);
    }
  }

}
