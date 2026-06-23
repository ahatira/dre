<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Immutable resolved geographic context for one search request.
 */
final readonly class GeoContext {

  /**
   * @param list<string> $postalPrefixes
   */
  public function __construct(
    public string $id,
    public string $slug,
    public GeoContextType $type,
    public string $label,
    public float $lat,
    public float $lng,
    public GeoBoundingBox $bbox,
    public string $countryCode,
    public array $postalPrefixes,
    public ?int $radiusM,
    public GeoPrecision $precision,
    public string $source,
  ) {}

}
