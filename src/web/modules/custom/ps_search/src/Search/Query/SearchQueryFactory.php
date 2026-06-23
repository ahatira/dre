<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\ps_search\Contract\SearchQueryFactoryInterface;
use Drupal\ps_search\Contract\SearchQueryExecutorInterface;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\MoreCriteriaConditionApplier;
use Drupal\ps_search\Service\SearchContentLanguageResolver;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\search_api\Entity\Index;
use Drupal\search_api\Query\QueryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Central factory for Search API queries used by Views hooks and future API.
 */
final class SearchQueryFactory implements SearchQueryFactoryInterface {

  /**
   * Search API index ID for property offers.
   */
  private const INDEX_ID = 'offers';

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
    private readonly SearchContentLanguageResolver $contentLanguageResolver,
    private readonly SearchListSortApplier $sortApplier,
    private readonly SearchQueryExecutorInterface $queryExecutor,
    private readonly SearchEngineSettingsReader $engineSettings,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function createQuery(Request $request, ?MapBounds $bounds = NULL): QueryInterface {
    $index = Index::load(self::INDEX_ID);
    if (!$index) {
      throw new \RuntimeException(sprintf('Search API index "%s" is not available.', self::INDEX_ID));
    }

    $query = $index->query();
    $this->apply($query, $request, $bounds);

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function applyBusinessFilters(QueryInterface $query, Request $request): void {
    if ($this->applyFromSearchContext($query, $request, FALSE, NULL)) {
      return;
    }

    $this->applyLegacyBusinessFilters($query, $request);
  }

  /**
   * {@inheritdoc}
   */
  public function applyListSort(QueryInterface $query, Request $request): void {
    $context = $this->getSearchContext($request);
    if ($context instanceof SearchContext && $this->engineSettings->isSearchContextEnabled()) {
      $this->queryExecutor->applyListSort($query, $context);
      return;
    }

    $this->sortApplier->apply($query, $request);
  }

  /**
   * {@inheritdoc}
   */
  public function applyMapBounds(QueryInterface $query, MapBounds $bounds): void {
    $query->addCondition('field_geo_lat', $bounds->swLat, '>=');
    $query->addCondition('field_geo_lat', $bounds->neLat, '<=');
    $query->addCondition('field_geo_lng', $bounds->swLng, '>=');
    $query->addCondition('field_geo_lng', $bounds->neLng, '<=');
  }

  /**
   * {@inheritdoc}
   */
  public function apply(QueryInterface $query, Request $request, ?MapBounds $bounds = NULL): void {
    if ($this->applyFromSearchContext($query, $request, TRUE, $bounds)) {
      return;
    }

    $this->applyLegacyBusinessFilters($query, $request);
    if ($bounds instanceof MapBounds) {
      $this->applyMapBounds($query, $bounds);
    }
  }

  /**
   * Applies SearchContext-driven filters when the v2 feature flag is enabled.
   */
  private function applyFromSearchContext(
    QueryInterface $query,
    Request $request,
    bool $withSpatial,
    ?MapBounds $bounds,
  ): bool {
    $context = $this->getSearchContext($request);
    if (!$context instanceof SearchContext || !$this->engineSettings->isSearchContextEnabled()) {
      return FALSE;
    }

    $this->queryExecutor->applyBusinessFilters($query, $context);
    if ($withSpatial) {
      $legacyBounds = $this->queryExecutor->shouldSkipLegacyMapBounds($context)
        ? NULL
        : $bounds;
      $this->queryExecutor->applySpatial($query, $context, $legacyBounds);
    }

    return TRUE;
  }

  /**
   * Legacy business filter path (LocationSearchFilter + query params).
   */
  private function applyLegacyBusinessFilters(QueryInterface $query, Request $request): void {
    $this->applyContentLanguageFilter($query, $request);

    $operationType = $this->queryFacetCode($request, 'operation_type');
    $assetType = $this->queryFacetCode($request, 'asset_type');
    $localityTokens = $this->locationSearchFilter->extractTokensFromRequest($request);
    $surfaceMin = $this->queryRangeBound($request, 'surface', 'min', 'surface_min', self::MAX_SURFACE);
    $surfaceMax = $this->queryRangeBound($request, 'surface', 'max', 'surface_max', self::MAX_SURFACE);
    $budgetMin = $this->queryRangeBound($request, 'budget', 'min', 'budget_min', self::MAX_BUDGET);
    $budgetMax = $this->queryRangeBound($request, 'budget', 'max', 'budget_max', self::MAX_BUDGET);
    $capacityMin = $this->queryRangeBound($request, 'capacity', 'min', 'capacity_min', 500.0);
    $capacityMax = $this->queryRangeBound($request, 'capacity', 'max', 'capacity_max', 500.0);

    if ($operationType !== NULL) {
      $query->addCondition('field_operation_type', $operationType);
    }
    if ($assetType !== NULL) {
      $query->addCondition('field_asset_type', $assetType);
    }
    if ($localityTokens !== [] && $this->shouldApplyLegacyLocationFilter($request)) {
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

  private function getSearchContext(Request $request): ?SearchContext {
    $context = $request->attributes->get(SearchContext::REQUEST_ATTRIBUTE);
    return $context instanceof SearchContext ? $context : NULL;
  }

  private function shouldApplyLegacyLocationFilter(Request $request): bool {
    if (!$this->engineSettings->isSearchContextEnabled()) {
      return TRUE;
    }
    if ($this->engineSettings->isLegacyLocationFilterEnabled()) {
      return TRUE;
    }

    return $this->getSearchContext($request)?->geo === NULL;
  }

  /**
   * Restricts results to the resolved content language (strict, no cross-lang fallback).
   *
   * Public API routes (/api/ps/*) have no language prefix; callers pass ?lang=
   * from drupalSettings.path.currentLanguage so markers match the search page.
   */
  private function applyContentLanguageFilter(QueryInterface $query, Request $request): void {
    $langcodes = $this->contentLanguageResolver->resolveSearchLangcodes($request);
    if ($langcodes === []) {
      return;
    }

    if (count($langcodes) === 1) {
      $query->addCondition('langcode', $langcodes[0]);
      return;
    }

    $group = $query->createConditionGroup('OR');
    foreach ($langcodes as $langcode) {
      $group->addCondition('langcode', $langcode);
    }
    $query->addConditionGroup($group);
  }

  /**
   * Reads a facet filter code from scalar or Views/Facets bracket params.
   */
  private function queryFacetCode(Request $request, string $key): ?string {
    $value = $this->readQueryParameter($request, $key);
    if (is_string($value) && $value !== '') {
      return $this->sanitizeCode($value);
    }

    if (!is_array($value) || $value === []) {
      return NULL;
    }

    foreach ($value as $code => $facetValue) {
      if (is_string($code) && $code !== '') {
        $sanitized = $this->sanitizeCode($code);
        if ($sanitized !== NULL) {
          return $sanitized;
        }
      }
      if (is_string($facetValue) && $facetValue !== '') {
        $sanitized = $this->sanitizeCode($facetValue);
        if ($sanitized !== NULL) {
          return $sanitized;
        }
      }
    }

    return NULL;
  }

  /**
   * Reads a numeric range bound from API or BEF bracket query params.
   */
  private function queryRangeBound(
    Request $request,
    string $befKey,
    string $bound,
    string $flatKey,
    float $max,
  ): ?float {
    $flatRaw = $this->readQueryParameter($request, $flatKey);
    if (is_string($flatRaw) || is_int($flatRaw) || is_float($flatRaw)) {
      $flatValue = $this->sanitizePositiveNumber($flatRaw, $max);
      if ($flatValue !== NULL) {
        return $flatValue;
      }
    }

    $nested = $this->readQueryParameter($request, $befKey);
    if (!is_array($nested) || !array_key_exists($bound, $nested)) {
      return NULL;
    }

    return $this->sanitizePositiveNumber($nested[$bound], $max);
  }

  /**
   * Reads a scalar or array query parameter without InputBag type exceptions.
   */
  private function readQueryParameter(Request $request, string $key): mixed {
    if (!$request->query->has($key)) {
      return NULL;
    }

    try {
      return $request->query->all($key);
    }
    catch (BadRequestException) {
      return $request->query->getString($key);
    }
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
