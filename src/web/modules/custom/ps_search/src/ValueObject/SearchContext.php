<?php

declare(strict_types=1);

namespace Drupal\ps_search\ValueObject;

/**
 * Immutable search context — single source of truth per HTTP request.
 */
final readonly class SearchContext {

  /**
   * Request attribute key used by the resolver subscriber.
   */
  public const REQUEST_ATTRIBUTE = '_ps_search_context';

  public function __construct(
    public ?GeoContext $geo,
    public SearchFilters $filters,
    public SpatialConstraint $spatial,
    public SearchSort $sort,
    public string $langcode,
    public string $countryCode,
    public bool $locationRequired,
    public bool $isValid,
    public ?string $invalidReason,
  ) {}

}
