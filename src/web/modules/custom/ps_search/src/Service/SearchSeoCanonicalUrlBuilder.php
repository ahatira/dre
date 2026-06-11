<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds canonical SEO URLs for the property search listing page.
 *
 * Strips facet/query noise and normalizes legacy search paths to SEO slugs.
 */
final class SearchSeoCanonicalUrlBuilder {

  public function __construct(
    private readonly SearchPathResolver $searchPathResolver,
    private readonly SearchSeoLocalityPathBuilder $seoLocalityPathBuilder,
    private readonly LanguageManagerInterface $languageManager,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Returns an absolute canonical URL for the current search request.
   */
  public function buildAbsoluteUrl(?Request $request = NULL): ?string {
    $request ??= $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return NULL;
    }

    $path = $this->buildCanonicalPath($request);
    if ($path === NULL) {
      return NULL;
    }

    return $request->getSchemeAndHttpHost() . $path;
  }

  /**
   * Returns a root-relative canonical path (with trailing slash).
   */
  public function buildCanonicalPath(?Request $request = NULL): ?string {
    $request ??= $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return NULL;
    }

    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $pathInfo = $request->getPathInfo();

    if ($this->isSeoSearchPath($pathInfo, $langcode)) {
      return $this->normalizePath($pathInfo);
    }

    [$langPrefix, $segment] = $this->parseSearchPathSegment($pathInfo, $langcode);
    if ($segment === NULL) {
      return NULL;
    }

    $operationType = $this->firstQueryScalar($request->query->all()['operation_type'] ?? NULL);
    if ($operationType === NULL) {
      return $this->normalizePath($langPrefix . '/' . $this->searchPathResolver->getSlugForLang($langcode));
    }

    $m = $this->searchPathResolver->getSeoSlugMappings($langcode);
    $opSlug = $m['val_to_op'][strtoupper($operationType)] ?? NULL;
    if ($opSlug === NULL) {
      return NULL;
    }

    $seoPath = $langPrefix . '/' . $opSlug;

    $assetType = $this->firstQueryScalar($request->query->all()['asset_type'] ?? NULL);
    if ($assetType !== NULL) {
      $assetSlug = $m['val_to_asset'][strtoupper($assetType)] ?? NULL;
      if ($assetSlug !== NULL) {
        $seoPath .= '/' . $assetSlug;
      }
    }

    $tokens = $this->extractLocationTokens($request);
    if (count($tokens) === 1) {
      $seoPath = $this->seoLocalityPathBuilder->appendSegmentsToPath($seoPath, $tokens[0]);
    }

    return $this->normalizePath($seoPath);
  }

  /**
   * Whether the request path is already an SEO search URL.
   */
  private function isSeoSearchPath(string $pathInfo, string $langcode): bool {
    $segments = $this->pathSegments($pathInfo, $langcode);
    if ($segments === []) {
      return FALSE;
    }

    $m = $this->searchPathResolver->getSeoSlugMappings($langcode);
    return isset($m['op_to_val'][strtolower($segments[0])]);
  }

  /**
   * Parses a configured search slug from the path, if present.
   *
   * @return array{0: string, 1: ?string}
   *   Language prefix and search slug segment, if any.
   */
  private function parseSearchPathSegment(string $pathInfo, string $langcode): array {
    $langPrefix = $this->languagePrefix($langcode);
    $stripped = $pathInfo;
    if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
      $stripped = substr($pathInfo, strlen($langPrefix));
    }

    $segments = array_values(array_filter(explode('/', $stripped)));
    if (count($segments) !== 1) {
      return [$langPrefix, NULL];
    }

    $segment = strtolower($segments[0]);
    if (!$this->searchPathResolver->isSearchPathSegment($segment)) {
      return [$langPrefix, NULL];
    }

    return [$langPrefix, $segment];
  }

  /**
   * Splits a path into segments after the optional language prefix.
   *
   * @return string[]
   *   Path segments after optional language prefix.
   */
  private function pathSegments(string $pathInfo, string $langcode): array {
    $langPrefix = $this->languagePrefix($langcode);
    $stripped = $pathInfo;
    if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
      $stripped = substr($pathInfo, strlen($langPrefix));
    }
    elseif ($langPrefix !== '' && $pathInfo === $langPrefix) {
      return [];
    }

    return array_values(array_filter(explode('/', $stripped)));
  }

  /**
   * Returns the URL language prefix for a langcode.
   */
  private function languagePrefix(string $langcode): string {
    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    return ($langcode !== $defaultLangcode) ? '/' . $langcode : '';
  }

  /**
   * Reads location tokens from locality or locations query parameters.
   *
   * @return string[]
   *   Up to 10 location tokens from the query string.
   */
  private function extractLocationTokens(Request $request): array {
    $localityRaw = $request->query->all()['locality'] ?? NULL;
    if (is_array($localityRaw)) {
      return array_values(array_filter(array_map('strval', $localityRaw)));
    }

    if (!is_string($localityRaw) || $localityRaw === '') {
      $locationsRaw = $request->query->get('locations');
      if (!is_string($locationsRaw) || $locationsRaw === '') {
        return [];
      }
      return $this->splitLocationTokens($locationsRaw);
    }

    return $this->splitLocationTokens($localityRaw);
  }

  /**
   * Splits a comma-separated location string into unique tokens.
   *
   * @return string[]
   *   Normalized location tokens.
   */
  private function splitLocationTokens(string $value): array {
    $parts = preg_split('/[,;]+/', $value) ?: [];
    $tokens = [];

    foreach ($parts as $part) {
      $cleaned = trim($part);
      if ($cleaned === '') {
        continue;
      }
      $tokens[mb_strtolower($cleaned)] = $cleaned;
      if (count($tokens) >= 10) {
        break;
      }
    }

    return array_values($tokens);
  }

  /**
   * Normalizes a BEF or scalar query parameter to a string value.
   */
  private function firstQueryScalar(mixed $value): ?string {
    if (is_array($value)) {
      $value = array_key_first($value);
    }
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    return $value;
  }

  /**
   * Ensures a root-relative path ends with a trailing slash.
   */
  private function normalizePath(string $path): string {
    $path = '/' . trim($path, '/');
    return $path . '/';
  }

}
