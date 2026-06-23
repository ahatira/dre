<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Context;

use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\GeoZone\GeoZoneType;
use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\GeoContextType;
use Drupal\ps_search\ValueObject\GeoPrecision;
use Drupal\ps_search\ValueObject\GeoZone;

/**
 * Maps GeoZone referential entries to GeoContext value objects.
 */
final class GeoContextFactory {

  /**
   * Builds a GeoContext from a GeoZone definition.
   */
  public static function fromGeoZone(GeoZone $zone, string $source = 'geo_zone'): GeoContext {
    $type = self::mapZoneType($zone->type);
    $precision = self::mapPrecision($zone->type);

    return new GeoContext(
      id: $zone->id,
      slug: $zone->slug,
      type: $type,
      label: $zone->label,
      lat: $zone->lat,
      lng: $zone->lng,
      bbox: $zone->bbox,
      countryCode: $zone->countryCode,
      postalPrefixes: $zone->postalPrefixes,
      radiusM: NULL,
      precision: $precision,
      source: $source,
    );
  }

  /**
   * Builds a GeoContext from offer-derived location metadata (L3 suggest/resolve).
   *
   * @param array<string, mixed> $meta
   *   Output of LocationSearchFilter::resolveTokenMetadata().
   */
  public static function fromOfferMetadata(
    array $meta,
    string $countryCode,
    ?GeoZoneRepositoryInterface $geoZoneRepository = NULL,
  ): ?GeoContext {
    $offerCount = (int) ($meta['offer_count'] ?? 0);
    $lat = $meta['lat'] ?? NULL;
    $lng = $meta['lng'] ?? NULL;
    if ($offerCount <= 0 && ($lat === NULL || $lng === NULL)) {
      return NULL;
    }

    $countryCode = strtolower($countryCode);
    $type = match ($meta['type'] ?? 'city') {
      'department' => GeoContextType::Department,
      'arrondissement', 'postal_code' => GeoContextType::Postal,
      default => GeoContextType::City,
    };

    if ($type === GeoContextType::Department
      && $geoZoneRepository !== NULL
      && is_string($meta['department_code'] ?? NULL)
      && $meta['department_code'] !== '') {
      $zone = $geoZoneRepository->findByPostalPrefix($meta['department_code'], $countryCode);
      if ($zone !== NULL) {
        return self::fromGeoZone($zone, 'offers');
      }
    }

    $label = is_string($meta['label'] ?? NULL) ? $meta['label'] : '';
    if ($label === '') {
      return NULL;
    }

    $latF = is_numeric($lat) ? (float) $lat : 0.0;
    $lngF = is_numeric($lng) ? (float) $lng : 0.0;
    $bbox = GeoBoundingBox::fromCenterAndRadiusKm($latF, $lngF, 10.0);
    $postalPrefixes = [];
    if (is_string($meta['postal_code'] ?? NULL) && strlen($meta['postal_code']) >= 2) {
      $postalPrefixes[] = substr($meta['postal_code'], 0, 2);
    }
    elseif (is_string($meta['department_code'] ?? NULL) && $meta['department_code'] !== '') {
      $postalPrefixes[] = $meta['department_code'];
    }

    $slug = '';
    if ($geoZoneRepository !== NULL && $postalPrefixes !== []) {
      $zone = $geoZoneRepository->findByPostalPrefix($postalPrefixes[0], $countryCode);
      if ($zone !== NULL && $type === GeoContextType::Department) {
        return self::fromGeoZone($zone, 'offers');
      }
    }

    return new GeoContext(
      id: 'offer.' . md5(mb_strtolower($label . '|' . ($meta['postal_code'] ?? ''))),
      slug: $slug,
      type: $type,
      label: $label,
      lat: $latF,
      lng: $lngF,
      bbox: $bbox,
      countryCode: $countryCode,
      postalPrefixes: $postalPrefixes,
      radiusM: NULL,
      precision: GeoPrecision::Approximate,
      source: 'offers',
    );
  }

  private static function mapZoneType(GeoZoneType $type): GeoContextType {
    return match ($type) {
      GeoZoneType::Address => GeoContextType::Address,
      GeoZoneType::City => GeoContextType::City,
      GeoZoneType::Postal => GeoContextType::Postal,
      GeoZoneType::Department => GeoContextType::Department,
      GeoZoneType::Region => GeoContextType::Region,
      GeoZoneType::CountryDefault => GeoContextType::CountryDefault,
    };
  }

  private static function mapPrecision(GeoZoneType $type): GeoPrecision {
    return match ($type) {
      GeoZoneType::Department, GeoZoneType::Region => GeoPrecision::Admin,
      GeoZoneType::Address => GeoPrecision::Exact,
      default => GeoPrecision::Approximate,
    };
  }

}
