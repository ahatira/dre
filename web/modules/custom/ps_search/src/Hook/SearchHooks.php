<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;
use Drupal\search_api\Plugin\views\query\SearchApiQuery;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ViewExecutable;
use Symfony\Component\HttpFoundation\Request;

/**
 * Hook implementations for ps_search.
 */
final class SearchHooks {

  /**
   * Minimum radius in kilometers for map proximity filtering.
   */
  private const MIN_PROXIMITY_RADIUS_KM = 20.0;

  /**
   * Parses query string values from the raw URL.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   *
   * @return array<string, mixed>
   *   Parsed query values.
   */
  private function getRawQueryValues(Request $request): array {
    $raw_query = (string) $request->server->get('QUERY_STRING', '');
    if ($raw_query === '') {
      return [];
    }

    $values = [];
    parse_str($raw_query, $values);
    return is_array($values) ? $values : [];
  }

  /**
   * Multi-value delimiter used in location query transport.
   */
  private const LOCATION_MULTI_DELIMITER = '||';

  /**
   * Search API field for main surface value.
   */
  private const SURFACE_FIELD = 'ps_offer_surface_main_value';

  /**
   * Search API field for normalized main price value.
   */
  private const PRICE_FIELD = 'ps_offer_price_normalized_main';

  /**
   * Search API field for ceiling height minimum.
   */
  private const CEILING_HEIGHT_MIN_FIELD = 'ps_offer_ceiling_height_min';

  /**
   * Search API field for ceiling height maximum.
   */
  private const CEILING_HEIGHT_MAX_FIELD = 'ps_offer_ceiling_height_max';

  /**
   * Search API string field for accessibility feature labels.
   */
  private const ACCESSIBILITY_FIELD = 'ps_offer_feature_accessibility';

  /**
   * Search API string field for equipment feature labels.
   */
  private const EQUIPMENTS_FIELD = 'ps_offer_feature_equipments';

  /**
   * Search API string field for service feature labels.
   */
  private const SERVICES_FIELD = 'ps_offer_feature_services';

  /**
   * Search API string field for building condition values.
   */
  private const BUILDING_CONDITION_FIELD = 'ps_offer_building_condition';

  /**
   * Search API fulltext field for transport text.
   */
  private const TRANSPORT_TEXT_FIELD = 'ps_offer_transport_text';

  /**
   * Search API boolean field for immersive tour availability.
   */
  private const IMMERSIVE_TOUR_FIELD = 'ps_offer_has_virtual_tour';

  /**
   * Search API boolean field for video availability.
   */
  private const VIDEO_FIELD = 'ps_offer_has_video';

  /**
   * Returns query values for one or more keys, accepting scalar or array input.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   * @param string[] $keys
   *   Query parameter names to try in order.
   *
   * @return array<int, mixed>
   *   Normalized list of values.
   */
  private function getQueryValues(Request $request, array $keys): array {
    foreach ($keys as $key) {
      if ($key === '') {
        continue;
      }

      try {
        $array_value = $request->query->all($key);
        if (is_array($array_value) && $array_value !== []) {
          return array_values($array_value);
        }
      }
      catch (\Throwable) {
        // Fall back to scalar getter when InputBag enforces scalar access.
      }

      try {
        $scalar_value = $request->query->get($key);
      }
      catch (\Throwable) {
        continue;
      }

      if ($scalar_value === NULL || $scalar_value === '') {
        continue;
      }

      return is_array($scalar_value) ? array_values($scalar_value) : [$scalar_value];
    }

    return [];
  }

  /**
   * Returns an associative query array (for [min]/[max] style parameters).
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   * @param string $key
   *   Query key.
   *
   * @return array<string, mixed>
   *   Associative values, or empty array if unavailable.
   */
  private function getQueryMap(Request $request, string $key): array {
    try {
      $value = $request->query->all($key);
      return is_array($value) ? $value : [];
    }
    catch (\Throwable) {
      return [];
    }
  }

  /**
   * Resolves Search API item IDs matching one or more location terms.
   *
   * @param string[] $terms
   *   Normalized location terms.
   *
    * @return string[]
    *   Matching indexed item IDs (entity:node/{nid}:{langcode}).
   */
  private function resolveOfferItemIdsByLocationTerms(array $terms): array {
    $normalized_terms = array_values(array_filter(array_map(
      static fn(string $value): string => trim($value),
      $terms
    )));

    if ($normalized_terms === []) {
      return [];
    }

    $database = \Drupal::database();
    $select = $database->select('node__field_address', 'fa');
    $select->join('node_field_data', 'n', 'n.nid = fa.entity_id');
    $select->addExpression("CONCAT('entity:node/', fa.entity_id, ':', n.langcode)", 'item_id');
    $select->distinct();
    $select->condition('n.status', 1);
    $select->condition('n.type', 'offer');

    $or = $select->orConditionGroup();
    $searchable_fields = [
      'fa.field_address_locality',
      'fa.field_address_postal_code',
      'fa.field_address_administrative_area',
      'fa.field_address_dependent_locality',
    ];

    foreach ($normalized_terms as $term) {
      $like = '%' . $database->escapeLike($term) . '%';
      $term_group = $select->orConditionGroup()
        ->condition('fa.field_address_locality', $like, 'LIKE')
        ->condition('fa.field_address_postal_code', $like, 'LIKE')
        ->condition('fa.field_address_administrative_area', $like, 'LIKE')
        ->condition('fa.field_address_dependent_locality', $like, 'LIKE');

      // Also match combined labels by requiring each token somewhere in the
      // address row (postal + locality, locality + district, full line...).
      $tokens = array_values(array_filter(array_map(
        static fn(string $value): string => trim($value),
        preg_split('/[^\pL\pN]+/u', $term) ?: []
      )));

      if (count($tokens) > 1) {
        $token_group = $select->andConditionGroup();
        foreach ($tokens as $token) {
          $token_like = '%' . $database->escapeLike($token) . '%';
          $token_or = $select->orConditionGroup();
          foreach ($searchable_fields as $field) {
            $token_or->condition($field, $token_like, 'LIKE');
          }
          $token_group->condition($token_or);
        }
        $term_group->condition($token_group);
      }

      $or->condition($term_group);
    }

    $select->condition($or);

    return array_values(array_unique(array_map(
      static fn(string $value): string => trim($value),
      $select->execute()->fetchCol()
    )));
  }

  /**
   * Resolves Search API item IDs within a radius from a geographic point.
   *
   * @param float $latitude
   *   Center latitude in decimal degrees.
   * @param float $longitude
   *   Center longitude in decimal degrees.
   * @param float $radiusKm
   *   Radius in kilometers.
   *
   * @return string[]
   *   Matching indexed item IDs (entity:node/{nid}:{langcode}).
   */
  private function resolveOfferItemIdsByProximity(float $latitude, float $longitude, float $radiusKm): array {
    if ($radiusKm <= 0) {
      return [];
    }

    $database = \Drupal::database();
    $select = $database->select('node__field_geofield', 'fg');
    $select->join('node_field_data', 'n', 'n.nid = fg.entity_id');
    $select->addExpression("CONCAT('entity:node/', fg.entity_id, ':', n.langcode)", 'item_id');
    $select->distinct();
    $select->condition('n.status', 1);
    $select->condition('n.type', 'offer');
    $select->condition('fg.field_geofield_value', NULL, 'IS NOT NULL');

    // Haversine distance in kilometers from geofield WKT POINT(lon lat).
    $point_lat = 'ST_Y(ST_GeomFromText(fg.field_geofield_value))';
    $point_lon = 'ST_X(ST_GeomFromText(fg.field_geofield_value))';
    $distance_expression = '(6371 * ACOS(LEAST(1, '
      . 'COS(RADIANS(:center_lat)) * COS(RADIANS(' . $point_lat . ')) '
      . '* COS(RADIANS(' . $point_lon . ') - RADIANS(:center_lon)) '
      . '+ SIN(RADIANS(:center_lat)) * SIN(RADIANS(' . $point_lat . '))'
      . ')))';
    $select->addExpression($distance_expression, 'distance_km', [
      ':center_lat' => $latitude,
      ':center_lon' => $longitude,
    ]);
    $select->havingCondition('distance_km', $radiusKm, '<=');

    return array_values(array_unique(array_map(
      static fn(string $value): string => trim($value),
      $select->execute()->fetchCol()
    )));
  }

  /**
   * Returns a finite float from query values for one or more parameter keys.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Current request.
   * @param string[] $keys
   *   Query parameter names to try in order.
   *
   * @return float|null
   *   Parsed float value, or NULL when missing/invalid.
   */
  private function getQueryFloat(Request $request, array $keys): ?float {
    foreach ($keys as $key) {
      try {
        $raw = $request->query->get($key);
      }
      catch (\Throwable) {
        continue;
      }

      if ($raw === NULL || $raw === '') {
        continue;
      }

      if (!is_scalar($raw)) {
        continue;
      }

      $value = filter_var((string) $raw, FILTER_VALIDATE_FLOAT);
      if ($value === FALSE) {
        continue;
      }

      $float = (float) $value;
      if (!is_finite($float)) {
        continue;
      }

      return $float;
    }

    return NULL;
  }

  /**
   * Implements hook_entity_view().
   */
  #[Hook('entity_view')]
  public function entityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, string $view_mode): void {
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }

    if (!in_array($view_mode, ['card_search', 'default', 'full'], TRUE)) {
      return;
    }

    $build['#attached']['library'][] = 'ps_search/offer_search_tracking';

    if (in_array($view_mode, ['default', 'full'], TRUE)) {
      $build['#attached']['drupalSettings']['psSearchCardSearch']['viewedOfferIds'] = [(int) $entity->id()];
    }
  }

  /**
   * Implements hook_page_attachments().
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    $route_name = \Drupal::routeMatch()->getRouteName();
    if ($route_name !== 'view.ps_offer_search.page_1') {
      return;
    }

    $attachments['#attached']['library'][] = 'ps_search/offer_search_tracking';
    $attachments['#attached']['library'][] = 'ui_suite_bnppre/map_proximity_control';
    $attachments['#attached']['drupalSettings']['psSearch']['locationAutocompleteEndpoint'] = Url::fromRoute('ps_search.location_autocomplete')->toString();
  }

  /**
   * Implements hook_views_query_alter().
   */
  #[Hook('views_query_alter')]
  public function viewsQueryAlter(ViewExecutable $view, QueryPluginBase $query): void {
    if ($view->id() !== 'ps_offer_search' || !in_array($view->current_display, ['page_1', 'attachment_1'], TRUE)) {
      return;
    }

    if (!$query instanceof SearchApiQuery) {
      return;
    }

    $request = \Drupal::requestStack()->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $search_api_query = $query->getSearchApiQuery();

    $raw = trim((string) $request->query->get('location_multi', ''));
    if ($raw !== '') {
      $terms = array_values(array_filter(array_unique(array_map(
        static fn(string $value): string => trim($value),
        explode(self::LOCATION_MULTI_DELIMITER, $raw)
      ))));

      if ($terms !== []) {
        $item_ids = $this->resolveOfferItemIdsByLocationTerms($terms);
        if ($item_ids === []) {
          $search_api_query->addCondition('search_api_id', '__no_match__', '=');
        }
        else {
          $search_api_query->addCondition('search_api_id', $item_ids, 'IN');
        }
      }
    }

    // -- Geographic proximity filter (point + radius in km). ------------------
    $nearby_lat = $this->getQueryFloat($request, ['nearby_lat', 'lat']);
    $nearby_lon = $this->getQueryFloat($request, ['nearby_lon', 'nearby_lng', 'lng', 'lon']);
    $nearby_radius_km = $this->getQueryFloat($request, ['nearby_radius_km', 'nearby_radius', 'radius']);

    if ($nearby_lat !== NULL && $nearby_lon !== NULL) {
      $effective_radius_km = self::MIN_PROXIMITY_RADIUS_KM;
      if ($nearby_radius_km !== NULL && $nearby_radius_km > 0) {
        $effective_radius_km = max(self::MIN_PROXIMITY_RADIUS_KM, $nearby_radius_km);
      }
      $item_ids = $this->resolveOfferItemIdsByProximity($nearby_lat, $nearby_lon, $effective_radius_km);
      if ($item_ids === []) {
        $search_api_query->addCondition('search_api_id', '__no_match__', '=');
      }
      else {
        $search_api_query->addCondition('search_api_id', $item_ids, 'IN');
      }
    }

    // -- Property type filter (normalize UI uppercase to index lowercase). -----
    $property_types = array_values(array_filter(array_map(
      static fn(mixed $v): string => strtolower(trim((string) $v)),
      $this->getQueryValues($request, ['ps_property_type', 'property_type'])
    )));
    if ($property_types !== []) {
      $group = $search_api_query->createConditionGroup('OR');
      foreach ($property_types as $type) {
        $group->addCondition('field_property_type', $type, '=');
      }
      $search_api_query->addConditionGroup($group);
    }

    // -- Transaction type filter (normalize UI uppercase to index lowercase). --
    $transaction_types = array_values(array_filter(array_map(
      static fn(mixed $v): string => strtolower(trim((string) $v)),
      $this->getQueryValues($request, ['ps_transaction_type', 'transaction_type', 'transaction'])
    )));
    if ($transaction_types !== []) {
      $group = $search_api_query->createConditionGroup('OR');
      foreach ($transaction_types as $type) {
        $group->addCondition('field_transaction_types', $type, '=');
      }
      $search_api_query->addConditionGroup($group);
    }
    $surface = $this->getQueryMap($request, 'surface');
    $surface_min = isset($surface['min']) && is_scalar($surface['min']) ? (float) $surface['min'] : NULL;
    $surface_max = isset($surface['max']) && is_scalar($surface['max']) ? (float) $surface['max'] : NULL;

    if ($surface_min !== NULL && $surface_min > 0) {
      $search_api_query->addCondition(self::SURFACE_FIELD, $surface_min, '>=');
    }
    if ($surface_max !== NULL && $surface_max > 0) {
      $search_api_query->addCondition(self::SURFACE_FIELD, $surface_max, '<=');
    }

    $price = $this->getQueryMap($request, 'price');
    $price_min = isset($price['min']) && is_scalar($price['min']) ? (float) $price['min'] : NULL;
    $price_max = isset($price['max']) && is_scalar($price['max']) ? (float) $price['max'] : NULL;

    if ($price_min !== NULL && $price_min > 0) {
      $search_api_query->addCondition(self::PRICE_FIELD, $price_min, '>=');
    }
    if ($price_max !== NULL && $price_max > 0) {
      $search_api_query->addCondition(self::PRICE_FIELD, $price_max, '<=');
    }

    // -- Ceiling height range. -------------------------------------------------
    $ceiling = $this->getQueryMap($request, 'ceiling_height');
    $ceiling_min = isset($ceiling['min']) && is_scalar($ceiling['min']) && (float) $ceiling['min'] > 0 ? (float) $ceiling['min'] : NULL;
    $ceiling_max = isset($ceiling['max']) && is_scalar($ceiling['max']) && (float) $ceiling['max'] > 0 ? (float) $ceiling['max'] : NULL;

    if ($ceiling_min !== NULL) {
      $search_api_query->addCondition(self::CEILING_HEIGHT_MIN_FIELD, $ceiling_min, '>=');
    }
    if ($ceiling_max !== NULL) {
      $search_api_query->addCondition(self::CEILING_HEIGHT_MAX_FIELD, $ceiling_max, '<=');
    }

    // -- Feature checkbox filters (accessibility / equipments / services). -----
    $featureFields = [
      'accessibility' => self::ACCESSIBILITY_FIELD,
      'equipments' => self::EQUIPMENTS_FIELD,
      'services' => self::SERVICES_FIELD,
    ];
    foreach ($featureFields as $param => $field) {
      $values = array_values(array_filter(array_map(
        static fn(mixed $v): string => trim((string) $v),
        $this->getQueryValues($request, [$param])
      )));
      if ($values === []) {
        continue;
      }
      $group = $search_api_query->createConditionGroup('OR');
      foreach ($values as $value) {
        $group->addCondition($field, $value, '=');
      }
      $search_api_query->addConditionGroup($group);
    }

    // -- Building condition filter. --------------------------------------------
    $building_conditions = array_values(array_filter(array_map(
      static fn(mixed $v): string => trim((string) $v),
      $this->getQueryValues($request, ['building_condition'])
    )));
    if ($building_conditions === []) {
      $building_condition = trim((string) $request->query->get('building_condition', ''));
      if ($building_condition !== '') {
        $building_conditions[] = $building_condition;
      }
    }
    if ($building_conditions !== []) {
      $group = $search_api_query->createConditionGroup('OR');
      foreach ($building_conditions as $building_condition) {
        $group->addCondition(self::BUILDING_CONDITION_FIELD, $building_condition, '=');
      }
      $search_api_query->addConditionGroup($group);
    }

    // -- Boolean toggles: immersive tour / video. ------------------------------
    // Apply only when explicitly present in query string to avoid default
    // values silently filtering page_1 while attachment_1 remains unfiltered.
    $raw_query_values = $this->getRawQueryValues($request);

    $immersive_enabled_in_query = array_key_exists('immersive_tour_enabled', $raw_query_values);
    if ($immersive_enabled_in_query) {
      $immersive_values = array_map(
        static fn(mixed $v): string => strtolower(trim((string) $v)),
        $this->getQueryValues($request, ['immersive_tour_enabled'])
      );
      if (array_intersect($immersive_values, ['1', 'true', 'on']) !== []) {
        $search_api_query->addCondition(self::IMMERSIVE_TOUR_FIELD, 1, '=');
      }
    }

    $video_enabled_in_query = array_key_exists('video_enabled', $raw_query_values);
    if ($video_enabled_in_query) {
      $video_values = array_map(
        static fn(mixed $v): string => strtolower(trim((string) $v)),
        $this->getQueryValues($request, ['video_enabled'])
      );
      if (array_intersect($video_values, ['1', 'true', 'on']) !== []) {
        $search_api_query->addCondition(self::VIDEO_FIELD, 1, '=');
      }
    }

    // -- Nearby transport fulltext filter. -------------------------------------
    $nearby_transport = trim((string) $request->query->get('nearby_transport', ''));
    if ($nearby_transport !== '') {
      $search_api_query->setFulltextFields([self::TRANSPORT_TEXT_FIELD]);
      $keys = $search_api_query->getKeys();
      if ($keys !== NULL && $keys !== [] && $keys !== '') {
        // Merge with existing fulltext.
        $combined = (is_array($keys) ? implode(' ', array_filter((array) $keys)) : (string) $keys) . ' ' . $nearby_transport;
        $search_api_query->keys(trim($combined));
      }
      else {
        $search_api_query->keys($nearby_transport);
      }
    }
  }

  /**
   * Implements hook_views_pre_view().
   */
  #[Hook('views_pre_view')]
  public function viewsPreView(ViewExecutable $view, string $display_id, array &$args): void {
    if ($view->id() !== 'ps_offer_search' || !in_array($display_id, ['page_1', 'attachment_1'], TRUE)) {
      return;
    }

    $request = \Drupal::requestStack()->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $raw_query_values = $this->getRawQueryValues($request);
    $input = $view->getExposedInput();

    if (!array_key_exists('immersive_tour_enabled', $raw_query_values)) {
      $input['immersive_tour'] = 'All';
    }

    if (!array_key_exists('video_enabled', $raw_query_values)) {
      $input['video'] = 'All';
    }

    $view->setExposedInput($input);
  }

}
