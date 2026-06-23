<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Item\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds zone-scoped map marker payloads for the search markers API.
 *
 * Labels are derived from indexed budget fields (no node loadMultiple).
 * Responses are cached per filter/bounds fingerprint (Phase 4.2).
 * Dense zones switch to grid clusters when zone_count exceeds markers_max.
 */
final class SearchMarkersBuilder {

  /**
   * Cache max-age in seconds for marker payloads.
   */
  private const CACHE_BIN_MAX_AGE = 60;

  /**
   * Maximum points loaded for server-side clustering.
   */
  private const CLUSTER_FETCH_MAX = 5000;

  /**
   * Cache tags for marker payload invalidation.
   *
   * @var list<string>
   */
  private const CACHE_TAGS = [
    'search_api_list:offers',
    'config:ps_search.map_zone_settings',
  ];

  public function __construct(
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
    private readonly MapBoundsResolver $mapBoundsResolver,
    private readonly SearchMapMarkerBuilder $markerBuilder,
    private readonly SearchResultCounter $resultCounter,
    private readonly SearchMarkersGridClusterBuilder $gridClusterBuilder,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly CacheBackendInterface $cacheBackend,
    private readonly SearchListLoadedLimitResolver $listLoadedLimitResolver,
    private readonly ?SearchSolrCircuitBreaker $circuitBreaker = NULL,
  ) {}

  /**
   * Returns marker data and zone count for the current search request.
   *
   * @return array<string, mixed>
   *   JSON-serializable marker payload.
   */
  public function build(Request $request): array {
    $max = $this->resolveMarkersMax();
    $cacheKey = $this->buildCacheKey($request, $max);
    $cached = $this->cacheBackend->get($cacheKey);
    if ($cached !== FALSE) {
      return $cached->data;
    }

    $payload = $this->buildUncached($request, $max);
    $this->cacheBackend->set(
      $cacheKey,
      $payload,
      time() + self::CACHE_BIN_MAX_AGE,
      self::CACHE_TAGS,
    );

    return $payload;
  }

  /**
   * Builds marker payload without cache lookup.
   *
   * @return array<string, mixed>
   *   Marker payload.
   */
  private function buildUncached(Request $request, int $max): array {
    if ($this->circuitBreaker?->isUnavailable()) {
      return $this->attachGlobalCount($this->emptyPayload(), 0);
    }

    $globalCount = $this->resultCounter->countBusinessFilters($request);

    $index = Index::load('offers');
    if (!$index) {
      return $this->attachGlobalCount($this->emptyPayload(), $globalCount);
    }

    $zoneCount = $this->resultCounter->countInBounds($request);
    if ($zoneCount === 0) {
      return $this->attachGlobalCount($this->emptyPayload(), $globalCount);
    }

    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    $listLimit = $this->listLoadedLimitResolver->resolve($request);
    $listOffset = $this->listLoadedLimitResolver->resolveOffset($request);
    $pageSize = $this->listLoadedLimitResolver->resolvePageSize($request, $listLimit, $listOffset);
    $useClusterMode = $zoneCount > $max
      && $bounds instanceof MapBounds
      && $this->isClusterModeEnabled()
      && $listLimit >= $zoneCount;

    if ($useClusterMode) {
      return $this->attachGlobalCount(
        $this->buildClusterPayload($request, $bounds, $zoneCount, $max),
        $globalCount,
      );
    }

    return $this->attachGlobalCount(
      $this->buildIndividualPayload($request, $max, $zoneCount, $listLimit, $listOffset, $pageSize),
      $globalCount,
    );
  }

  /**
   * Builds individual marker payload for zones within the markers cap.
   *
   * @return array<string, mixed>
   *   Marker payload.
   */
  private function buildIndividualPayload(
    Request $request,
    int $max,
    int $zoneCount,
    int $listLimit,
    int $listOffset = 0,
    int $pageSize = 0,
  ): array {
    $index = Index::load('offers');
    if (!$index) {
      return $this->emptyPayload();
    }

    $fetchLimit = $pageSize > 0 ? $pageSize : max(1, min($listLimit, $max));
    $query = $index->query();
    $query->range($listOffset, min($fetchLimit, $max));
    $bounds = $this->mapBoundsResolver->resolveActiveBounds($request);
    $this->filterQueryBuilder->apply($query, $request, $bounds);
    $this->filterQueryBuilder->applyListSort($query, $request);

    $query->addCondition('field_geo_lat', NULL, '<>');
    $query->addCondition('field_geo_lng', NULL, '<>');

    try {
      $results = $query->execute();
    }
    catch (\Exception $exception) {
      $this->circuitBreaker?->recordFailure($exception);
      return $this->emptyPayload();
    }

    $markers = [];
    $seenNids = [];
    foreach ($results->getResultItems() as $item) {
      if (!$item instanceof ItemInterface) {
        continue;
      }
      $row = $this->extractMarkerFromItem($item);
      if ($row === NULL) {
        continue;
      }
      if (isset($seenNids[$row['nid']])) {
        continue;
      }
      $seenNids[$row['nid']] = TRUE;
      $markers[] = $row;
    }

    return [
      'display_mode' => 'markers',
      'markers' => $markers,
      'clusters' => [],
      'zone_count' => $zoneCount,
      'capped' => $zoneCount > count($markers),
      'markers_max' => $max,
    ];
  }

  /**
   * Builds grid cluster payload for dense zones.
   *
   * @return array<string, mixed>
   *   Cluster payload.
   */
  private function buildClusterPayload(Request $request, MapBounds $bounds, int $zoneCount, int $max): array {
    $index = Index::load('offers');
    if (!$index) {
      return $this->emptyPayload();
    }

    $fetchLimit = min($zoneCount, self::CLUSTER_FETCH_MAX);
    $query = $index->query();
    $query->range(0, $fetchLimit);
    $this->filterQueryBuilder->apply($query, $request, $bounds);
    $this->filterQueryBuilder->applyListSort($query, $request);
    $query->addCondition('field_geo_lat', NULL, '<>');
    $query->addCondition('field_geo_lng', NULL, '<>');

    try {
      $results = $query->execute();
    }
    catch (\Exception $exception) {
      $this->circuitBreaker?->recordFailure($exception);
      return $this->emptyPayload();
    }

    $points = [];
    $seenNids = [];
    foreach ($results->getResultItems() as $item) {
      if (!$item instanceof ItemInterface) {
        continue;
      }
      $point = $this->extractPointFromItem($item);
      if ($point === NULL) {
        continue;
      }
      $nid = (string) $point['nid'];
      if (isset($seenNids[$nid])) {
        continue;
      }
      $seenNids[$nid] = TRUE;
      $points[] = [
        'lat' => $point['lat'],
        'lng' => $point['lng'],
      ];
    }

    $targetCells = (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('markers_cluster_cells') ?? 64);
    $clusters = $this->gridClusterBuilder->build($points, $bounds, $targetCells);

    return [
      'display_mode' => 'clusters',
      'markers' => [],
      'clusters' => $clusters,
      'zone_count' => $zoneCount,
      'capped' => TRUE,
      'markers_max' => $max,
    ];
  }

  /**
   * Returns an empty marker payload.
   *
   * @return array<string, mixed>
   *   Empty payload.
   */
  private function emptyPayload(): array {
    return [
      'display_mode' => 'markers',
      'markers' => [],
      'clusters' => [],
      'zone_count' => 0,
      'global_count' => 0,
      'capped' => FALSE,
      'markers_max' => $this->resolveMarkersMax(),
    ];
  }

  /**
   * Adds the business-filter total to a marker payload.
   *
   * @param array<string, mixed> $payload
   *   Marker payload without global_count.
   * @param int $globalCount
   *   Total results matching business filters (ignores map bounds).
   *
   * @return array<string, mixed>
   *   Payload with global_count set.
   */
  private function attachGlobalCount(array $payload, int $globalCount): array {
    $payload['global_count'] = $globalCount;
    return $payload;
  }

  /**
   * Whether server-side grid clusters are enabled in map zone settings.
   */
  private function isClusterModeEnabled(): bool {
    return (bool) ($this->configFactory->get('ps_search.map_zone_settings')->get('markers_cluster_enabled') ?? TRUE);
  }

  /**
   * Resolves the configured markers cap for the current request.
   */
  private function resolveMarkersMax(): int {
    $max = (int) ($this->configFactory->get('ps_search.map_zone_settings')->get('markers_max') ?? 500);
    return max(1, min($max, 1000));
  }

  /**
   * Builds a stable cache key from request filters and map bounds.
   */
  private function buildCacheKey(Request $request, int $max): string {
    $params = $request->query->all();
    ksort($params);

    return 'ps_search:markers:' . hash('sha256', json_encode($params, JSON_THROW_ON_ERROR) . ':' . $max);
  }

  /**
   * Extracts marker payload from a Search API result item.
   *
   * @return array{nid: string, lat: float, lng: float, label: string}|null
   *   Marker row or NULL when coordinates are incomplete.
   */
  private function extractMarkerFromItem(ItemInterface $item): ?array {
    $point = $this->extractPointFromItem($item);
    if ($point === NULL) {
      return NULL;
    }

    $budgetRaw = $this->firstFieldValue($item, 'field_budget_value');
    $currencyRaw = $this->firstFieldValue($item, 'field_budget_currency');

    return [
      'nid' => (string) $point['nid'],
      'lat' => $point['lat'],
      'lng' => $point['lng'],
      'label' => $this->markerBuilder->buildPriceLabelFromValues($budgetRaw, $currencyRaw),
    ];
  }

  /**
   * Extracts indexed coordinates from a Search API result item.
   *
   * @return array{nid: int, lat: float, lng: float}|null
   *   Geo point or NULL when coordinates are incomplete.
   */
  private function extractPointFromItem(ItemInterface $item): ?array {
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

  /**
   * Returns the first indexed value for a Search API field.
   */
  private function firstFieldValue(ItemInterface $item, string $fieldId): mixed {
    $field = $item->getField($fieldId);
    if ($field === NULL) {
      return NULL;
    }
    $values = $field->getValues();
    return $values === [] ? NULL : $values[0];
  }

}
