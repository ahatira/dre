<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\Plugin\media\Source\Image;

/**
 * Generic gallery media source wrapper.
 */
#[MediaSource(
  id: 'bnp_gallery',
  label: new TranslatableMarkup('BNP Gallery'),
  description: new TranslatableMarkup('Use a local image collection for reusable galleries.'),
  allowed_field_types: ['image'],
  default_thumbnail_filename: 'no-thumbnail.png',
  thumbnail_alt_metadata_attribute: 'thumbnail_alt_value',
)]
final class BnpMediaGallery extends Image {

}
