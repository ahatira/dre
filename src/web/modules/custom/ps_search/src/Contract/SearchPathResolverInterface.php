<?php

declare(strict_types=1);

namespace Drupal\ps_search\Contract;

/**
 * Resolves public search paths for links (SEO-aware in lot L2).
 */
interface SearchPathResolverInterface {

  /**
   * Returns the internal user-facing search base path.
   *
   * Example: /find-property (no language prefix; Drupal Url handles prefix).
   */
  public function getPublicPath(?string $langcode = NULL): string;

  /**
   * Maps a stored machine search path to the localized public slug.
   */
  public function resolveStoredPublicSearchPath(string $path, string $langcode): string;

  /**
   * Internal Drupal route path (leading slash, machine slug).
   */
  public function getInternalPath(): string;

  /**
   * Whether the full path (no language prefix) is exactly a search page path.
   */
  public function isSearchPath(string $path): bool;

  /**
   * Whether a path segment is a configured operation slug for a language.
   */
  public function isOperationSlug(string $langcode, string $slug): bool;

  /**
   * Whether a path segment is a configured asset slug (or alias) for a language.
   */
  public function isAssetSlug(string $langcode, string $slug): bool;

  /**
   * Parses SEO path segments into operation/asset codes and locality tail segments.
   *
   * @param string[] $segments
   *   Path segments without language prefix.
   *
   * @return array{operation_type: ?string, asset_type: ?string, locality_segments: list<string>}
   */
  public function resolveFacetsFromPathSegments(string $langcode, array $segments): array;

  /**
   * Builds the SEO path prefix for active operation and/or asset filters.
   */
  public function buildSeoFilterPathPrefix(string $langcode, ?string $operationType, ?string $assetType): ?string;

  /**
   * Returns the localized public search slug for a language.
   */
  public function getSlugForLang(string $langcode): string;

}
