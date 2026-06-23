<?php

declare(strict_types=1);

namespace Drupal\ps_search\Geocoding;

use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\GeocodingProviderInterface;
use Drupal\ps_search\Search\Context\GeoContextFactory;
use Drupal\ps_search\ValueObject\LocationResolveResult;

/**
 * Matches queries against the GeoZone referential (departments, regions, slugs).
 */
final class GeoZoneGeocodingProvider implements GeocodingProviderInterface {

  public function __construct(
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function id(): string {
    return 'geo_zone';
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

    $countryCode = strtolower($countryCode);
    $slugCandidate = $this->normalizeSlug($query);
    $zone = $this->geoZoneRepository->findBySlug($slugCandidate, $countryCode);
    if ($zone !== NULL) {
      return new LocationResolveResult(
        GeoContextFactory::fromGeoZone($zone, 'geo_zone'),
        FALSE,
        [],
      );
    }

    if (preg_match('/^\d{2,3}$/', $query) === 1) {
      $zone = $this->geoZoneRepository->findByPostalPrefix($query, $countryCode);
      if ($zone !== NULL) {
        return new LocationResolveResult(
          GeoContextFactory::fromGeoZone($zone, 'geo_zone'),
          FALSE,
          [],
        );
      }
    }

    $labelMatches = $this->matchByLabel($query, $countryCode);
    if (count($labelMatches) === 1) {
      return new LocationResolveResult(
        GeoContextFactory::fromGeoZone($labelMatches[0], 'geo_zone'),
        FALSE,
        [],
      );
    }
    if (count($labelMatches) > 1) {
      $candidates = array_map(
        static fn ($zone) => GeoContextFactory::fromGeoZone($zone, 'geo_zone'),
        $labelMatches,
      );

      return new LocationResolveResult(NULL, TRUE, $candidates);
    }

    return new LocationResolveResult(NULL, FALSE, []);
  }

  /**
   * @return list<GeoZone>
   */
  private function matchByLabel(string $query, string $countryCode): array {
    $needle = mb_strtolower(trim($query));
    if ($needle === '') {
      return [];
    }

    $matches = [];
    foreach ($this->geoZoneRepository->allForCountry($countryCode) as $candidate) {
      $label = mb_strtolower($candidate->label);
      if ($label === $needle || str_contains($label, $needle)) {
        $matches[] = $candidate;
      }
    }

    return $matches;
  }

  private function normalizeSlug(string $value): string {
    $slug = mb_strtolower(trim($value));
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? $slug;

    return trim($slug, '-');
  }

}
