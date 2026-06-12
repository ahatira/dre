<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds canonical SEO URLs for the property search listing page.
 *
 * Strips facet/query noise and normalizes legacy search paths to SEO slugs.
 */
final class SearchSeoCanonicalUrlBuilder {

  private const SEARCH_ROUTE = 'view.ps_search_offers.page_list';

  public function __construct(
    private readonly SearchPathResolver $searchPathResolver,
    private readonly SearchSeoLocalityPathBuilder $seoLocalityPathBuilder,
    private readonly LanguageManagerInterface $languageManager,
    private readonly RequestStack $requestStack,
  ) {}

  /**
   * Builds a root-relative SEO search path from canonical filter query params.
   *
   * @param array<string, string> $query
   *   Keys: operation_type, asset_type, locality and/or locations.
   */
  public function buildCanonicalPathForFilters(string $langcode, array $query): ?string {
    return $this->buildCanonicalPathFromQuery($langcode, $query);
  }

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
   * Builds absolute alternate URLs keyed by langcode plus x-default.
   *
   * @return array<string, string>
   *   Alternate URLs keyed by langcode and optionally x-default.
   */
  public function buildAlternateUrls(?Request $request = NULL): array {
    $request ??= $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return [];
    }

    $query = $this->resolveHreflangQuery($request);
    $urls = [];

    foreach ($this->languageManager->getLanguages() as $langcode => $language) {
      $urls[$langcode] = Url::fromRoute(self::SEARCH_ROUTE, [], [
        'query' => $query,
        'language' => $language,
        'absolute' => TRUE,
      ])->toString();
    }

    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    if (isset($urls[$defaultLangcode])) {
      $urls['x-default'] = $urls[$defaultLangcode];
    }

    return $urls;
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
      $query = $this->parseSeoPathFilterQuery($pathInfo, $langcode);
      if ($query !== []) {
        return $this->buildCanonicalPathFromQuery($langcode, $query);
      }

      return $this->normalizePath($pathInfo);
    }

    return $this->buildCanonicalPathFromQuery($langcode, $this->resolveHreflangQuery($request));
  }

  /**
   * Extracts canonical search filters for hreflang and canonical URLs.
   *
   * @return array<string, string>
   *   operation_type, asset_type and optional locality/locations query params.
   */
  public function resolveHreflangQuery(?Request $request = NULL): array {
    $request ??= $this->requestStack->getCurrentRequest();
    if ($request === NULL) {
      return [];
    }

    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    $pathInfo = $request->getPathInfo();

    if ($this->isSeoSearchPath($pathInfo, $langcode)) {
      $fromPath = $this->parseSeoPathFilterQuery($pathInfo, $langcode);
      if ($fromPath !== []) {
        return $fromPath;
      }
    }

    $query = [];
    $operationType = $this->firstQueryScalar($request->query->all()['operation_type'] ?? NULL);
    if ($operationType !== NULL) {
      $query['operation_type'] = $operationType;
    }

    $assetType = $this->firstQueryScalar($request->query->all()['asset_type'] ?? NULL);
    if ($assetType !== NULL) {
      $query['asset_type'] = $assetType;
    }

    $tokens = $this->extractLocationTokens($request);
    if (count($tokens) === 1 && $tokens[0] !== '') {
      $query['locality'] = $tokens[0];
    }
    elseif (count($tokens) > 1) {
      $query['locations'] = implode(',', $tokens);
    }

    return $query;
  }

  /**
   * Builds a canonical SEO path for a language from canonical filter query.
   */
  private function buildCanonicalPathFromQuery(string $langcode, array $query): ?string {
    $langPrefix = $this->languagePrefix($langcode);
    $operationType = $query['operation_type'] ?? NULL;

    if ($operationType === NULL) {
      return $this->normalizePath($langPrefix . '/' . $this->searchPathResolver->getSlugForLang($langcode));
    }

    $m = $this->searchPathResolver->getSeoSlugMappings($langcode);
    $opSlug = $m['val_to_op'][strtoupper($operationType)] ?? NULL;
    if ($opSlug === NULL) {
      return NULL;
    }

    $seoPath = $langPrefix . '/' . $opSlug;

    $assetType = $query['asset_type'] ?? NULL;
    if (is_string($assetType) && $assetType !== '') {
      $assetSlug = $m['val_to_asset'][strtoupper($assetType)] ?? NULL;
      if ($assetSlug !== NULL) {
        $seoPath .= '/' . $assetSlug;
      }
    }

    $locality = $query['locality'] ?? NULL;
    $locations = $query['locations'] ?? NULL;
    $locationValue = is_string($locations) && $locations !== '' ? $locations : $locality;
    if (is_string($locationValue) && $locationValue !== '') {
      $tokens = $this->splitLocationTokens($locationValue);
      if (count($tokens) === 1) {
        $seoPath = $this->seoLocalityPathBuilder->appendSegmentsToPath($seoPath, $tokens[0]);
      }
    }

    return $this->normalizePath($seoPath);
  }

  /**
   * Parses SEO path segments into canonical search filter query params.
   *
   * @return array<string, string>
   *   Canonical filter query params extracted from the SEO path.
   */
  private function parseSeoPathFilterQuery(string $pathInfo, string $langcode): array {
    $segments = $this->pathSegments($pathInfo, $langcode);
    if ($segments === []) {
      return [];
    }

    $m = $this->searchPathResolver->getSeoSlugMappings($langcode);
    $operationType = $m['op_to_val'][strtolower($segments[0])] ?? NULL;
    if ($operationType === NULL) {
      return [];
    }

    $query = ['operation_type' => $operationType];
    $rest = array_slice($segments, 1);
    $assetFound = FALSE;
    $possibleDeptSegment = NULL;
    $possibleCitySegment = NULL;

    foreach ($rest as $segment) {
      $slug = strtolower($segment);
      if (!$assetFound && isset($m['asset_to_val'][$slug])) {
        $query['asset_type'] = $m['asset_to_val'][$slug];
        $assetFound = TRUE;
        continue;
      }

      $possibleDeptSegment = $possibleCitySegment;
      $possibleCitySegment = $segment;
    }

    if ($possibleDeptSegment !== NULL && $possibleCitySegment !== NULL) {
      $token = $this->seoLocalityPathBuilder->pathSegmentsToToken(
        $possibleDeptSegment,
        $possibleCitySegment,
      );
      if ($token !== NULL && $token !== '') {
        $query['locality'] = $token;
      }
    }
    elseif ($possibleCitySegment !== NULL) {
      $token = $this->seoLocalityPathBuilder->pathSegmentsToToken(NULL, $possibleCitySegment);
      if ($token === NULL) {
        $token = $this->seoLocalityPathBuilder->deptSegmentToToken($possibleCitySegment);
      }
      if ($token !== NULL && $token !== '') {
        $query['locality'] = $token;
      }
    }

    return $query;
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
