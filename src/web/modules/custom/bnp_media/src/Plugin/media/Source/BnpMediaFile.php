<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\Plugin\media\Source\File;

/**
 * Generic file media source wrapper.
 */
#[MediaSource(
  id: 'bnp_file',
  label: new TranslatableMarkup('BNP File'),
  description: new TranslatableMarkup('Use local files for reusable media.'),
  allowed_field_types: ['file'],
  default_thumbnail_filename: 'bnp_file_generic.svg',
)]
final class BnpMediaFile extends File {

}
