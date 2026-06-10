<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\ps_search\Contract\SearchResultGeoBoundsResolverInterface;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Item\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Computes geographic bounds and filter counts for search results.
 *
 * Used by MapBoundsResolver for auto-fit on first load and for zone/global
 * counters. Geo extents are derived from Search API in batches (lat/lng only)
 * without loading full node entities.
 */
final class SearchResultGeoBoundsResolver implements SearchResultGeoBoundsResolverInterface {

  /**
   * Maximum offers loaded to derive geographic extents.
   */
  private const FETCH_MAX = 5000;

  /**
   * Relative padding added around result extents.
   */
  private const PADDING_RATIO = 0.08;

  /**
   * Minimum padding in kilometres when results are co-located.
   */
  private const MIN_PADDING_KM = 2.0;

  /**
   * Batch size when scanning geo coordinates from Search API.
   */
  private const BATCH_SIZE = 100;

  public function __construct(
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
  ) {}

  /**
   * Counts offers matching business filters only.
   */
  public function countBusinessFilters(Request $request): int {
    return $this->executeCount($request, NULL);
  }

  /**
   * Counts offers matching business filters within explicit map bounds.
   */
  public function countInBoundsWithBounds(Request $request, MapBounds $bounds): int {
    return $this->executeCount($request, $bounds);
  }

  /**
   * Builds bounds that contain all geo-located offers matching business filters.
   */
  public function resolveFromFilteredResults(Request $request): ?MapBounds {
    $index = Index::load('offers');
    if (!$index) {
      return NULL;
    }

    $swLat = NULL;
    $neLat = NULL;
    $swLng = NULL;
    $neLng = NULL;
    $offset = 0;

    while ($offset < self::FETCH_MAX) {
      $query = $index->query();
      $query->range($offset, self::BATCH_SIZE);
      $this->filterQueryBuilder->applyBusinessFilters($query, $request);
      $query->addCondition('field_geo_lat', NULL, '<>');
      $query->addCondition('field_geo_lng', NULL, '<>');
      $query->setOption('search_api_bypass_access', TRUE);

      try {
        $items = $query->execute()->getResultItems();
      }
      catch (\Exception) {
        return NULL;
      }

      if ($items === []) {
        break;
      }

      foreach ($items as $item) {
        if (!$item instanceof ItemInterface) {
          continue;
        }
        $lat = $item->getField('field_geo_lat')?->getValues()[0] ?? NULL;
        $lng = $item->getField('field_geo_lng')?->getValues()[0] ?? NULL;
        if (!is_numeric($lat) || !is_numeric($lng)) {
          continue;
        }
        $lat = (float) $lat;
        $lng = (float) $lng;
        $swLat = $swLat === NULL ? $lat : min($swLat, $lat);
        $neLat = $neLat === NULL ? $lat : max($neLat, $lat);
        $swLng = $swLng === NULL ? $lng : min($swLng, $lng);
        $neLng = $neLng === NULL ? $lng : max($neLng, $lng);
      }

      if (count($items) < self::BATCH_SIZE) {
        break;
      }
      $offset += self::BATCH_SIZE;
    }

    if ($swLat === NULL || $neLat === NULL || $swLng === NULL || $neLng === NULL) {
      return NULL;
    }

    return $this->boundsFromExtents($swLat, $swLng, $neLat, $neLng);
  }

  /**
   * Runs a zero-range Search API count query.
   */
  private function executeCount(Request $request, ?MapBounds $bounds): int {
    $index = Index::load('offers');
    if (!$index) {
      return 0;
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);
    if ($bounds instanceof MapBounds) {
      $this->filterQueryBuilder->applyMapBounds($query, $bounds);
    }

    try {
      return (int) $query->execute()->getResultCount();
    }
    catch (\Exception) {
      return 0;
    }
  }

  /**
   * Builds padded bounds from geographic extents.
   */
  private function boundsFromExtents(float $swLat, float $swLng, float $neLat, float $neLng): MapBounds {
    $latPad = max(($neLat - $swLat) * self::PADDING_RATIO, self::MIN_PADDING_KM / 111.0);
    $centerLat = ($swLat + $neLat) / 2.0;
    $lngScale = cos(deg2rad(max(-89.9, min(89.9, $centerLat))));
    $lngPad = ($neLng - $swLng) * self::PADDING_RATIO;
    if ($lngScale > 0) {
      $lngPad = max($lngPad, self::MIN_PADDING_KM / (111.0 * $lngScale));
    }
    else {
      $lngPad = max($lngPad, self::MIN_PADDING_KM / 111.0);
    }

    return new MapBounds(
      max(-90.0, $swLat - $latPad),
      max(-180.0, $swLng - $lngPad),
      min(90.0, $neLat + $latPad),
      min(180.0, $neLng + $lngPad),
    );
  }

}
