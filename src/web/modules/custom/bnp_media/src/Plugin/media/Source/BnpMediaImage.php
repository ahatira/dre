<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\Plugin\media\Source\Image;

/**
 * Generic image media source wrapper.
 */
#[MediaSource(
  id: 'bnp_image',
  label: new TranslatableMarkup('BNP Image'),
  description: new TranslatableMarkup('A locally hosted image file.'),
  allowed_field_types: ['image'],
  default_thumbnail_filename: 'no-thumbnail.png',
  thumbnail_alt_metadata_attribute: 'thumbnail_alt_value',
)]
final class BnpMediaImage extends Image {

}
