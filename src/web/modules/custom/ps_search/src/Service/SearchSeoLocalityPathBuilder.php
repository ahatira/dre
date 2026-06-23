<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Site\Settings;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\ValueObject\GeoZone;

/**
 * Builds and parses BNPPRE-style locality segments in SEO search URLs.
 *
 * Supported path patterns (after operation/asset slugs):
 * - {region-slug}/           e.g. ile-de-france
 * - {dept-name-code}/         e.g. paris-75, bouches-du-rhone-13
 * - {dept}/{city-arr-postal}/ e.g. paris-75/paris-9-75009
 *
 * Multiple locations stay in the ?locations= query string.
 */
final class SearchSeoLocalityPathBuilder {

  public function __construct(
    private readonly Connection $database,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
  ) {}

  /**
   * Converts a filter token into SEO URL path segments.
   *
   * @return array{region?: string, dept?: string, city?: string}
   */
  public function tokenToPathSegments(string $token): array {
    if ($this->geoZoneRepository->isRegionToken($token)) {
      $slug = $this->geoZoneRepository->parseRegionToken($token);
      if ($slug !== NULL) {
        return ['region' => $slug];
      }
    }

    $countryCode = $this->resolveCountryCode();
    $zone = $this->findGeoZoneForToken($token, $countryCode);
    if ($zone !== NULL && $zone->type === GeoZoneType::Region) {
      return ['region' => $zone->slug];
    }

    $meta = $this->resolveOutboundMetadata($token);
    $segments = [];

    if (($meta['type'] ?? '') === 'department') {
      $deptSlug = $zone?->slug;
      if ($deptSlug === NULL || $deptSlug === '') {
        $deptCode = preg_match('/^\d{2,3}$/', $token) === 1 ? $token : '';
        $deptName = (string) ($meta['admin_area'] ?? '');
        if ($deptName !== '' && $deptCode !== '') {
          $deptSlug = $this->toSlug($deptName) . '-' . $deptCode;
        }
      }
      if ($deptSlug !== NULL && $deptSlug !== '') {
        $segments['dept'] = $deptSlug;
      }
      return $segments;
    }

    $postalCode = (string) ($meta['postal_code'] ?? '');
    $deptCode = $postalCode !== '' ? substr($postalCode, 0, 2) : '';
    $deptZone = $deptCode !== '' ? $this->geoZoneRepository->findByPostalPrefix($deptCode, $countryCode) : NULL;
    if ($deptZone !== NULL) {
      $segments['dept'] = $deptZone->slug;
    }
    else {
      $deptName = (string) ($meta['admin_area'] ?? '');
      if ($deptName !== '' && $deptCode !== '') {
        $segments['dept'] = $this->toSlug($deptName) . '-' . $deptCode;
      }
    }

    $locality = (string) ($meta['locality'] ?? '');
    if ($locality === '') {
      return $segments;
    }

    $citySlug = $this->toSlug($locality);
    if (($meta['type'] ?? '') === 'arrondissement' && $postalCode !== '') {
      $arrNum = (int) substr($postalCode, -2);
      $segments['city'] = $citySlug . '-' . $arrNum . '-' . $postalCode;
      return $segments;
    }

    $segments['city'] = $postalCode !== '' ? $citySlug . '-' . $postalCode : $citySlug;
    return $segments;
  }

  /**
   * Appends BNPPRE locality segments to an SEO path prefix.
   */
  public function appendSegmentsToPath(string $seoPath, string $token): string {
    $segments = $this->tokenToPathSegments($token);
    if (isset($segments['region'])) {
      $seoPath .= '/' . $segments['region'];
    }
    if (isset($segments['dept'])) {
      $seoPath .= '/' . $segments['dept'];
    }
    if (isset($segments['city'])) {
      $seoPath .= '/' . $segments['city'];
    }
    return $seoPath;
  }

  /**
   * Parses locality tail segments into a single location filter token.
   *
   * @param list<string> $segments
   */
  public function parseLocalitySegments(array $segments): ?string {
    $segments = array_values(array_filter($segments, static fn (string $segment): bool => $segment !== ''));
    if ($segments === []) {
      return NULL;
    }

    if (count($segments) === 1) {
      return $this->singleSegmentToToken($segments[0]);
    }

    $citySegment = $segments[count($segments) - 1];
    return $this->citySegmentToToken($citySegment);
  }

  /**
   * Resolves inbound SEO path segments to a location filter token.
   *
   * @deprecated Prefer parseLocalitySegments() for new code.
   */
  public function pathSegmentsToToken(?string $deptSegment, ?string $citySegment): ?string {
    if ($citySegment !== NULL && $citySegment !== '') {
      return $this->citySegmentToToken($citySegment);
    }
    if ($deptSegment !== NULL && $deptSegment !== '') {
      return $this->singleSegmentToToken($deptSegment);
    }
    return NULL;
  }

  /**
   * Parses a single locality segment (region, department or city).
   */
  public function singleSegmentToToken(string $segment): ?string {
    if ($this->isDepartmentSlugSegment($segment)) {
      $zone = $this->geoZoneRepository->findBySlug($segment, $this->resolveCountryCode());
      if ($zone !== NULL && $zone->type === GeoZoneType::Department) {
        return $zone->code;
      }
      return $this->deptSegmentToToken($segment);
    }

    $zone = $this->geoZoneRepository->findBySlug($segment, $this->resolveCountryCode());
    if ($zone !== NULL) {
      return $this->geoZoneToToken($zone);
    }

    return $this->citySegmentToToken($segment);
  }

  /**
   * Parses a dept segment such as paris-75 into a department token.
   */
  public function deptSegmentToToken(string $segment): ?string {
    if (preg_match('/-(\d{2,3})$/', $segment, $matches) !== 1) {
      return NULL;
    }
    return $matches[1];
  }

  /**
   * Parses a city segment into a location filter token.
   */
  public function citySegmentToToken(string $segment): ?string {
    if ($this->isDepartmentSlugSegment($segment)) {
      return $this->deptSegmentToToken($segment);
    }

    $parts = explode('-', $segment);
    if ($parts === []) {
      return NULL;
    }

    $postal = array_pop($parts);
    if (preg_match('/^\d{5}$/', $postal) === 1) {
      if ($parts !== [] && is_numeric($parts[count($parts) - 1])) {
        array_pop($parts);
      }
      return $postal;
    }

    if ($parts === []) {
      return $this->slugToLabel($segment);
    }

    return $this->slugToLabel(implode('-', $parts));
  }

  /**
   * Detects a department slug such as paris-75 or bouches-du-rhone-13.
   */
  public function isDepartmentSlugSegment(string $segment): bool {
    if (preg_match('/-\d{5}$/', $segment) === 1) {
      return FALSE;
    }

    return $this->deptSegmentToToken($segment) !== NULL;
  }

  /**
   * Converts a label to a URL slug.
   */
  public function toSlug(string $value): string {
    $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
    if ($ascii === FALSE) {
      $ascii = $value;
    }
    $ascii = strtolower($ascii);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $ascii);
    return trim((string) $slug, '-');
  }

  private function geoZoneToToken(GeoZone $zone): string {
    return match ($zone->type) {
      GeoZoneType::Region => $this->geoZoneRepository->buildRegionToken($zone->slug),
      GeoZoneType::Department => $zone->code,
      default => $zone->slug,
    };
  }

  private function findGeoZoneForToken(string $token, string $countryCode): ?GeoZone {
    if ($this->geoZoneRepository->isRegionToken($token)) {
      $slug = $this->geoZoneRepository->parseRegionToken($token);
      return $slug !== NULL ? $this->geoZoneRepository->findBySlug($slug, $countryCode) : NULL;
    }

    if (preg_match('/^\d{2,3}$/', $token) === 1) {
      return $this->geoZoneRepository->findByPostalPrefix($token, $countryCode);
    }

    if (preg_match('/^\d{5}$/', $token) === 1) {
      return $this->geoZoneRepository->findByPostalPrefix(substr($token, 0, 2), $countryCode);
    }

    return NULL;
  }

  private function resolveOutboundMetadata(string $token): array {
    $meta = [
      'type' => 'city',
      'locality' => '',
      'admin_area' => '',
      'postal_code' => '',
    ];

    if (preg_match('/^\d{5}$/', $token) === 1) {
      $select = $this->database->select('node__field_address', 'a');
      $select->fields('a', ['field_address_locality', 'field_address_administrative_area']);
      $select->condition('a.field_address_postal_code', $token);
      $select->range(0, 1);
      $row = $select->execute()->fetchAssoc();
      if ($row !== FALSE) {
        $meta['locality'] = trim((string) ($row['field_address_locality'] ?? ''));
        $meta['admin_area'] = trim((string) ($row['field_address_administrative_area'] ?? ''));
        $meta['postal_code'] = $token;
        $meta['type'] = $this->isArrondissementPostal($meta['locality'], $token) ? 'arrondissement' : 'postal_code';
      }
      return $meta;
    }

    if (preg_match('/^\d{2,3}$/', $token) === 1) {
      $meta['type'] = 'department';
      $zone = $this->geoZoneRepository->findDepartmentByCode($token, $this->resolveCountryCode());
      $meta['admin_area'] = $zone?->label ?? '';
      return $meta;
    }

    $meta['locality'] = $token;
    return $meta;
  }

  private function isArrondissementPostal(string $locality, string $postalCode): bool {
    if (strlen($postalCode) !== 5) {
      return FALSE;
    }
    $suffix = (int) substr($postalCode, -2);
    $prefix = substr($postalCode, 0, 2);
    $localityLower = mb_strtolower($locality);

    return ($localityLower === 'paris' && $prefix === '75' && $suffix >= 1 && $suffix <= 20)
      || ($localityLower === 'lyon' && $prefix === '69' && $suffix >= 1 && $suffix <= 9)
      || ($localityLower === 'marseille' && $prefix === '13' && $suffix >= 1 && $suffix <= 16);
  }

  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

  /**
   * Converts a URL slug back to a human label.
   */
  private function slugToLabel(string $slug): string {
    return ucwords(str_replace('-', ' ', $slug));
  }

}
