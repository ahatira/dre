<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * Geocoding provider plugin contract (geo_zone, offers, google, osm).
 */
interface GeocodingProviderInterface {

  /**
   * Provider machine id (matches engine_settings.geocoding_providers[].id).
   */
  public function id(): string;

  /**
   * Attempts to resolve a location query for the given country.
   */
  public function resolve(
    string $query,
    string $countryCode,
    string $langcode,
  ): LocationResolveResult;

}
