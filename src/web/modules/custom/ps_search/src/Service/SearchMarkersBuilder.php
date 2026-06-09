<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Item\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds zone-scoped map marker payloads for the search markers API.
 */
final class SearchMarkersBuilder {

  public function __construct(
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly SearchMapMarkerBuilder $markerBuilder,
    private readonly SearchResultCounter $resultCounter,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Returns marker data and zone count for the current search request.
   *
   * @return array{markers: list<array{nid: string, lat: float, lng: float, label: string}>, zone_count: int}
   *   JSON-serializable marker payload.
   */
  public function build(Request $request): array {
    $index = Index::load('offers');
    if (!$index) {
      return [
        'markers' => [],
        'zone_count' => 0,
      ];
    }

    $max = (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('markers_max') ?? 500);
    $max = max(1, min($max, 1000));

    $query = $index->query();
    $query->range(0, $max);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);

    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    if ($bounds !== NULL) {
      $this->filterQueryBuilder->applyMapBounds($query, $bounds);
    }

    $query->addCondition('field_geo_lat', NULL, '<>');
    $query->addCondition('field_geo_lng', NULL, '<>');

    try {
      $results = $query->execute();
    }
    catch (\Exception) {
      return [
        'markers' => [],
        'zone_count' => 0,
      ];
    }

    $rows = [];
    foreach ($results->getResultItems() as $item) {
      if (!$item instanceof ItemInterface) {
        continue;
      }
      $row = $this->extractCoordinates($item);
      if ($row !== NULL) {
        $rows[] = $row;
      }
    }

    if ($rows === []) {
      return [
        'markers' => [],
        'zone_count' => $this->resultCounter->countInBounds($request),
      ];
    }

    $nids = array_column($rows, 'nid');
    /** @var array<int, \Drupal\node\NodeInterface> $nodes */
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    $markers = [];
    foreach ($rows as $row) {
      $node = $nodes[$row['nid']] ?? NULL;
      if (!$node instanceof NodeInterface || $node->bundle() !== 'offer') {
        continue;
      }
      $markers[] = [
        'nid' => (string) $row['nid'],
        'lat' => $row['lat'],
        'lng' => $row['lng'],
        'label' => $this->markerBuilder->buildPriceLabel($node),
      ];
    }

    return [
      'markers' => $markers,
      'zone_count' => $this->resultCounter->countInBounds($request),
    ];
  }

  /**
   * Extracts nid and coordinates from a search result item.
   *
   * @return array{nid: int, lat: float, lng: float}|null
   *   Parsed coordinates or NULL when incomplete.
   */
  private function extractCoordinates(ItemInterface $item): ?array {
    $nidField = $item->getField('nid');
    $latField = $item->getField('field_geo_lat');
    $lngField = $item->getField('field_geo_lng');
    if ($nidField === NULL || $latField === NULL || $lngField === NULL) {
      return NULL;
    }

    $nidValues = $nidField->getValues();
    $latValues = $latField->getValues();
    $lngValues = $lngField->getValues();
    if ($nidValues === [] || $latValues === [] || $lngValues === []) {
      return NULL;
    }

    $nid = (int) $nidValues[0];
    $lat = (float) $latValues[0];
    $lng = (float) $lngValues[0];
    if ($nid <= 0 || !is_finite($lat) || !is_finite($lng)) {
      return NULL;
    }

    return [
      'nid' => $nid,
      'lat' => $lat,
      'lng' => $lng,
    ];
  }

}
