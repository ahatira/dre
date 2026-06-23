<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Result of resolving a free-text location query (L3 geocoding).
 */
final readonly class LocationResolveResult {

  /**
   * @param list<GeoContext> $candidates
   */
  public function __construct(
    public ?GeoContext $geo,
    public bool $ambiguous,
    public array $candidates,
  ) {}

}
