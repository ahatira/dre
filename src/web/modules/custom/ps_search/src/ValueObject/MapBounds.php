<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Geographic bounding box for map zone searches (SW/NE corners).
 */
final class MapBounds {

  public function __construct(
    public readonly float $swLat,
    public readonly float $swLng,
    public readonly float $neLat,
    public readonly float $neLng,
  ) {}

  /**
   * Serialises bounds for the map_bounds URL query parameter.
   */
  public function toQueryValue(): string {
    return implode(',', [
      $this->format($this->swLat),
      $this->format($this->swLng),
      $this->format($this->neLat),
      $this->format($this->neLng),
    ]);
  }

  /**
   * Builds Search API location BETWEEN values (lat,lng pairs).
   *
   * @return array{0: string, 1: string}
   *   South-west and north-east coordinate strings.
   */
  public function toSearchApiBetween(): array {
    return [
      $this->format($this->swLat) . ',' . $this->format($this->swLng),
      $this->format($this->neLat) . ',' . $this->format($this->neLng),
    ];
  }

  /**
   * Formats a coordinate for stable query strings.
   */
  private function format(float $value): string {
    return rtrim(rtrim(sprintf('%.6f', $value), '0'), '.');
  }

}
