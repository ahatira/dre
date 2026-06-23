<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\ps_search\Service\MoreCriteriaConditionApplier;
use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\ps_search\ValueObject\RangeFilter;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Applies normalized SearchFilters from SearchContext to Search API queries.
 */
final class SearchBusinessFilterApplier {

  public function __construct(
    private readonly SearchContentLanguageResolver $contentLanguageResolver,
    private readonly MoreCriteriaConditionApplier $moreCriteriaApplier,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Applies business filters from the search context (no spatial constraints).
   */
  public function apply(QueryInterface $query, SearchContext $context): void {
    $this->applyLanguageFilter($query, $context);
    $this->applyFilters($query, $context->filters);

    $request = $this->requestStack->getCurrentRequest();
    if ($request !== NULL && $context->filters->moreCriteria === []) {
      $this->moreCriteriaApplier->apply($query, $request);
    }
  }

  private function applyLanguageFilter(QueryInterface $query, SearchContext $context): void {
    $langcode = $context->langcode;
    if ($langcode === '') {
      return;
    }

    $query->addCondition('langcode', $langcode);
  }

  private function applyFilters(QueryInterface $query, SearchFilters $filters): void {
    if ($filters->operationType !== NULL) {
      $query->addCondition('field_operation_type', $filters->operationType);
    }
    if ($filters->assetType !== NULL) {
      $query->addCondition('field_asset_type', $filters->assetType);
    }

    $this->applyRange($query, 'surface_total', $filters->surface);
    $this->applyRange($query, 'field_budget_value', $filters->budget);
    $this->applyRange($query, 'field_capacity_total', $filters->capacity);
  }

  private function applyRange(QueryInterface $query, string $field, ?RangeFilter $range): void {
    if ($range === NULL || $range->isEmpty()) {
      return;
    }
    if ($range->min !== NULL) {
      $query->addCondition($field, $range->min, '>=');
    }
    if ($range->max !== NULL) {
      $query->addCondition($field, $range->max, '<=');
    }
  }

}
