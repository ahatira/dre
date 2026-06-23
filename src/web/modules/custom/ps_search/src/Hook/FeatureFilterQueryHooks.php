<?php

declare(strict_types=1);

namespace Drupal\ps_search\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_search\Service\FeatureSearchFilterRegistry;
use Drupal\ps_search\Service\MoreCriteriaConditionApplier;
use Drupal\ps_search\Service\SearchViewsQueryGate;
use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\ConditionInterface;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Re-applies per-feature filters when combine mode is OR.
 *
 * Views exposed filters always combine with AND. When the business configures
 * feature_filters_combine=or, strip dynamic feature_* conditions from the
 * Views query and re-apply them as an OR group via MoreCriteriaConditionApplier.
 */
final class FeatureFilterQueryHooks {

  public function __construct(
    private readonly MoreCriteriaConditionApplier $moreCriteriaApplier,
    private readonly FeatureSearchFilterRegistry $featureFilterRegistry,
    private readonly RequestStack $requestStack,
    private readonly SearchViewsQueryGate $viewsQueryGate,
  ) {}

  /**
   * Implements hook_search_api_query_alter().
   */
  #[Hook('search_api_query_alter')]
  public function searchApiQueryAlter(QueryInterface $query): void {
    if ($this->viewsQueryGate->isHandledByContextQuery($query)) {
      return;
    }

    if (!str_contains((string) $query->getSearchId(), 'ps_search_offers')) {
      return;
    }

    if ($this->moreCriteriaApplier->getFeatureFiltersCombine() === 'and') {
      return;
    }

    $request = $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return;
    }

    $dynamicFields = $this->getDynamicFeatureFieldNames();
    $this->stripFeatureConditions($query->getConditionGroup(), $dynamicFields);
    $this->moreCriteriaApplier->applyFeatureFiltersOnly($query, $request);
  }

  /**
   * Collects indexed field names for exposed per-feature filters.
   *
   * @return list<string>
   */
  private function getDynamicFeatureFieldNames(): array {
    $fields = [];
    foreach ($this->featureFilterRegistry->getExposedFilters(NULL, FALSE) as $filter) {
      $fields[] = (string) $filter['field'];
    }
    return $fields;
  }

  /**
   * Removes per-feature conditions added by Views exposed filters.
   *
   * @param list<string> $fieldNames
   *   Field names to strip.
   */
  private function stripFeatureConditions(ConditionGroupInterface $group, array $fieldNames): void {
    $conditions = &$group->getConditions();
    foreach ($conditions as $key => $condition) {
      if ($condition instanceof ConditionGroupInterface) {
        $this->stripFeatureConditions($condition, $fieldNames);
        if ($condition->isEmpty()) {
          unset($conditions[$key]);
        }
        continue;
      }
      if ($condition instanceof ConditionInterface) {
        $field = (string) $condition->getField();
        if (in_array($field, $fieldNames, TRUE) || str_starts_with($field, 'feature_')) {
          unset($conditions[$key]);
        }
      }
    }
    $conditions = array_values($conditions);
  }

}
