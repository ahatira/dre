<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Axis-aligned geographic bounding box (south-west / north-east corners).
 */
final class GeoBoundingBox {

  public function __construct(
    public readonly float $swLat,
    public readonly float $swLng,
    public readonly float $neLat,
    public readonly float $neLng,
  ) {}

  /**
   * Builds an approximate bbox around a center point and radius in kilometres.
   */
  public static function fromCenterAndRadiusKm(float $lat, float $lng, float $radiusKm): self {
    $latDelta = $radiusKm / 111.0;
    $lngScale = max(cos(deg2rad($lat)), 0.01);
    $lngDelta = $radiusKm / (111.0 * $lngScale);

    return new self(
      swLat: max(-90.0, $lat - $latDelta),
      swLng: max(-180.0, $lng - $lngDelta),
      neLat: min(90.0, $lat + $latDelta),
      neLng: min(180.0, $lng + $lngDelta),
    );
  }

  /**
   * @param array<string, mixed> $data
   */
  public static function fromConfigArray(array $data): ?self {
    if (!isset($data['sw_lat'], $data['sw_lng'], $data['ne_lat'], $data['ne_lng'])) {
      return NULL;
    }

    return new self(
      swLat: (float) $data['sw_lat'],
      swLng: (float) $data['sw_lng'],
      neLat: (float) $data['ne_lat'],
      neLng: (float) $data['ne_lng'],
    );
  }

  /**
   * @return array<string, float>
   */
  public function toConfigArray(): array {
    return [
      'sw_lat' => $this->swLat,
      'sw_lng' => $this->swLng,
      'ne_lat' => $this->neLat,
      'ne_lng' => $this->neLng,
    ];
  }

  /**
   * Whether corner values form a valid non-degenerate box.
   */
  public function isValid(): bool {
    return $this->swLat >= -90.0
      && $this->neLat <= 90.0
      && $this->swLng >= -180.0
      && $this->neLng <= 180.0
      && $this->swLat < $this->neLat
      && $this->swLng < $this->neLng;
  }

}
