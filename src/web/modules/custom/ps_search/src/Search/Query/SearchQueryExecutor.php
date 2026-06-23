<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Query;

use Drupal\ps_search\Contract\SearchQueryExecutorInterface;
use Drupal\ps_search\ValueObject\MapBounds;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SpatialMode;
use Drupal\search_api\Query\QueryInterface;

/**
 * Central executor applying SearchContext to Search API queries.
 */
final class SearchQueryExecutor implements SearchQueryExecutorInterface {

  public function __construct(
    private readonly SearchBusinessFilterApplier $businessFilterApplier,
    private readonly SearchSpatialApplier $spatialApplier,
    private readonly SearchContextSortApplier $sortApplier,
    private readonly SearchQueryConditionStripper $conditionStripper,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function applyBusinessFilters(QueryInterface $query, SearchContext $context): void {
    $this->businessFilterApplier->apply($query, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function applySpatial(QueryInterface $query, SearchContext $context, ?MapBounds $legacyMapBounds = NULL): void {
    if (!$context->isValid) {
      return;
    }

    $this->conditionStripper->stripFields($query, SearchQueryConditionStripper::LOCATION_FIELDS);
    $this->spatialApplier->apply(
      $query,
      $context->spatial,
      $context->geo,
      $legacyMapBounds,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function applyListSort(QueryInterface $query, SearchContext $context): void {
    $sorts = &$query->getSorts();
    $sorts = [];
    $this->sortApplier->apply($query, $context);
  }

  /**
   * {@inheritdoc}
   */
  public function apply(QueryInterface $query, SearchContext $context, ?MapBounds $legacyMapBounds = NULL): void {
    $this->applyBusinessFilters($query, $context);
    $this->applySpatial($query, $context, $legacyMapBounds);
  }

  /**
   * Whether map bounds from MapBoundsResolver should be skipped for this context.
   */
  public function shouldSkipLegacyMapBounds(SearchContext $context): bool {
    return in_array($context->spatial->mode, [
      SpatialMode::BboxAndPostal,
      SpatialMode::Geofilt,
      SpatialMode::Viewport,
    ], TRUE);
  }

}
