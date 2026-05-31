<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Service;

/**
 * Attaches provider-specific oEmbed iframe libraries.
 */
final class ProviderLibraryAttacher {

  /**
   * Attaches the provider-specific JS library when known.
   */
  public function attach(array &$variables, ?string $provider): void {
    $provider = strtolower((string) $provider);

    $library = match ($provider) {
      'youtube' => 'bnp_media/oembed_frame_youtube',
      'vimeo' => 'bnp_media/oembed_frame_vimeo',
      'mediahub' => 'bnp_media/oembed_frame_mediahub',
      default => NULL,
    };

    if ($library !== NULL) {
      $variables['#attached']['library'][] = $library;
    }
  }

}
