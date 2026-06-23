<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\ValueObject\GeoZone;

/**
 * Read-only access to geo zone referential data per country site.
 */
interface GeoZoneRepositoryInterface {

  public const REGION_TOKEN_PREFIX = 'region:';

  /**
   * Returns a zone by its stable identifier.
   */
  public function get(string $id): ?GeoZone;

  /**
   * Finds a zone by URL slug within a country.
   */
  public function findBySlug(string $slug, string $countryCode): ?GeoZone;

  /**
   * Finds the most specific zone matching a postal prefix.
   */
  public function findByPostalPrefix(string $prefix, string $countryCode): ?GeoZone;

  /**
   * Returns the configured default zone for a country.
   */
  public function getDefaultForCountry(string $countryCode): ?GeoZone;

  /**
   * Returns direct child zones of a parent zone id.
   *
   * @return list<\Drupal\ps_search\ValueObject\GeoZone>
   */
  public function children(string $parentId): array;

  /**
   * Returns all zones for a country.
   *
   * @return list<\Drupal\ps_search\ValueObject\GeoZone>
   */
  public function allForCountry(string $countryCode): array;

  /**
   * Supported country codes with loaded geo zone config.
   *
   * @return list<string>
   */
  public function getSupportedCountries(): array;

  /**
   * Finds a department zone by INSEE or administrative code.
   */
  public function findDepartmentByCode(string $code, string $countryCode): ?GeoZone;

  /**
   * Returns the parent region zone when the child has a region parent.
   */
  public function getRegionForZone(GeoZone $zone): ?GeoZone;

  /**
   * @return list<GeoZone>
   */
  public function searchByLabelPrefix(
    string $query,
    string $countryCode,
    GeoZoneType $type,
    int $limit = 5,
  ): array;

  public function buildRegionToken(string $slug): string;

  public function isRegionToken(string $token): bool;

  public function parseRegionToken(string $token): ?string;

  public function normalizeForSearch(string $value): string;

  public function matchesSearchNeedle(string $needle, string $label, string $slug): bool;

}
