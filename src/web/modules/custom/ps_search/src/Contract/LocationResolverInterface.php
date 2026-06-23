<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * Resolves free-text location queries and GeoZone identifiers to GeoContext.
 */
interface LocationResolverInterface {

  /**
   * Resolves a user query (suggest, apply, API) to a GeoContext.
   */
  public function resolveQuery(
    string $query,
    string $countryCode,
    string $langcode,
  ): LocationResolveResult;

  /**
   * Resolves a GeoZone id or slug deterministically (SEO-safe).
   */
  public function resolveGeoZone(string $zoneIdOrSlug, string $countryCode): ?GeoContext;

}
