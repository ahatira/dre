<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\ps_search\ValueObject\SearchContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resolves an immutable SearchContext from an inbound HTTP request.
 */
interface SearchContextResolverInterface {

  /**
   * Builds the search context for a request (no external geocoding).
   */
  public function resolve(Request $request): SearchContext;

  /**
   * Whether the request targets a search listing page or internal search route.
   */
  public function isSearchRequest(Request $request): bool;

}
