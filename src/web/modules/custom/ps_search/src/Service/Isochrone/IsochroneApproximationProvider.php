<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service\Isochrone;

/**
 * Speed-based circle approximation (offline fallback).
 */
final class IsochroneApproximationProvider implements IsochroneProviderInterface {

  private const TRANSPORTS = ['walking', 'transports', 'bike', 'car'];

  /**
   * Average travel speed in metres per minute per transport mode.
   */
  private const SPEED_M_PER_MIN = [
    'walking' => 80,
    'transports' => 240,
    'bike' => 400,
    'car' => 1000,
  ];

  private const POLYGON_POINTS = 32;

  /**
   *
   */
  public function id(): string {
    return 'approximation';
  }

  /**
   *
   */
  public function supportsTransport(string $transport): bool {
    return in_array($transport, self::TRANSPORTS, TRUE);
  }

  /**
   *
   */
  public function build(float $lat, float $lng, string $transport, int $minutes): ?array {
    if (!$this->supportsTransport($transport)) {
      return NULL;
    }

    $speed = self::SPEED_M_PER_MIN[$transport];
    $radiusM = (int) round($speed * $minutes);
    $ring = $this->buildCircleRing($lat, $lng, $radiusM);

    return IsochroneGeoHelper::payloadFromRing(
      $ring,
      $this->id(),
      $transport,
      $minutes,
      $lat,
      $lng,
      $radiusM,
    );
  }

  /**
   * Estimates reach radius in metres for radial probing (Google provider).
   */
  public function estimateRadiusMeters(string $transport, int $minutes): int {
    if (!$this->supportsTransport($transport)) {
      return 400;
    }

    return (int) round(self::SPEED_M_PER_MIN[$transport] * $minutes);
  }

  /**
   * @return list<array{0: float, 1: float}>
   */
  private function buildCircleRing(float $lat, float $lng, int $radiusM): array {
    $ring = [];
    for ($step = 0; $step < self::POLYGON_POINTS; $step++) {
      $bearing = 360 * $step / self::POLYGON_POINTS;
      $point = IsochroneGeoHelper::destinationAtBearing($lat, $lng, $bearing, (float) $radiusM);
      $ring[] = [$point['lng'], $point['lat']];
    }

    if ($ring !== []) {
      $ring[] = $ring[0];
    }

    return $ring;
  }

}
