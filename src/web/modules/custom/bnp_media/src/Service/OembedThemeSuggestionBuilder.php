<?php

declare(strict_types=1);

namespace Drupal\bnp_media\Service;

/**
 * Builds oEmbed iframe theme suggestions.
 */
final class OembedThemeSuggestionBuilder {

  /**
   * Returns provider-specific suggestions for remote video iframe rendering.
   *
   * @return string[]
   *   A list of extra theme suggestions.
   */
  public function buildSuggestions(?string $provider): array {
    $provider = strtolower(trim((string) $provider));
    if ($provider === '') {
      return [];
    }

    return [
      'media_oembed_iframe__remote_video__' . $provider,
    ];
  }

}
