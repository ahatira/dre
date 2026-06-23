<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Applies More-filters query parameters to a Search API query.
 */
final class MoreCriteriaConditionApplier {

  private const SETTINGS_CONFIG = 'ps_search.feature_filter_sync';

  public function __construct(
    private readonly FeatureSearchFilterRegistry $featureFilterRegistry,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Applies dynamic feature filters and core More-filters to a Search API query.
   */
  public function apply($query, Request $request): void {
    $this->applyFeatureFilters($query, $request);
    $this->applyCoreFilters($query, $request);
  }

  /**
   * Applies only per-feature filters (used after stripping Views conditions).
   */
  public function applyFeatureFiltersOnly($query, Request $request): void {
    $this->applyFeatureFilters($query, $request);
  }

  /**
   * Applies per-feature filters from the registry.
   */
  private function applyFeatureFilters($query, Request $request): void {
    $combine = $this->getFeatureFiltersCombine();
    $groups = [];

    foreach ($this->featureFilterRegistry->getExposedFilters(NULL, FALSE) as $filter) {
      $subGroup = $query->createConditionGroup('AND');
      if ($this->applyFilterToGroup($query, $subGroup, $request, $filter)) {
        $groups[] = $subGroup;
      }
    }

    if ($groups === []) {
      return;
    }

    if ($combine === 'or' && count($groups) > 1) {
      $outer = $query->createConditionGroup('OR');
      foreach ($groups as $group) {
        $outer->addConditionGroup($group);
      }
      $query->addConditionGroup($outer);
      return;
    }

    foreach ($groups as $group) {
      $query->addConditionGroup($group);
    }
  }

  /**
   * Applies one filter definition to a condition group.
   */
  private function applyFilterToGroup($query, $group, Request $request, array $filter): bool {
    $param = (string) $filter['param'];
    $field = (string) $filter['field'];
    $widget = (string) $filter['widget'];

    return match ($widget) {
      'checkbox' => $this->applyCheckboxFilter($group, $request, $param, $field),
      'yes_no' => $this->applyYesNoFilter($group, $request, $param, $field),
      'tags' => $this->applyTagsFilter($query, $group, $request, $param, $field),
      'range' => $this->applyRangeFilter($group, $request, $param, $field, 1000000.0),
      'text', 'select' => $this->applyTextFilter($group, $request, $param, $field),
      'date' => $this->applyDateFilter($group, $request, $param, $field),
      default => FALSE,
    };
  }

  /**
   * Applies core More-filters not driven by feature definitions.
   */
  private function applyCoreFilters($query, Request $request): void {
    foreach (['has_immersive_tour', 'has_video'] as $field) {
      if ($request->query->get($field) === '1') {
        $query->addCondition($field, TRUE);
      }
    }

    $reference = $this->sanitizeText($request->query->get('reference'));
    if ($reference !== NULL) {
      $query->addCondition('field_reference', $reference);
    }

    $transport = $this->sanitizeText($request->query->get('nearby_transport'));
    if ($transport !== NULL) {
      $query->addCondition('nearby_transport', $transport, 'contains');
    }
  }

  /**
   * Returns configured combine mode for active feature filters.
   */
  public function getFeatureFiltersCombine(): string {
    $value = (string) ($this->configFactory->get(self::SETTINGS_CONFIG)->get('feature_filters_combine') ?? 'and');
    return in_array($value, ['and', 'or'], TRUE) ? $value : 'and';
  }

  /**
   * Applies a checkbox (flag) filter to a condition group.
   */
  private function applyCheckboxFilter($group, Request $request, string $param, string $field): bool {
    if ($request->query->get($param) !== '1') {
      return FALSE;
    }
    $group->addCondition($field, TRUE);
    return TRUE;
  }

  /**
   * Applies a yes/no filter to a condition group.
   */
  private function applyYesNoFilter($group, Request $request, string $param, string $field): bool {
    $value = $request->query->get($param);
    if ($value === '1') {
      $group->addCondition($field, TRUE);
      return TRUE;
    }
    if ($value === '0') {
      $group->addCondition($field, FALSE);
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Applies a multi-value tags filter (OR within the same feature).
   */
  private function applyTagsFilter($query, $group, Request $request, string $param, string $field): bool {
    $values = array_values(array_filter(array_map(
      fn(mixed $value): ?string => $this->sanitizeTagValue($value),
      $request->query->all($param),
    )));
    if ($values === []) {
      return FALSE;
    }

    if (count($values) === 1) {
      $group->addCondition($field, $values[0]);
      return TRUE;
    }

    $tagGroup = $query->createConditionGroup('OR');
    foreach ($values as $value) {
      $tagGroup->addCondition($field, $value);
    }
    $group->addConditionGroup($tagGroup);
    return TRUE;
  }

  /**
   * Applies a numeric range filter to a condition group.
   */
  private function applyRangeFilter($group, Request $request, string $param, string $field, float $max): bool {
    $applied = FALSE;
    $min = $this->sanitizePositiveNumber($request->query->get($param . '_min'), $max);
    $maxValue = $this->sanitizePositiveNumber($request->query->get($param . '_max'), $max);
    if ($min !== NULL) {
      $group->addCondition($field, $min, '>=');
      $applied = TRUE;
    }
    if ($maxValue !== NULL) {
      $group->addCondition($field, $maxValue, '<=');
      $applied = TRUE;
    }
    return $applied;
  }

  /**
   * Applies a text filter to a condition group.
   */
  private function applyTextFilter($group, Request $request, string $param, string $field): bool {
    $value = $this->sanitizeText($request->query->get($param));
    if ($value === NULL) {
      return FALSE;
    }
    $group->addCondition($field, $value);
    return TRUE;
  }

  /**
   * Applies a date filter to a condition group.
   */
  private function applyDateFilter($group, Request $request, string $param, string $field): bool {
    $value = $this->sanitizeDate($request->query->get($param));
    if ($value !== NULL) {
      $group->addCondition($field, $value);
      return TRUE;
    }

    $applied = FALSE;
    $min = $this->sanitizeDate($request->query->get($param . '_min'));
    $max = $this->sanitizeDate($request->query->get($param . '_max'));
    if ($min !== NULL) {
      $group->addCondition($field, $min, '>=');
      $applied = TRUE;
    }
    if ($max !== NULL) {
      $group->addCondition($field, $max, '<=');
      $applied = TRUE;
    }
    return $applied;
  }

  /**
   * Sanitizes a free-text query value.
   */
  private function sanitizeText(mixed $value): ?string {
    if (!is_string($value)) {
      return NULL;
    }
    $trimmed = trim($value);
    if ($trimmed === '' || mb_strlen($trimmed) > 255) {
      return NULL;
    }
    return $trimmed;
  }

  /**
   * Sanitizes a tag/dictionary value from the query string.
   */
  private function sanitizeTagValue(mixed $value): ?string {
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^a-zA-Z0-9_\-.]+/', '', $value);
    return ($cleaned !== NULL && $cleaned !== '') ? $cleaned : NULL;
  }

  /**
   * Sanitizes a positive numeric query value.
   */
  private function sanitizePositiveNumber(mixed $value, float $max): ?float {
    if ($value === NULL || $value === '') {
      return NULL;
    }
    if (!is_numeric($value)) {
      return NULL;
    }
    $number = (float) $value;
    if ($number < 0 || $number > $max) {
      return NULL;
    }
    return $number;
  }

  /**
   * Sanitizes an ISO date (YYYY-MM-DD).
   */
  private function sanitizeDate(mixed $value): ?string {
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
      return NULL;
    }
    return $value;
  }

}
