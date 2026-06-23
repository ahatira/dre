<?php

declare(strict_types=1);

namespace Drupal\ps_search\Geocoding;

use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\GeocodingProviderInterface;
use Drupal\ps_search\Search\Context\GeoContextFactory;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * Resolves locations from indexed offer address data (centroids, localities).
 */
final class OfferDerivedGeocodingProvider implements GeocodingProviderInterface {

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return 'offers';
  }

  /**
   * {@inheritdoc}
   */
  public function resolve(
    string $query,
    string $countryCode,
    string $langcode,
  ): LocationResolveResult {
    $query = trim($query);
    if ($query === '') {
      return new LocationResolveResult(NULL, FALSE, []);
    }

    $meta = $this->locationSearchFilter->resolveTokenMetadata($query);
    if (($meta['type'] ?? '') === 'department') {
      $meta['department_code'] = $meta['department_code'] ?? $query;
    }

    $geo = GeoContextFactory::fromOfferMetadata($meta, $countryCode, $this->geoZoneRepository);
    if ($geo === NULL) {
      return new LocationResolveResult(NULL, FALSE, []);
    }

    return new LocationResolveResult($geo, FALSE, []);
  }

}
