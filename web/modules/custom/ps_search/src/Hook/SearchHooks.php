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
   * Implements hook_entity_view().
   */
  #[Hook('entity_view')]
  public function entityView(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, string $view_mode): void {
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'offer') {
      return;
    }

    if (!in_array($view_mode, ['card_search', 'default'], TRUE)) {
      return;
    }

    $build['#attached']['library'][] = 'ps_search/offer_search_tracking';

    if ($view_mode === 'default') {
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
        $group = $search_api_query->createConditionGroup('OR');
        foreach ($terms as $term) {
          $group->addCondition('field_address', $term, '=');
        }

        $search_api_query->addConditionGroup($group);
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
