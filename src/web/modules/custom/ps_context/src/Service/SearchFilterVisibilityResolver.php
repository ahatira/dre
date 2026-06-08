<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_context\Entity\PsContextRuleInterface;

/**
 * Resolves search filter bar visibility from the offer context matrix.
 *
 * Maps matrix tab targets (group_surface, group_capacity) to search filters.
 * When no asset type is selected, Surface remains available (search UX).
 */
final class SearchFilterVisibilityResolver {

  private const TAB_SURFACE = 'group_surface';

  private const TAB_CAPACITY = 'group_capacity';

  /**
   * Matrix tab targets exposed to the search filter bar.
   */
  private const SEARCH_TABS = [
    self::TAB_SURFACE,
    self::TAB_CAPACITY,
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  /**
   * Resolves filter visibility for a given asset / operation context.
   *
   * @return array{show_surface: bool, show_capacity: bool, primary_filter: string}
   *   Visibility flags and primary filter key (surface|capacity).
   */
  public function resolve(?string $assetType, ?string $operationType = NULL): array {
    if ($assetType === NULL || $assetType === '') {
      return [
        'show_surface' => TRUE,
        'show_capacity' => FALSE,
        'primary_filter' => 'surface',
      ];
    }

    $tabs = $this->evaluateTabVisibility([
      'field_asset_type' => strtoupper($assetType),
      'field_operation_type' => $operationType ?? '',
      'field_divisible' => '',
    ]);

    $showSurface = $tabs[self::TAB_SURFACE] ?? FALSE;
    $showCapacity = $tabs[self::TAB_CAPACITY] ?? FALSE;

    // Asset codes not covered by seed rules (e.g. LOG) — default to surface.
    if (!$showSurface && !$showCapacity) {
      return [
        'show_surface' => TRUE,
        'show_capacity' => FALSE,
        'primary_filter' => 'surface',
      ];
    }

    return [
      'show_surface' => $showSurface,
      'show_capacity' => $showCapacity,
      'primary_filter' => $showCapacity ? 'capacity' : 'surface',
    ];
  }

  /**
   * Builds a visibility map keyed by asset type code (empty string = no asset).
   *
   * @param list<string> $assetCodes
   *   Asset type codes from SEO mappings.
   *
   * @return array<string, array{show_surface: bool, show_capacity: bool, primary_filter: string}>
   */
  public function buildVisibilityMap(array $assetCodes): array {
    $map = [
      '' => $this->resolve(NULL),
    ];

    foreach ($assetCodes as $code) {
      $map[strtoupper($code)] = $this->resolve($code);
    }

    return $map;
  }

  /**
   * Evaluates matrix rules for tab show/hide actions.
   *
   * @param array<string, string> $fieldValues
   *   Simulated offer field values.
   *
   * @return array<string, bool>
   *   Tab target => visible.
   */
  private function evaluateTabVisibility(array $fieldValues): array {
    $tabs = array_fill_keys(self::SEARCH_TABS, TRUE);

    foreach ($this->loadActiveRules() as $rule) {
      if (!$this->evaluateConditions($rule, $fieldValues)) {
        continue;
      }

      foreach ($rule->getActions() as $action) {
        $target = $action['target'] ?? '';
        if (!isset($tabs[$target])) {
          continue;
        }

        match ($action['action_type'] ?? '') {
          'show_tab' => $tabs[$target] = TRUE,
          'hide_tab' => $tabs[$target] = FALSE,
          default => NULL,
        };
      }
    }

    return $tabs;
  }

  /**
   * @return list<\Drupal\ps_context\Entity\PsContextRuleInterface>
   */
  private function loadActiveRules(): array {
    /** @var \Drupal\ps_context\Entity\PsContextRuleInterface[] $rules */
    $rules = $this->entityTypeManager
      ->getStorage('ps_context_rule')
      ->loadByProperties(['status' => TRUE]);

    uasort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());

    return array_values($rules);
  }

  /**
   * @param array<string, string> $fieldValues
   */
  private function evaluateConditions(PsContextRuleInterface $rule, array $fieldValues): bool {
    $conditions = $rule->getConditions();

    if ($conditions === []) {
      return TRUE;
    }

    $results = array_map(
      fn(array $condition): bool => $this->evaluateCondition($condition, $fieldValues),
      $conditions,
    );

    return $rule->getConditionsLogic() === 'OR'
      ? in_array(TRUE, $results, TRUE)
      : !in_array(FALSE, $results, TRUE);
  }

  /**
   * @param array<string, string> $fieldValues
   */
  private function evaluateCondition(array $condition, array $fieldValues): bool {
    $field_name = $condition['field_name'] ?? '';
    $operator = $condition['operator'] ?? 'equals';
    $expected = (string) ($condition['value'] ?? '');
    $actual = $fieldValues[$field_name] ?? '';

    return match ($operator) {
      'equals' => $actual === $expected,
      'not_equals' => $actual !== $expected,
      'empty' => $actual === '',
      'filled' => $actual !== '',
      'contains' => str_contains($actual, $expected),
      default => FALSE,
    };
  }

}
