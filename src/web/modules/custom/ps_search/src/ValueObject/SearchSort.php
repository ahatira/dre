<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * List sort parameters for search results.
 */
final readonly class SearchSort {

  public const DEFAULT_SORT_BY = 'surface_total';

  public const DEFAULT_SORT_ORDER = 'ASC';

  public const DISTANCE_SORT_FIELD = 'field_geo_point__distance';

  public function __construct(
    public string $sortBy,
    public string $sortOrder,
  ) {}

}
