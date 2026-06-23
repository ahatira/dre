<?php

declare(strict_types=1);

namespace Drupal\ps_search\GeoZone;

/**
 * Administrative or semantic geo zone types for search v2.
 */
enum GeoZoneType: string {

  case Address = 'address';
  case City = 'city';
  case Postal = 'postal';
  case Department = 'department';
  case Region = 'region';
  case CountryDefault = 'country_default';

  /**
   * Whether postal prefix filters apply for this zone type.
   */
  public function requiresPostalPrefixes(): bool {
    return match ($this) {
      self::Department, self::Region => TRUE,
      default => FALSE,
    };
  }

  /**
   * Parses a stored config value into an enum case.
   */
  public static function fromConfig(string $value): ?self {
    return self::tryFrom($value);
  }

}
