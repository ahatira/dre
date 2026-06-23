<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Filter;

use Drupal\ps_search\Api\ApiRoutePaths;

/**
 * HTMX settings for the search filter bar (Phase 5A infrastructure).
 */
final class FilterBarHtmxSettings {

  public const COUNT_LABEL_ROUTE = ApiRoutePaths::HTMX_COUNT_LABEL;

  public const APPLY_TYPE_ROUTE = ApiRoutePaths::HTMX_APPLY_TYPE;

  public const APPLY_LOCATION_ROUTE = ApiRoutePaths::HTMX_APPLY_LOCATION;

  public const APPLY_SURFACE_ROUTE = ApiRoutePaths::HTMX_APPLY_SURFACE;

  public const APPLY_CAPACITY_ROUTE = ApiRoutePaths::HTMX_APPLY_CAPACITY;

  public const APPLY_BUDGET_ROUTE = ApiRoutePaths::HTMX_APPLY_BUDGET;

  public const MORE_CRITERIA_ROUTE = ApiRoutePaths::HTMX_MORE_CRITERIA;

  public const APPLY_MOBILE_ROUTE = ApiRoutePaths::HTMX_APPLY_MOBILE;

  public const APPLY_MORE_ROUTE = ApiRoutePaths::HTMX_APPLY_MORE;

  public const RESULTS_HEADER_ROUTE = ApiRoutePaths::HTMX_RESULTS_HEADER;

  /**
   * DOM id of the HTMX swap target for the results header.
   */
  public const RESULTS_HEADER_TARGET_ID = 'ps-search-results-header';

  /**
   * Returns drupalSettings payload for the shared HTMX client library.
   *
   * @return array<string, mixed>
   *   Settings consumed by search-filter-htmx.js.
   */
  public function buildJsSettings(): array {
    return [
      'enabled' => TRUE,
      'countUrl' => self::COUNT_LABEL_ROUTE,
      'moreCriteriaGroupUrl' => self::MORE_CRITERIA_ROUTE,
      'resultsHeaderUrl' => self::RESULTS_HEADER_ROUTE,
      'resultsHeaderTargetId' => self::RESULTS_HEADER_TARGET_ID,
      'popins' => [
        'type' => [
          'targetId' => 'ps-filter-type-count-label',
          'openSelector' => '.ps-filter-bar__item--type.show',
          'dropdownClass' => 'ps-filter-bar__item--type',
          'applyUrl' => self::APPLY_TYPE_ROUTE,
        ],
        'location' => [
          'targetId' => 'ps-filter-location-count-label',
          'openSelector' => '.ps-filter-bar__item--location .dropdown-menu.show',
          'dropdownClass' => 'ps-filter-bar__item--location',
          'toggleSelector' => '.js-ps-location-toggle',
          'applyUrl' => self::APPLY_LOCATION_ROUTE,
        ],
        'surface' => [
          'targetId' => 'ps-filter-surface-count-label',
          'openSelector' => '.ps-filter-bar__item--surface.show',
          'dropdownClass' => 'ps-filter-bar__item--surface',
          'applyUrl' => self::APPLY_SURFACE_ROUTE,
        ],
        'capacity' => [
          'targetId' => 'ps-filter-capacity-count-label',
          'openSelector' => '.ps-filter-bar__item--capacity.show',
          'dropdownClass' => 'ps-filter-bar__item--capacity',
          'applyUrl' => self::APPLY_CAPACITY_ROUTE,
        ],
        'budget' => [
          'targetId' => 'ps-filter-budget-count-label',
          'openSelector' => '.ps-filter-bar__item--budget.show',
          'dropdownClass' => 'ps-filter-bar__item--budget',
          'applyUrl' => self::APPLY_BUDGET_ROUTE,
        ],
        'mobile' => [
          'targetId' => 'ps-filter-mobile-count-label',
          'openSelector' => '#ps-mobile-filters.show',
          'offcanvasId' => 'ps-mobile-filters',
          'applyUrl' => self::APPLY_MOBILE_ROUTE,
        ],
        'more' => [
          'targetId' => 'ps-filter-more-count-label',
          'openSelector' => '#ps-more-offcanvas.show',
          'offcanvasId' => 'ps-more-offcanvas',
          'applyUrl' => self::APPLY_MORE_ROUTE,
        ],
      ],
    ];
  }

  /**
   * Resolves a popin config by key.
   *
   * @return array<string, string>|null
   *   Popin config or NULL when unknown.
   */
  public function getPopin(string $key): ?array {
    $settings = $this->buildJsSettings();
    $popin = $settings['popins'][$key] ?? NULL;
    return is_array($popin) ? $popin : NULL;
  }

}
