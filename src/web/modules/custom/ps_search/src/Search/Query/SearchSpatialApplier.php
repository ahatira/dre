<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\ps_search\ValueObject\GeoBoundingBox;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\SpatialConstraint;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\QueryInterface;

/**
 * Applies SearchContext spatial constraints to Search API queries.
 */
final class SearchSpatialApplier {

  private const GEO_POINT_FIELD = 'field_geo_point';

  private const DEFAULT_GEOFILT_RADIUS_KM = '20';

  /**
   * Applies spatial filtering for a resolved search context.
   */
  public function apply(
    QueryInterface $query,
    SpatialConstraint $spatial,
    ?GeoContext $geo,
    ?MapBounds $legacyMapBounds = NULL,
  ): void {
    match ($spatial->mode) {
      SpatialMode::None => NULL,
      SpatialMode::BboxAndPostal => $this->applyBboxAndPostal($query, $spatial, $geo),
      SpatialMode::Geofilt => $this->applyGeofilt($query, $spatial, $geo),
      SpatialMode::Viewport => $this->applyViewport($query, $spatial, $legacyMapBounds),
      SpatialMode::Isochrone => NULL,
    };
  }

  private function applyBboxAndPostal(QueryInterface $query, SpatialConstraint $spatial, ?GeoContext $geo): void {
    $bbox = $spatial->bbox ?? $geo?->bbox;
    if (!$bbox instanceof GeoBoundingBox) {
      return;
    }

    $group = $query->createConditionGroup('AND');
    $group->addCondition('field_geo_lat', [$bbox->swLat, $bbox->neLat], 'BETWEEN');
    $group->addCondition('field_geo_lng', [$bbox->swLng, $bbox->neLng], 'BETWEEN');

    if ($spatial->postalPrefixes !== []) {
      $postalGroup = $query->createConditionGroup('OR');
      foreach ($spatial->postalPrefixes as $prefix) {
        $this->addPostalPrefixCondition($postalGroup, $prefix);
      }
      $group->addConditionGroup($postalGroup);
    }

    $query->addConditionGroup($group);
  }

  private function applyGeofilt(QueryInterface $query, SpatialConstraint $spatial, ?GeoContext $geo): void {
    if ($geo === NULL) {
      return;
    }

    $radiusKm = $this->resolveRadiusKm($spatial);
    $query->setOption('search_api_location', [[
      'field' => self::GEO_POINT_FIELD,
      'lat' => (string) $geo->lat,
      'lon' => (string) $geo->lng,
      'radius' => (string) $radiusKm,
    ]]);
  }

  private function applyViewport(QueryInterface $query, SpatialConstraint $spatial, ?MapBounds $legacyMapBounds): void {
    $bounds = $spatial->viewport ?? $legacyMapBounds;
    if (!$bounds instanceof MapBounds) {
      return;
    }

    $query->addCondition('field_geo_lat', $bounds->swLat, '>=');
    $query->addCondition('field_geo_lat', $bounds->neLat, '<=');
    $query->addCondition('field_geo_lng', $bounds->swLng, '>=');
    $query->addCondition('field_geo_lng', $bounds->neLng, '<=');
  }

  private function addPostalPrefixCondition(ConditionGroupInterface $target, string $prefix): void {
    $prefix = trim($prefix);
    if ($prefix === '') {
      return;
    }

    if (strlen($prefix) >= 5) {
      $target->addCondition('field_address_postal_code', $prefix);
      return;
    }

    $padLength = max(0, 5 - strlen($prefix));
    $min = $prefix . str_repeat('0', $padLength);
    $max = $prefix . str_repeat('9', $padLength);
    $target->addCondition('field_address_postal_code', [$min, $max], 'BETWEEN');
  }

  private function resolveRadiusKm(SpatialConstraint $spatial): string {
    if ($spatial->radiusM !== NULL && $spatial->radiusM > 0) {
      return (string) max(1, (int) round($spatial->radiusM / 1000));
    }

    return self::DEFAULT_GEOFILT_RADIUS_KM;
  }

}
