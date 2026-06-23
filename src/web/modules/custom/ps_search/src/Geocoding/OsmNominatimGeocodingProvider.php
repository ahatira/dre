<?php

declare(strict_types=1);

namespace Drupal\ps_search\Geocoding;

use Drupal\ps_search\Contract\GeocodingProviderInterface;
use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * OpenStreetMap Nominatim geocoding provider (L3 fallback).
 *
 * Stub until HTTP integration is configured in BO.
 */
final class OsmNominatimGeocodingProvider implements GeocodingProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return 'osm';
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
