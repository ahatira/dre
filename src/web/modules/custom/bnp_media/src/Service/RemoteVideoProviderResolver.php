<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Service;

/**
 * Resolves remote video provider IDs from source URLs.
 */
final class RemoteVideoProviderResolver {

  /**
   * Resolves a normalized provider ID from a remote video URL.
   */
  public function resolveFromUrl(string $url): ?string {
    $url = trim($url);
    if ($url === '') {
      return NULL;
    }

    $host = (string) parse_url($url, PHP_URL_HOST);
    $path = (string) parse_url($url, PHP_URL_PATH);
    $query = (string) parse_url($url, PHP_URL_QUERY);

    $haystack = strtolower($host . ' ' . $path . ' ' . $query . ' ' . $url);
    if ($haystack === '') {
      return NULL;
    }

    if (str_contains($haystack, 'youtu.be') || str_contains($haystack, 'youtube.com')) {
      return 'youtube';
    }

    if (str_contains($haystack, 'vimeo.com')) {
      return 'vimeo';
    }

    if (str_contains($haystack, 'mediahub')) {
      return 'mediahub';
    }

    return NULL;
  }

}
