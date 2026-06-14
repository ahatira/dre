<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Url;

/**
 * Builds public search URLs from operation, asset and optional locality preset.
 */
final class SearchPresetQueryBuilder {

  public function __construct(
    private readonly SearchSeoCanonicalUrlBuilder $canonicalUrlBuilder,
    private readonly SearchPathResolver $searchPathResolver,
    private readonly LanguageManagerInterface $languageManager,
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
   * Normalizes a facet code to uppercase or NULL when empty.
   */
  private function normalizeCode(?string $code): ?string {
    if (!is_string($code) || trim($code) === '') {
      return NULL;
    }

    return strtoupper(trim($code));
  }

}
