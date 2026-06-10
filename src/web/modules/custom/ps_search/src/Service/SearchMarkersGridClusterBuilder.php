<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\ps_search\ValueObject\MapBounds;

/**
 * Builds grid-based marker clusters for dense map zones.
 */
final class SearchMarkersGridClusterBuilder {

  /**
   * Default target number of grid cells for cluster aggregation.
   */
  private const DEFAULT_TARGET_CELLS = 64;

  /**
   * Aggregates geo points into grid cells for cluster markers.
   *
   * @param list<array{lat: float, lng: float}> $points
   *   Geo points inside the active zone.
   * @param \Drupal\ps_search\ValueObject\MapBounds $bounds
   *   Active map bounds.
   * @param int $targetCells
   *   Approximate number of grid cells (8x8 by default).
   *
   * @return list<array{lat: float, lng: float, count: int, map_bounds: string}>
   *   Cluster rows sorted by descending count.
   */
  public function build(array $points, MapBounds $bounds, int $targetCells = self::DEFAULT_TARGET_CELLS): array {
    if ($points === []) {
      return [];
    }

    $targetCells = max(4, min($targetCells, 256));
    $cols = (int) max(2, round(sqrt($targetCells)));
    $rows = (int) max(2, ceil($targetCells / $cols));

    $latSpan = max($bounds->neLat - $bounds->swLat, 0.00001);
    $lngSpan = max($bounds->neLng - $bounds->swLng, 0.00001);
    $latStep = $latSpan / $rows;
    $lngStep = $lngSpan / $cols;

    /** @var array<string, array{count: int, lat_sum: float, lng_sum: float, row: int, col: int}> $cells */
    $cells = [];

    foreach ($points as $point) {
      $lat = $point['lat'];
      $lng = $point['lng'];
      if (!is_finite($lat) || !is_finite($lng)) {
        continue;
      }

      $row = (int) min($rows - 1, max(0, floor(($lat - $bounds->swLat) / $latStep)));
      $col = (int) min($cols - 1, max(0, floor(($lng - $bounds->swLng) / $lngStep)));
      $key = $row . ':' . $col;

      if (!isset($cells[$key])) {
        $cells[$key] = [
          'count' => 0,
          'lat_sum' => 0.0,
          'lng_sum' => 0.0,
          'row' => $row,
          'col' => $col,
        ];
      }

      $cells[$key]['count']++;
      $cells[$key]['lat_sum'] += $lat;
      $cells[$key]['lng_sum'] += $lng;
    }

    $clusters = [];
    foreach ($cells as $cell) {
      if ($cell['count'] <= 0) {
        continue;
      }

      $swLat = $bounds->swLat + ($cell['row'] * $latStep);
      $neLat = min($bounds->neLat, $swLat + $latStep);
      $swLng = $bounds->swLng + ($cell['col'] * $lngStep);
      $neLng = min($bounds->neLng, $swLng + $lngStep);
      $cellBounds = new MapBounds($swLat, $swLng, $neLat, $neLng);

      $clusters[] = [
        'lat' => round($cell['lat_sum'] / $cell['count'], 6),
        'lng' => round($cell['lng_sum'] / $cell['count'], 6),
        'count' => $cell['count'],
        'map_bounds' => $cellBounds->toQueryValue(),
      ];
    }

    usort($clusters, static fn(array $a, array $b): int => $b['count'] <=> $a['count']);

    return $clusters;
  }

}
