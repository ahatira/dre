<?php

declare(strict_types=1);

namespace Drupal\ps_search_filters\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\Config\LanguageConfigFactoryOverrideInterface;
use Drupal\views\ViewExecutable;
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
    $locality = $this->resolveLocality($query?->get('locality'));

    $assetLabel = $activeAsset ? $this->dictionaryLabel('asset_type', $activeAsset) : NULL;
    $opPhrase = $activeOp ? $this->operationPhrase($activeOp) : NULL;

    $currentSort = (string) ($query?->get('sort_by') ?? 'search_api_relevance');
    $currentOrder = strtoupper((string) ($query?->get('sort_order') ?? 'DESC'));

    return [
      'title' => $this->buildTitle($assetLabel, $opPhrase, $locality),
      'count' => (int) ($view->total_rows ?? 0),
      'sort_options' => $this->buildSortOptions($view, $currentSort, $currentOrder),
      'current_sort' => $currentSort,
      'current_order' => $currentOrder,
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
   * Resolves the locality label from query or SEO path.
   */
  private function resolveLocality(mixed $queryLocality): ?string {
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
   * Builds exposed sort options for the results header dropdown.
   *
   * @return list<array{value: string, label: string, selected: bool}>
   *   Sort options.
   */
  private function buildSortOptions(ViewExecutable $view, string $currentSort, string $currentOrder): array {
    $sorts = $view->display_handler->getOption('sorts') ?? [];
    $options = [];

    foreach ($sorts as $sort) {
      if (empty($sort['exposed'])) {
        continue;
      }
      $field = (string) ($sort['expose']['field_identifier'] ?? '');
      if ($field === '') {
        continue;
      }
      $order = strtoupper((string) ($sort['order'] ?? 'ASC'));
      $label = (string) ($sort['expose']['label'] ?? $field);
      $options[] = [
        'value' => $field . '|' . $order,
        'label' => $label,
        'selected' => $field === $currentSort && $order === $currentOrder,
      ];
    }

    if ($options === []) {
      $options[] = [
        'value' => 'search_api_relevance|DESC',
        'label' => (string) $this->t('Relevance'),
        'selected' => TRUE,
      ];
    }

    return $options;
  }

}
