<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Spatial filtering constraints derived from geo context and map interactions.
 */
final readonly class SpatialConstraint {

  /**
   * @param list<string> $postalPrefixes
   */
  public function __construct(
    public SpatialMode $mode,
    public ?GeoBoundingBox $bbox,
    public array $postalPrefixes,
    public ?int $radiusM,
    public ?MapBounds $viewport,
    public ?string $isochroneGeoJson,
  ) {}

}
