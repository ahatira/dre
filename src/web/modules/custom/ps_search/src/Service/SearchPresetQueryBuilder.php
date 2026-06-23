<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Builds public search URLs from operation, asset and optional locality preset.
 */
final class SearchPresetQueryBuilder {

  public function __construct(
    private readonly SearchSeoCanonicalUrlBuilder $canonicalUrlBuilder,
    private readonly SearchPathResolverInterface $searchPathResolver,
    private readonly LanguageManagerInterface $languageManager,
    private readonly SearchEngineSettingsReader $engineSettings,
    private readonly SearchContextSerializerInterface $contextSerializer,
    private readonly SearchContextResolverInterface $contextResolver,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
  ) {}

  /**
   * Builds a localized public search URL for a preset.
   */
  public function buildUrl(
    ?string $operationType,
    ?string $assetType,
    ?string $localityToken = NULL,
    ?string $langcode = NULL,
  ): string {
    $langcode ??= $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();

    if ($this->engineSettings->isSearchContextEnabled()) {
      return $this->buildContextUrl($operationType, $assetType, $localityToken, $langcode);
    }

    $query = $this->buildQuery($operationType, $assetType, $localityToken);

    $path = $this->canonicalUrlBuilder->buildCanonicalPathForFilters($langcode, $query);
    if ($path !== NULL) {
      return Url::fromUserInput($path, ['language' => $this->languageManager->getLanguage($langcode)])->toString();
    }

    return Url::fromRoute('view.ps_search_offers.page_list', [], [
      'query' => $query,
      'language' => $this->languageManager->getLanguage($langcode),
    ])->toString();
  }

  /**
   * Normalizes preset values into canonical search filter query params.
   *
   * @return array<string, string>
   *   Legacy query parameters for flexible URLs.
   */
  public function buildQuery(
    ?string $operationType,
    ?string $assetType,
    ?string $localityToken = NULL,
  ): array {
    $query = [];

    $operationType = $this->normalizeCode($operationType);
    if ($operationType !== NULL) {
      $query['operation_type'] = $operationType;
    }

    $assetType = $this->normalizeCode($assetType);
    if ($assetType !== NULL) {
      $query['asset_type'] = $assetType;
    }

    $localityToken = trim((string) $localityToken);
    if ($localityToken !== '') {
      $query['locality'] = $localityToken;
    }

    return $query;
  }

  /**
   * Returns the bare search listing path for a language (fallback).
   */
  public function getSearchBasePath(?string $langcode = NULL): string {
    $langcode ??= $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();
    return $this->searchPathResolver->getPublicPath($langcode);
  }

  /**
   * Builds a v2 SEO URL from preset values via SearchContext resolution.
   */
  private function buildContextUrl(
    ?string $operationType,
    ?string $assetType,
    ?string $localityToken,
    string $langcode,
  ): string {
    $operationType = $this->normalizeCode($operationType);
    $assetType = $this->normalizeCode($assetType);
    $path = $this->buildPresetSeoPath($langcode, $operationType, $assetType, $localityToken);

    $request = Request::create($path, 'GET');
    $context = $this->contextResolver->resolve($request);

    return $this->languagePrefix($langcode) . $this->contextSerializer->buildSeoPath($context, $langcode);
  }

  /**
   * Builds URL language prefix for non-default languages.
   */
  private function languagePrefix(string $langcode): string {
    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    return ($langcode !== $defaultLangcode) ? '/' . $langcode : '';
  }

  /**
   * Builds a root-relative SEO path for preset op/asset/locality values.
   */
  private function buildPresetSeoPath(
    string $langcode,
    ?string $operationType,
    ?string $assetType,
    ?string $localityToken,
  ): string {
    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    $langPrefix = ($langcode !== $defaultLangcode) ? '/' . $langcode : '';

    $seoPrefix = $this->searchPathResolver->buildSeoFilterPathPrefix($langcode, $operationType, $assetType);
    if ($seoPrefix === NULL) {
      return $langPrefix . '/' . trim($this->searchPathResolver->getSlugForLang($langcode), '/') . '/';
    }

    $path = $langPrefix . rtrim($seoPrefix, '/');
    $geoSlug = $this->resolveLocalitySlug($localityToken);
    if ($geoSlug !== NULL) {
      $path .= '/' . $geoSlug;
    }

    return $path . '/';
  }

  /**
   * Resolves a preset locality token to a GeoZone slug when possible.
   */
  private function resolveLocalitySlug(?string $localityToken): ?string {
    $localityToken = trim((string) $localityToken);
    if ($localityToken === '') {
      return NULL;
    }

    $countryCode = $this->resolveCountryCode();
    $slugCandidate = strtolower($localityToken);
    $zone = $this->geoZoneRepository->findBySlug($slugCandidate, $countryCode);
    if ($zone !== NULL) {
      return $zone->slug;
    }

    if (preg_match('/^\d{2,3}$/', $localityToken) === 1) {
      $zone = $this->geoZoneRepository->findByPostalPrefix($localityToken, $countryCode);
      if ($zone !== NULL) {
        return $zone->slug;
      }
    }

    return $slugCandidate;
  }

  /**
   * Resolves the current site country code from settings.
   */
  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

  /**
   * Normalizes a facet code to uppercase or NULL when empty.
   */
  private function normalizeCode(?string $code): ?string {
    if (!is_string($code) || trim($code) === '') {
      return NULL;
    }

    return strtoupper(trim($code));
  }

}
