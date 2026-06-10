<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service\Isochrone;

use Drupal\ps_search\ValueObject\MapBounds;

/**
 * Shared geodesy helpers for isochrone providers.
 */
final class IsochroneGeoHelper {

  private const EARTH_RADIUS_M = 6378137.0;

  /**
   * @return array{lat: float, lng: float}
   */
  public static function destinationAtBearing(float $lat, float $lng, float $bearingDeg, float $distanceM): array {
    $angular = $distanceM / self::EARTH_RADIUS_M;
    $bearing = deg2rad($bearingDeg);
    $latRad = deg2rad($lat);
    $lngRad = deg2rad($lng);

    $destLat = asin(sin($latRad) * cos($angular) + cos($latRad) * sin($angular) * cos($bearing));
    $destLng = $lngRad + atan2(
      sin($bearing) * sin($angular) * cos($latRad),
      cos($angular) - sin($latRad) * sin($destLat),
    );

    return [
      'lat' => rad2deg($destLat),
      'lng' => rad2deg($destLng),
    ];
  }

  /**
   * @param list<array{0: float, 1: float}> $ring
   *   GeoJSON ring [lng, lat] pairs.
   */
  public static function boundsFromRing(array $ring): MapBounds {
    $lats = [];
    $lngs = [];
    foreach ($ring as $pair) {
      if (!isset($pair[0], $pair[1])) {
        continue;
      }
      $lngs[] = (float) $pair[0];
      $lats[] = (float) $pair[1];
    }

    if ($lats === []) {
      return new MapBounds(0.0, 0.0, 0.0, 0.0);
    }

    return new MapBounds(min($lats), min($lngs), max($lats), max($lngs));
  }

  /**
   * @param list<array{0: float, 1: float}> $ring
   *
   * @return array<string, mixed>
   */
  public static function payloadFromRing(
    array $ring,
    string $provider,
    string $transport,
    int $minutes,
    float $lat,
    float $lng,
    ?int $radiusM = NULL,
  ): array {
    $bounds = self::boundsFromRing($ring);

    return [
      'provider' => $provider,
      'transport' => $transport,
      'minutes' => $minutes,
      'center' => [
        'lat' => $lat,
        'lng' => $lng,
      ],
      'radius_m' => $radiusM,
      'polygon' => [$ring],
      'map_bounds' => $bounds->toQueryValue(),
      'bounds' => [
        'swLat' => $bounds->swLat,
        'swLng' => $bounds->swLng,
        'neLat' => $bounds->neLat,
        'neLng' => $bounds->neLng,
      ],
    ];
  }

}
