<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Determines whether a property search listing URL should be indexed.
 *
 * Clean SEO paths without query string are indexable. Any user-visible facet,
 * sort, pagination or map parameter triggers noindex (canonical stays clean).
 */
final class SearchSeoIndexabilityChecker {

  public function __construct(
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Returns TRUE when the search request should use a noindex robots tag.
   */
  public function shouldNoindex(?Request $request = NULL): bool {
    $request ??= $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return FALSE;
    }

    $queryString = $request->getQueryString();
    if ($queryString === NULL || $queryString === '') {
      return FALSE;
    }

    $userQuery = [];
    parse_str($queryString, $userQuery);

    return $userQuery !== [];
  }

}
