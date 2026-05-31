<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\MediaSource;
use Drupal\media\Plugin\media\Source\AudioFile;

/**
 * Generic audio media source wrapper.
 */
#[MediaSource(
  id: 'bnp_audio_file',
  label: new TranslatableMarkup('BNP Audio file'),
  description: new TranslatableMarkup('Use audio files for reusable media.'),
  allowed_field_types: ['file'],
  default_thumbnail_filename: 'bnp_audio_file.svg',
)]
final class BnpMediaAudioFile extends AudioFile {

}
