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

/**
 * Hook implementations for ps_search.
 */
final class SearchHooks {

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
    if ($view->id() !== 'ps_offer_search' || $view->current_display !== 'page_1') {
      return;
    }

    if (!$query instanceof SearchApiQuery) {
      return;
    }

    $request = \Drupal::requestStack()->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $raw = trim((string) $request->query->get('location_multi', ''));
    if ($raw !== '') {
      $terms = array_values(array_filter(array_unique(array_map(
        static fn(string $value): string => trim($value),
        explode(self::LOCATION_MULTI_DELIMITER, $raw)
      ))));

      if ($terms !== []) {
        $search_api_query = $query->getSearchApiQuery();
        $group = $search_api_query->createConditionGroup('OR');
        foreach ($terms as $term) {
          $group->addCondition('field_address', $term, '=');
        }

        $search_api_query->addConditionGroup($group);
      }
    }

    $search_api_query = $query->getSearchApiQuery();
    $surface = (array) $request->query->all('surface');
    $surface_min = isset($surface['min']) && is_scalar($surface['min']) ? (float) $surface['min'] : NULL;
    $surface_max = isset($surface['max']) && is_scalar($surface['max']) ? (float) $surface['max'] : NULL;

    if ($surface_min !== NULL && $surface_min > 0) {
      $search_api_query->addCondition(self::SURFACE_FIELD, $surface_min, '>=');
    }
    if ($surface_max !== NULL && $surface_max > 0) {
      $search_api_query->addCondition(self::SURFACE_FIELD, $surface_max, '<=');
    }

    $price = (array) $request->query->all('price');
    $price_min = isset($price['min']) && is_scalar($price['min']) ? (float) $price['min'] : NULL;
    $price_max = isset($price['max']) && is_scalar($price['max']) ? (float) $price['max'] : NULL;

    if ($price_min !== NULL && $price_min > 0) {
      $search_api_query->addCondition(self::PRICE_FIELD, $price_min, '>=');
    }
    if ($price_max !== NULL && $price_max > 0) {
      $search_api_query->addCondition(self::PRICE_FIELD, $price_max, '<=');
    }

    // -- Ceiling height range. -------------------------------------------------
    $ceiling = (array) $request->query->all('ceiling_height');
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
        (array) $request->query->all($param)
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

    // -- Building condition text filter. ---------------------------------------
    $building_condition = trim((string) $request->query->get('building_condition', ''));
    if ($building_condition !== '') {
      $search_api_query->addCondition(self::BUILDING_CONDITION_FIELD, $building_condition, '=');
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

}
