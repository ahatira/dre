<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Service;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Url;

/**
 * Builds oEmbed iframe route URLs with stable query parameters.
 */
final class OembedIframeBuilder {

  /**
   * Builds an oEmbed iframe URL for a remote video source.
   */
  public function buildIframeUrl(string $source_url, ?string $provider, int $max_width, int $max_height): string {
    $query = [
      'url' => $source_url,
      'max_width' => $max_width,
      'max_height' => $max_height,
      'type' => 'remote_video',
      'provider' => $provider ?? '',
      'hash' => Crypt::hashBase64($source_url . '|' . (string) $provider),
    ];

    return Url::fromRoute('media.oembed_iframe', [], ['query' => $query])->toString();
  }

}
