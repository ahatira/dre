<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Plugin\media\Source;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\media\Attribute\OEmbedMediaSource;
use Drupal\media\Plugin\media\Source\OEmbed;

/**
 * Provides a generic remote video source with a strict provider whitelist.
 */
#[OEmbedMediaSource(
  id: 'bnp_remote_video',
  label: new TranslatableMarkup('BNP Remote video'),
  description: new TranslatableMarkup('Use remote video URLs for reusable media.'),
  allowed_field_types: ['string'],
  default_thumbnail_filename: 'bnp_remote_video.svg',
  providers: ['YouTube', 'Vimeo', 'MediaHub'],
)]
final class BnpMediaRemoteVideo extends OEmbed {

}
