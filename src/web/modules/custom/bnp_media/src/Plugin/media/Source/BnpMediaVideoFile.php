<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\Plugin\media\Source\VideoFile;

/**
 * Generic video media source wrapper.
 */
#[MediaSource(
  id: 'bnp_video_file',
  label: new TranslatableMarkup('BNP Video file'),
  description: new TranslatableMarkup('Use video files for reusable media.'),
  allowed_field_types: ['file'],
  default_thumbnail_filename: 'bnp_video_file.svg',
)]
final class BnpMediaVideoFile extends VideoFile {

}
