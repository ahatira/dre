<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service\Isochrone;

/**
 * Builds isochrone geometry for a travel mode and duration.
 */
interface IsochroneProviderInterface {

  /**
   * Provider identifier stored in the JSON payload (e.g. ors, google).
   */
  public function id(): string;

  /**
   * Whether this provider can compute an isochrone for the transport mode.
   */
  public function supportsTransport(string $transport): bool;

  /**
   * Builds an isochrone payload or returns NULL when unavailable.
   *
   * @return array<string, mixed>|null
   *   Isochrone payload (polygon, map_bounds, bounds, provider, …).
   */
  public function build(float $lat, float $lng, string $transport, int $minutes): ?array;

}
