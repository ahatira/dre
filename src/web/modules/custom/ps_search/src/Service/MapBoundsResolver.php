<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\ValueObject\MapBounds;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves active or default map bounds for zone-scoped search queries.
 */
final class MapBoundsResolver {

  private const QUERY_PARAM = 'map_bounds';

  private const MAX_LAT = 90.0;

  private const MAX_LNG = 180.0;

  public function __construct(
    private readonly LocationCentroidResolver $locationCentroidResolver,
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns bounds from the request or a computed default for the search zone.
   */
  public function resolveActiveBounds(Request $request): ?MapBounds {
    $explicit = $this->parseQueryBounds($request);
    if ($explicit instanceof MapBounds) {
      return $explicit;
    }

    return $this->resolveDefaultBounds($request);
  }

  /**
   * Whether the request carries an explicit map_bounds parameter.
   */
  public function hasExplicitBounds(Request $request): bool {
    return $this->parseQueryBounds($request) instanceof MapBounds;
  }

  /**
   * Whether locality tokens changed between two requests.
   *
   * When TRUE, clients should recalculate the default map zone (decision B).
   */
  public function localityFilterChanged(Request $current, Request $previous): bool {
    $currentTokens = $this->locationSearchFilter->extractTokensFromRequest($current);
    $previousTokens = $this->locationSearchFilter->extractTokensFromRequest($previous);
    return $currentTokens !== $previousTokens;
  }

  /**
   * Parses map_bounds=sw_lat,sw_lng,ne_lat,ne_lng from the query string.
   */
  private function parseQueryBounds(Request $request): ?MapBounds {
    $raw = $request->query->get(self::QUERY_PARAM);
    if (!is_string($raw) || $raw === '') {
      return NULL;
    }

    $parts = array_map('trim', explode(',', $raw));
    if (count($parts) !== 4) {
      return NULL;
    }

    $values = [];
    foreach ($parts as $part) {
      if (!is_numeric($part)) {
        return NULL;
      }
      $values[] = (float) $part;
    }

    [$swLat, $swLng, $neLat, $neLng] = $values;
    if (!$this->isValidBounds($swLat, $swLng, $neLat, $neLng)) {
      return NULL;
    }

    return new MapBounds($swLat, $swLng, $neLat, $neLng);
  }

  /**
   * Builds default bounds from locality centroid or configured France defaults.
   */
  private function resolveDefaultBounds(Request $request): ?MapBounds {
    $locationMap = $this->locationCentroidResolver->resolveFromRequest($request);
    if ($locationMap !== NULL
      && isset($locationMap['lat'], $locationMap['lng'])
      && is_numeric($locationMap['lat'])
      && is_numeric($locationMap['lng'])) {
      $radiusM = (int) ($locationMap['radiusM'] ?? 2500);
      return $this->boundsFromCenter((float) $locationMap['lat'], (float) $locationMap['lng'], $radiusM / 1000);
    }

    $config = $this->configFactory->get('ps_search.map_zone_settings');
    $lat = (float) ($config->get('default_center_lat') ?? 46.603354);
    $lng = (float) ($config->get('default_center_lng') ?? 1.888334);
    $radiusKm = (float) ($config->get('default_radius_km') ?? 50);

    return $this->boundsFromCenter($lat, $lng, $radiusKm);
  }

  /**
   * Approximates a bounding box from a center point and radius in kilometres.
   */
  public function boundsFromCenter(float $lat, float $lng, float $radiusKm): MapBounds {
    $radiusKm = max(0.1, min($radiusKm, 500.0));
    $latDelta = $radiusKm / 111.0;
    $lngScale = cos(deg2rad(max(-89.9, min(89.9, $lat))));
    $lngDelta = $lngScale > 0 ? $radiusKm / (111.0 * $lngScale) : $radiusKm / 111.0;

    return new MapBounds(
      max(-self::MAX_LAT, $lat - $latDelta),
      max(-self::MAX_LNG, $lng - $lngDelta),
      min(self::MAX_LAT, $lat + $latDelta),
      min(self::MAX_LNG, $lng + $lngDelta),
    );
  }

  /**
   * Validates geographic ordering and sane span for a bounding box.
   */
  private function isValidBounds(float $swLat, float $swLng, float $neLat, float $neLng): bool {
    if ($swLat < -self::MAX_LAT || $neLat > self::MAX_LAT) {
      return FALSE;
    }
    if ($swLng < -self::MAX_LNG || $neLng > self::MAX_LNG) {
      return FALSE;
    }
    if ($swLat >= $neLat || $swLng >= $neLng) {
      return FALSE;
    }
    if (($neLat - $swLat) > 30.0 || ($neLng - $swLng) > 30.0) {
      return FALSE;
    }

    return TRUE;
  }

}
