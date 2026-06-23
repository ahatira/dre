<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\search_api\Query\ConditionGroupInterface;
use Drupal\search_api\Query\ConditionInterface;
use Drupal\search_api\Query\QueryInterface;

/**
 * Removes Search API conditions by indexed field name (recursive).
 */
final class SearchQueryConditionStripper {

  /**
   * Fields stripped when replacing legacy locality with SearchContext spatial.
   *
   * @var list<string>
   */
  public const LOCATION_FIELDS = [
    'field_address_locality',
    'field_address_admin_area',
    'field_address_postal_code',
  ];

  /**
   * @param list<string> $fieldNames
   */
  public function stripFields(QueryInterface $query, array $fieldNames): void {
    if ($fieldNames === []) {
      return;
    }

    $this->stripFieldsFromGroup($query->getConditionGroup(), $fieldNames);
  }

  /**
   * @param list<string> $fieldNames
   */
  private function stripFieldsFromGroup(ConditionGroupInterface $group, array $fieldNames): void {
    $conditions = &$group->getConditions();
    foreach ($conditions as $key => $condition) {
      if ($condition instanceof ConditionGroupInterface) {
        $this->stripFieldsFromGroup($condition, $fieldNames);
        if ($condition->isEmpty()) {
          unset($conditions[$key]);
        }
        continue;
      }

      if ($condition instanceof ConditionInterface && in_array($condition->getField(), $fieldNames, TRUE)) {
        unset($conditions[$key]);
      }
    }

    $conditions = array_values($conditions);
  }

}
