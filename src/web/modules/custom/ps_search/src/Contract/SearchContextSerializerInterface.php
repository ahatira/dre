<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

use Drupal\Core\Url;
use Drupal\ps_search\ValueObject\SearchContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * Serializes SearchContext to SEO URLs and client payloads.
 */
interface SearchContextSerializerInterface {

  /**
   * Builds a SearchContext from the current request (delegates to resolver).
   */
  public function fromRequest(Request $request): SearchContext;

  /**
   * Builds a canonical Drupal URL for a search context.
   */
  public function toUrl(SearchContext $context, string $langcode): Url;

  /**
   * Builds a root-relative SEO path (with trailing slash, no query string).
   */
  public function buildSeoPath(SearchContext $context, string $langcode): string;

  /**
   * Builds flat query parameters for filters not encoded in the SEO path.
   *
   * @return array<string, mixed>
   */
  public function buildQueryParams(SearchContext $context): array;

  /**
   * Compact array for drupalSettings and client-side store.
   *
   * @return array<string, mixed>
   */
  public function toArray(SearchContext $context): array;

  /**
   * Builds SEO path from facet query (outbound path processor helper).
   *
   * @param array<string, mixed> $query
   */
  public function buildSeoPathFromQuery(string $langcode, array $query): ?string;

}
