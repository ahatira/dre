<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Filter;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\SearchResultCounter;
use Drupal\ps_search\Search\Query\SearchListSortApplier;
use Drupal\views\ViewExecutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds dynamic title, count and sort options for the search results header.
 */
final class SearchResultsHeaderBuilder {

  use StringTranslationTrait;

  public function __construct(
    private readonly ConfigFactoryInterface $configFactory,
    private readonly LanguageManagerInterface $languageManager,
    private readonly LanguageConfigFactoryOverrideInterface $langConfigOverride,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RequestStack $requestStack,
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly SearchResultCounter $searchResultCounter,
    private readonly SearchListSortApplier $sortApplier,
  ) {}

  /**
   * Builds header variables for the search results pane.
   *
   * @return array<string, mixed>
   *   Twig-friendly header data.
   */
  public function build(ViewExecutable $view): array {
    $request = $this->requestStack->getCurrentRequest();
    $query = $request?->query;

    [$activeOp, $activeAsset] = $this->resolveActiveFilters();
    $locality = $this->resolveLocalityLabel($request);

    $assetLabel = $activeAsset ? $this->dictionaryLabel('asset_type', $activeAsset) : NULL;
    $opPhrase = $activeOp ? $this->operationPhrase($activeOp) : NULL;

    $currentSort = $query?->get('sort_by');
    $currentOrder = $query?->get('sort_order');
    $currentSortBy = is_string($currentSort) ? $currentSort : NULL;
    $currentSortOrder = is_string($currentOrder) || is_numeric($currentOrder) ? (string) $currentOrder : NULL;

    $globalCount = $request instanceof Request
      ? $this->searchResultCounter->countBusinessFilters($request)
      : $this->resolveResultCount($view);
    $zoneCount = $request instanceof Request
      ? $this->searchResultCounter->countInBounds($request)
      : $globalCount;

    return [
      'title' => $this->buildTitle($assetLabel, $opPhrase, $locality),
      'count' => $globalCount,
      'zone_count' => $zoneCount,
      'sort_options' => $this->sortApplier->buildOptions($currentSortBy, $currentSortOrder),
      'selected_sort_label' => $this->sortApplier->buildSelectedLabel($currentSortBy, $currentSortOrder),
      'current_sort' => $currentSortBy ?? SearchListSortApplier::DEFAULT_SORT_BY,
      'current_order' => strtoupper((string) ($currentSortOrder ?? SearchListSortApplier::DEFAULT_SORT_ORDER)),
    ];
  }

  /**
   * Builds SEO copy for the search listing page (title + meta description).
   *
   * @return array{title: string, description: string}
   *   Human-readable strings without site name suffix.
   */
  public function buildSeoHeadData(ViewExecutable $view): array {
    [$activeOp, $activeAsset] = $this->resolveActiveFilters();
    $locality = $this->resolveLocalityLabel($this->requestStack->getCurrentRequest());

    $assetLabel = $activeAsset ? $this->dictionaryLabel('asset_type', $activeAsset) : NULL;
    $opPhrase = $activeOp ? $this->operationPhrase($activeOp) : NULL;

    return [
      'title' => $this->buildTitle($assetLabel, $opPhrase, $locality),
      'description' => $this->buildMetaDescription($assetLabel, $activeOp, $locality),
    ];
  }

  /**
   * Builds a themed render array for the results header fragment.
   */
  public function buildRenderArray(ViewExecutable $view): array {
    $header = $this->build($view);

    return [
      '#theme' => 'ps_search_results_header',
      '#title' => $header['title'],
      '#count' => $header['count'],
      '#zone_count' => $header['zone_count'],
      '#sort_options' => $header['sort_options'],
      '#selected_sort_label' => $header['selected_sort_label'],
      '#cache' => [
        'contexts' => ['url.query_args', 'languages:language_interface'],
        'max-age' => 0,
      ],
      '#attached' => [
        'library' => [
          'ps_theme/component_dropdown',
        ],
      ],
    ];
  }

  /**
   * Resolves active operation and asset type from SEO path or query string.
   *
   * @return array{0: ?string, 1: ?string}
   *   Operation code and asset type code.
   */
  private function resolveActiveFilters(): array {
    $request = $this->requestStack->getCurrentRequest();
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $base = $this->configFactory->get('ps_search.seo_url_mappings');
    $langOverride = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');

    $opSlugs = array_merge(
      $base->get('operation_types') ?? [],
      $langOverride->get('operation_types') ?? [],
    );
    $assetSlugs = array_merge(
      $base->get('asset_types') ?? [],
      $langOverride->get('asset_types') ?? [],
    );
    $opBySlug = array_flip($opSlugs);
    $assetBySlug = array_flip($assetSlugs);

    $langPrefix = ($langcode !== $this->languageManager->getDefaultLanguage()->getId())
      ? '/' . $langcode
      : '';

    $pathInfo = $request?->getPathInfo() ?? '';
    if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
      $stripped = substr($pathInfo, strlen($langPrefix));
    }
    else {
      $stripped = $pathInfo;
    }
    $segments = array_values(array_filter(explode('/', $stripped)));

    $activeOp = NULL;
    $activeAsset = NULL;
    if (!empty($segments[0]) && isset($opBySlug[$segments[0]])) {
      $activeOp = $opBySlug[$segments[0]];
      if (!empty($segments[1]) && isset($assetBySlug[$segments[1]])) {
        $activeAsset = $assetBySlug[$segments[1]];
      }
    }

    $query = $request?->query;
    if (!$activeOp) {
      $activeOp = $this->firstQueryValue($query?->all('operation_type'));
    }
    if (!$activeAsset) {
      $activeAsset = $this->firstQueryValue($query?->all('asset_type'));
    }

    return [$activeOp, $activeAsset];
  }

  /**
   * Returns the first scalar value from a query parameter.
   */
  private function firstQueryValue(mixed $value): ?string {
    if (is_array($value)) {
      $value = reset($value);
    }
    if (!is_string($value) || $value === '') {
      return NULL;
    }
    return $value;
  }

  /**
   * Resolves a human locality label from query tokens or SEO path.
   */
  private function resolveLocalityLabel(?Request $request): ?string {
    if ($request !== NULL) {
      $tokens = $this->locationSearchFilter->extractTokensFromRequest($request);
      if ($tokens !== []) {
        $meta = $this->locationSearchFilter->resolveTokenMetadata($tokens[0]);
        $label = trim((string) ($meta['label'] ?? ''));
        if ($label !== '') {
          return $label;
        }
      }
    }

    $queryLocality = $this->firstQueryValue($request?->query->all()['locality'] ?? NULL);
    return $this->resolveLocality($queryLocality);
  }

  /**
   * Resolves the locality label from query or SEO path.
   */
  private function resolveLocality(?string $queryLocality): ?string {
    if (is_string($queryLocality) && $queryLocality !== '') {
      $parts = array_map('trim', explode(',', $queryLocality));
      return $parts[0] !== '' ? $parts[0] : NULL;
    }

    return $this->extractLocalityFromPath();
  }

  /**
   * Extracts locality slug from SEO path segments.
   */
  private function extractLocalityFromPath(): ?string {
    $request = $this->requestStack->getCurrentRequest();
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $base = $this->configFactory->get('ps_search.seo_url_mappings');
    $langOverride = $this->langConfigOverride->getOverride($langcode, 'ps_search.seo_url_mappings');

    $opSlugs = array_map('strtolower', array_values(array_merge(
      $base->get('operation_types') ?? [],
      $langOverride->get('operation_types') ?? [],
    )));
    $assetSlugs = array_map('strtolower', array_values(array_merge(
      $base->get('asset_types') ?? [],
      $langOverride->get('asset_types') ?? [],
    )));

    $langPrefix = ($langcode !== $this->languageManager->getDefaultLanguage()->getId())
      ? '/' . $langcode
      : '';
    $pathInfo = $request?->getPathInfo() ?? '';
    if ($langPrefix !== '' && str_starts_with($pathInfo, $langPrefix . '/')) {
      $pathInfo = substr($pathInfo, strlen($langPrefix));
    }

    $segments = array_values(array_filter(explode('/', $pathInfo)));
    if ($segments === [] || !in_array(strtolower($segments[0]), $opSlugs, TRUE)) {
      return NULL;
    }

    $localityIndex = 1;
    if (!empty($segments[1]) && in_array(strtolower($segments[1]), $assetSlugs, TRUE)) {
      $localityIndex = 2;
    }

    if (empty($segments[$localityIndex])) {
      return NULL;
    }

    $slug = preg_replace('/-\d{5}$/', '', $segments[$localityIndex]) ?? $segments[$localityIndex];
    $words = explode('-', $slug);
    $words = array_map(static fn(string $word): string => ucfirst($word), $words);

    return implode(' ', $words);
  }

  /**
   * Loads a dictionary label for a code.
   */
  private function dictionaryLabel(string $type, string $code): string {
    $storage = $this->entityTypeManager->getStorage('ps_dictionary_entry');
    $entryId = $type . '.' . strtolower($code);
    $entry = $storage->load($entryId);
    return $entry ? (string) $entry->label() : $code;
  }

  /**
   * Returns a human phrase for an operation type code.
   */
  private function operationPhrase(string $code): string {
    return match (strtoupper($code)) {
      'LOC' => (string) $this->t('for rent'),
      'VEN' => (string) $this->t('for sale'),
      default => strtolower($code),
    };
  }

  /**
   * Builds the results header title from active filters.
   */
  private function buildTitle(?string $assetLabel, ?string $opPhrase, ?string $locality): string {
    $in = (string) $this->t('in');

    if ($assetLabel && $opPhrase && $locality) {
      return (string) $this->t('@asset @operation @in @locality', [
        '@asset' => $assetLabel,
        '@operation' => $opPhrase,
        '@in' => $in,
        '@locality' => $locality,
      ]);
    }
    if ($assetLabel && $opPhrase) {
      return (string) $this->t('@asset @operation', [
        '@asset' => $assetLabel,
        '@operation' => $opPhrase,
      ]);
    }
    if ($assetLabel && $locality) {
      return (string) $this->t('@asset @in @locality', [
        '@asset' => $assetLabel,
        '@in' => $in,
        '@locality' => $locality,
      ]);
    }
    if ($locality) {
      return (string) $this->t('Properties @in @locality', [
        '@in' => $in,
        '@locality' => $locality,
      ]);
    }
    if ($assetLabel) {
      return $assetLabel;
    }

    return (string) $this->t('Property search');
  }

  /**
   * Builds a meta description aligned with BNPPRE listing pages.
   */
  private function buildMetaDescription(?string $assetLabel, ?string $operationCode, ?string $locality): string {
    $localitySuffix = $locality
      ? (string) $this->t('in @locality', ['@locality' => $locality])
      : (string) $this->t('across France');

    if ($assetLabel && $operationCode) {
      return match (strtoupper($operationCode)) {
        'LOC' => (string) $this->t('Discover all our @asset for rent @locality_suffix and find your ideal space quickly.', [
          '@asset' => mb_strtolower($assetLabel),
          '@locality_suffix' => $localitySuffix,
        ]),
        'VEN' => (string) $this->t('Discover all our @asset for sale @locality_suffix and find your ideal space quickly.', [
          '@asset' => mb_strtolower($assetLabel),
          '@locality_suffix' => $localitySuffix,
        ]),
        default => (string) $this->t('Discover all our @asset @locality_suffix and find your ideal space quickly.', [
          '@asset' => mb_strtolower($assetLabel),
          '@locality_suffix' => $localitySuffix,
        ]),
      };
    }

    if ($locality) {
      return (string) $this->t('Search commercial real estate @locality_suffix and find your ideal space quickly.', [
        '@locality_suffix' => $localitySuffix,
      ]);
    }

    return (string) $this->t('Search commercial real estate across France and find your ideal space quickly.');
  }

  /**
   * Returns the total number of filtered results for the header counter.
   */
  private function resolveResultCount(ViewExecutable $view): int {
    $total = (int) ($view->total_rows ?? 0);
    if ($total > 0) {
      return $total;
    }

    return count($view->result ?? []);
  }

}
