<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Stub;

use Drupal\ps_search\Contract\SearchResultGeoBoundsResolverInterface;
use Drupal\ps_search\ValueObject\MapBounds;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test double for map geo bounds resolution.
 */
final class StubSearchResultGeoBoundsResolver implements SearchResultGeoBoundsResolverInterface {

  public function __construct(
    private readonly int $defaultZoneCount = 1,
    private readonly int $globalCount = 0,
    private readonly ?MapBounds $resultBounds = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function countBusinessFilters(Request $request): int {
    return $this->globalCount;
  }

  /**
   * {@inheritdoc}
   */
  public function countInBoundsWithBounds(Request $request, MapBounds $bounds): int {
    return $this->defaultZoneCount;
  }

  /**
   * {@inheritdoc}
   */
  public function resolveFromFilteredResults(Request $request): ?MapBounds {
    return $this->resultBounds;
  }

}
