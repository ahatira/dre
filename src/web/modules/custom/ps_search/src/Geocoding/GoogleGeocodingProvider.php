<?php

declare(strict_types=1);

namespace Drupal\ps_search\Geocoding;

use Drupal\ps_search\Contract\GeocodingProviderInterface;
use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * Google Geocoding API provider (L3 — external, suggest/resolve only).
 *
 * Stub until API credentials and HTTP client integration are configured.
 */
final class GoogleGeocodingProvider implements GeocodingProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return 'google';
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(
    string $query,
    string $countryCode,
    string $langcode,
  ): LocationResolveResult {
    return new LocationResolveResult(NULL, FALSE, []);
  }

}
