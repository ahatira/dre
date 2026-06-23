<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

/**
 * Derives postal code prefixes from administrative zone codes per country.
 */
final class GeoZonePostalPrefixBuilder {

  /**
   * @return list<string>
   */
  public function forDepartmentCode(string $countryCode, string $code): array {
    $countryCode = strtolower($countryCode);
    $code = strtoupper(trim($code));

    if ($code === '') {
      return [];
    }

    if ($countryCode === 'fr') {
      return match ($code) {
        '2A' => ['200'],
        '2B' => ['202'],
        default => preg_match('/^\d{2,3}$/', $code) === 1 ? [$code] : [],
      };
    }

    if (preg_match('/^\d{2,4}$/', $code) === 1) {
      return [$code];
    }

    return [];
  }

}
