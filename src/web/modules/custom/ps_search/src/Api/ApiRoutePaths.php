<?php

declare(strict_types=1);

namespace Drupal\ps_search\Api;

/**
 * Canonical public HTTP paths for ps_search API endpoints.
 */
final class ApiRoutePaths {

  public const BASE = '/api/ps';

  public const MARKERS = self::BASE . '/markers';

  public const ISOCHRONE = self::BASE . '/isochrone';

  public const COUNT = self::BASE . '/count';

  public const LOCATION_SUGGEST = self::BASE . '/location-suggest';

  public const LOCATION_DATA = self::BASE . '/location-data';

  public const HTMX_COUNT_LABEL = self::BASE . '/htmx/count-label';

  public const HTMX_APPLY_TYPE = self::BASE . '/htmx/apply-type';

  public const HTMX_APPLY_LOCATION = self::BASE . '/htmx/apply-location';

  public const HTMX_APPLY_RANGE_PREFIX = self::BASE . '/htmx/apply-range';

  public const HTMX_APPLY_SURFACE = self::HTMX_APPLY_RANGE_PREFIX . '/surface';

  public const HTMX_APPLY_CAPACITY = self::HTMX_APPLY_RANGE_PREFIX . '/capacity';

  public const HTMX_APPLY_BUDGET = self::HTMX_APPLY_RANGE_PREFIX . '/budget';

  public const HTMX_APPLY_MOBILE = self::BASE . '/htmx/apply-mobile';

  public const HTMX_MORE_CRITERIA = self::BASE . '/htmx/more-criteria';

  public const HTMX_RESULTS_HEADER = self::BASE . '/htmx/results-header';

  /**
   * Maps Drupal route names to rate-limit buckets.
   *
   * @return array<string, string>
   */
  public static function rateLimitBuckets(): array {
    return [
      'ps_search.api.markers' => 'markers',
      'ps_search.api.isochrone' => 'isochrone',
      'ps_search.api.location_suggest' => 'location_suggest',
      'ps_search.api.location_data' => 'location_data',
      'ps_search.api.count' => 'count',
      'ps_search.api.filter_htmx.count_label' => 'htmx',
      'ps_search.api.filter_htmx.apply_type' => 'htmx',
      'ps_search.api.filter_htmx.apply_location' => 'htmx',
      'ps_search.api.filter_htmx.apply_range' => 'htmx',
      'ps_search.api.filter_htmx.apply_mobile' => 'htmx',
      'ps_search.api.filter_htmx.results_header' => 'htmx',
      'ps_search.api.filter_htmx.more_criteria_group' => 'htmx',
    ];
  }

  /**
   * Route names protected by rate limiting and security headers.
   *
   * @return list<string>
   */
  public static function protectedRouteNames(): array {
    return array_keys(self::rateLimitBuckets());
  }

}
