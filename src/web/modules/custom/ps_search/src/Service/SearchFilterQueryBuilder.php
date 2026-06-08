<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies URL search filter parameters to a Search API query.
 */
final class SearchFilterQueryBuilder {

  /**
   * Max accepted server-side bound for surface filters (m²).
   */
  private const MAX_SURFACE = 200000.0;

  /**
   * Max accepted server-side bound for budget filters (€/m²/year).
   */
  private const MAX_BUDGET = 100000.0;

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly MoreCriteriaConditionApplier $moreCriteriaApplier,
  ) {}

  /**
   * Applies search filter query parameters from the request.
   */
  public function apply(QueryInterface $query, Request $request): void {
    $operationType = $this->sanitizeCode($request->query->get('operation_type'));
    $assetType = $this->sanitizeCode($request->query->get('asset_type'));
    $localityTokens = $this->locationSearchFilter->extractTokensFromRequest($request);
    $surfaceMin = $this->sanitizePositiveNumber($request->query->get('surface_min'), self::MAX_SURFACE);
    $surfaceMax = $this->sanitizePositiveNumber($request->query->get('surface_max'), self::MAX_SURFACE);
    $budgetMin = $this->sanitizePositiveNumber($request->query->get('budget_min'), self::MAX_BUDGET);
    $budgetMax = $this->sanitizePositiveNumber($request->query->get('budget_max'), self::MAX_BUDGET);
    $capacityMin = $this->sanitizePositiveNumber($request->query->get('capacity_min'), 500.0);
    $capacityMax = $this->sanitizePositiveNumber($request->query->get('capacity_max'), 500.0);

    if ($operationType !== NULL) {
      $query->addCondition('field_operation_type', $operationType);
    }
    if ($assetType !== NULL) {
      $query->addCondition('field_asset_type', $assetType);
    }
    if ($localityTokens !== []) {
      $this->locationSearchFilter->applyToQuery($query, $localityTokens);
    }
    if ($surfaceMin !== NULL) {
      $query->addCondition('surface_total', $surfaceMin, '>=');
    }
    if ($surfaceMax !== NULL) {
      $query->addCondition('surface_total', $surfaceMax, '<=');
    }
    if ($budgetMin !== NULL) {
      $query->addCondition('field_budget_value', $budgetMin, '>=');
    }
    if ($budgetMax !== NULL) {
      $query->addCondition('field_budget_value', $budgetMax, '<=');
    }
    if ($capacityMin !== NULL) {
      $query->addCondition('field_capacity_total', $capacityMin, '>=');
    }
    if ($capacityMax !== NULL) {
      $query->addCondition('field_capacity_total', $capacityMax, '<=');
    }

    $this->moreCriteriaApplier->apply($query, $request);
  }

  /**
   * Sanitizes a filter code: only A–Z letters, max 10 chars.
   */
  private function sanitizeCode(mixed $value): ?string {
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^A-Z]/i', '', strtoupper(substr($value, 0, 10)));
    return $cleaned !== '' ? $cleaned : NULL;
  }

  /**
   * Sanitizes a positive numeric value.
   */
  private function sanitizePositiveNumber(mixed $value, float $max): ?float {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    $num = filter_var($value, FILTER_VALIDATE_FLOAT);
    if ($num === FALSE || $num < 0) {
      return NULL;
    }
    return min((float) $num, $max);
  }

}
