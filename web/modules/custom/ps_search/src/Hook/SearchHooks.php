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
  }

}
