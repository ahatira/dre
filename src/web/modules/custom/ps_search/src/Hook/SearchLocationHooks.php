<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Service\LocationCentroidResolver;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\ConditionInterface;
use Drupal\search_api\Query\QueryInterface;
use Drupal\views\ViewExecutable;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Location filtering and map settings for the search view.
 */
final class SearchLocationHooks {

  /**
   * Views filter identifiers replaced by unified location query logic.
   */
  private const LOCATION_FILTER_IDS = [
    'field_address_locality',
    'field_address_postal_code',
    'field_address_admin_area',
  ];

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly LocationCentroidResolver $locationCentroidResolver,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Implements hook_search_api_query_alter().
   */
  #[Hook('search_api_query_alter')]
  public function searchApiQueryAlter(QueryInterface $query): void {
    $searchId = (string) $query->getSearchId();
    if (!str_contains($searchId, 'ps_search_offers')) {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $tokens = $this->locationSearchFilter->extractTokensFromRequest($request);
    if ($tokens === []) {
      return;
    }

    $this->stripAddressConditions($query->getConditionGroup());
    $this->locationSearchFilter->applyToQuery($query, $tokens);
  }

  /**
   * Implements hook_preprocess_HOOK() for views_view.
   */
  #[Hook('preprocess_views_view')]
  public function preprocessViewsView(array &$variables): void {
    $view = $variables['view'] ?? NULL;
    if (!$view instanceof ViewExecutable || $view->id() !== 'ps_search_offers') {
      return;
    }

    if (($variables['display_id'] ?? '') !== 'page_list') {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $locationMap = $this->locationCentroidResolver->resolveFromRequest($request);
    if ($locationMap === NULL || $locationMap['lat'] === NULL || $locationMap['lng'] === NULL) {
      return;
    }

    $variables['#attached']['drupalSettings']['psSearch']['locationMap'] = $locationMap;
  }

  /**
   * Removes default Views address filters replaced by unified token logic.
   */
  private function stripAddressConditions(ConditionGroupInterface $group): void {
    $conditions = &$group->getConditions();
    foreach ($conditions as $key => $condition) {
      if ($condition instanceof ConditionGroupInterface) {
        $this->stripAddressConditions($condition);
        if ($condition->isEmpty()) {
          unset($conditions[$key]);
        }
        continue;
      }
      if ($condition instanceof ConditionInterface && in_array($condition->getField(), self::LOCATION_FILTER_IDS, TRUE)) {
        unset($conditions[$key]);
      }
    }
    $conditions = array_values($conditions);
  }

}
