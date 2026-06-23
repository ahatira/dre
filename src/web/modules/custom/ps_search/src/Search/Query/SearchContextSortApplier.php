<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchSort;
use Drupal\search_api\Query\QueryInterface;

/**
 * Applies SearchContext sort parameters to Search API queries.
 */
final class SearchContextSortApplier {

  private const GEO_POINT_FIELD = 'field_geo_point';

  private const DISTANCE_SORT_RADIUS_KM = '20000';

  /**
   * Applies list sort from the search context.
   */
  public function apply(QueryInterface $query, SearchContext $context): void {
    $sort = $context->sort;

    if ($sort->sortBy === SearchSort::DISTANCE_SORT_FIELD) {
      $origin = $this->resolveSortOrigin($context->geo);
      if ($origin === NULL) {
        $query->sort(SearchSort::DEFAULT_SORT_BY, SearchSort::DEFAULT_SORT_ORDER);
        return;
      }

      $query->setOption('search_api_location', [[
        'field' => self::GEO_POINT_FIELD,
        'lat' => (string) $origin['lat'],
        'lon' => (string) $origin['lng'],
        'radius' => self::DISTANCE_SORT_RADIUS_KM,
      ]]);
      $query->sort($sort->sortBy, $sort->sortOrder);
      return;
    }

    $query->sort($sort->sortBy, $sort->sortOrder);
  }

  /**
   * @return array{lat: float, lng: float}|null
   */
  private function resolveSortOrigin(?GeoContext $geo): ?array {
    if ($geo === NULL) {
      return NULL;
    }

    return [
      'lat' => $geo->lat,
      'lng' => $geo->lng,
    ];
  }

}
