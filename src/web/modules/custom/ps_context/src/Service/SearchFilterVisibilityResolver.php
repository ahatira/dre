<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\ps_context\Value\OfferContextState;

/**
 * Resolves search filter bar visibility from the offer context matrix.
 *
 * Maps matrix tab targets (group_surface, group_capacity) to search filters.
 * When no asset type is selected, Surface remains available (search UX).
 */
final class SearchFilterVisibilityResolver {

  private const TAB_SURFACE = 'group_surface';

  private const TAB_CAPACITY = 'group_capacity';

  private const TAB_BUDGET = 'group_budget';

  public function __construct(
    private readonly ContextRuleEvaluatorInterface $evaluator,
  ) {}

  /**
   * Resolves filter visibility for a given asset / operation context.
   *
   * @return array{show_surface: bool, show_capacity: bool, show_price: bool, primary_filter: string}
   *   Visibility flags and primary filter key (surface|capacity).
   */
  public function resolve(?string $assetType, ?string $operationType = NULL): array {
    if ($assetType === NULL || $assetType === '') {
      return [
        'show_surface' => TRUE,
        'show_capacity' => FALSE,
        'show_price' => FALSE,
        'primary_filter' => 'surface',
      ];
    }

    $state = $this->evaluator->resolveFromValues([
      'field_asset_type' => strtoupper($assetType),
      'field_operation_type' => $operationType ?? '',
      'field_divisible' => '',
    ]);

    $showSurface = $state->isTabVisible(self::TAB_SURFACE);
    $showCapacity = $state->isTabVisible(self::TAB_CAPACITY);
    $showPrice = $state->isTabVisible(self::TAB_BUDGET);

    // Asset codes not covered by seed rules — default to surface.
    if (!$showSurface && !$showCapacity) {
      return [
        'show_surface' => TRUE,
        'show_capacity' => FALSE,
        'show_price' => $showPrice,
        'primary_filter' => 'surface',
      ];
    }

    return [
      'show_surface' => $showSurface,
      'show_capacity' => $showCapacity,
      'show_price' => $showPrice,
      'primary_filter' => $showCapacity ? 'capacity' : 'surface',
    ];
  }

  /**
   * Builds a visibility map keyed by asset type code (empty string = no asset).
   *
   * @param list<string> $assetCodes
   *   Asset type codes from SEO mappings.
   *
   * @return array<string, array{show_surface: bool, show_capacity: bool, show_price: bool, primary_filter: string}>
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
   * Exposes raw matrix state for a field value set (testing / debugging).
   *
   * @param array<string, string> $fieldValues
   */
  public function resolveState(array $fieldValues): OfferContextState {
    return $this->evaluator->resolveFromValues($fieldValues);
  }

}
