<?php

declare(strict_types=1);

namespace Drupal\ps_search\Search\Context;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\ps_search\Contract\GeoZoneRepositoryInterface;
use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Contract\SearchPathResolverInterface;
use Drupal\ps_search\ValueObject\GeoContext;
use Drupal\ps_search\ValueObject\RangeFilter;
use Drupal\ps_search\ValueObject\SearchContext;
use Drupal\ps_search\ValueObject\SearchFilters;
use Drupal\ps_search\ValueObject\SearchSort;
use Drupal\ps_search\ValueObject\SpatialMode;
use Symfony\Component\HttpFoundation\Request;

/**
 * Deterministic SEO URL builder and SearchContext payload serializer.
 */
final class SearchContextSerializer implements SearchContextSerializerInterface {

  public function __construct(
    private readonly SearchContextResolverInterface $contextResolver,
    private readonly SearchPathResolverInterface $searchPathResolver,
    private readonly GeoZoneRepositoryInterface $geoZoneRepository,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function fromRequest(Request $request): SearchContext {
    return $this->contextResolver->resolve($request);
  }

  /**
   * {@inheritdoc}
   */
  public function toUrl(SearchContext $context, string $langcode): Url {
    $path = $this->languagePrefix($langcode) . $this->buildSeoPath($context, $langcode);
    $options = ['query' => $this->buildQueryParams($context)];
    $language = $this->languageManager->getLanguage($langcode);
    if ($language !== NULL) {
      $options['language'] = $language;
    }

    return Url::fromUserInput($path, $options);
  }

  /**
   * {@inheritdoc}
   */
  public function buildSeoPath(SearchContext $context, string $langcode): string {
    $seoPrefix = $this->searchPathResolver->buildSeoFilterPathPrefix(
      $langcode,
      $context->filters->operationType,
      $context->filters->assetType,
    );

    if ($seoPrefix === NULL) {
      return '/' . trim($this->searchPathResolver->getSlugForLang($langcode), '/') . '/';
    }

    $path = rtrim($seoPrefix, '/');
    if ($context->geo !== NULL && $context->geo->slug !== '') {
      $path .= '/' . $context->geo->slug;
    }

    return $path . '/';
  }

  /**
   * {@inheritdoc}
   */
  public function buildQueryParams(SearchContext $context): array {
    $query = [];
    $filters = $context->filters;

    if ($filters->operationType !== NULL && $this->isFlexibleBase($context)) {
      $query['operation_type'] = $filters->operationType;
    }
    if ($filters->assetType !== NULL && $this->isFlexibleBase($context)) {
      $query['asset_type'] = $filters->assetType;
    }

    if ($context->geo !== NULL && $this->isFlexibleBase($context)) {
      $query['zone'] = $context->geo->slug;
    }

    $this->appendRangeParams($query, 'surface', $filters->surface, 'surface_min', 'surface_max');
    $this->appendRangeParams($query, 'budget', $filters->budget, 'budget_min', 'budget_max');
    $this->appendRangeParams($query, 'capacity', $filters->capacity, 'capacity_min', 'capacity_max');

    if ($context->sort->sortBy !== SearchSort::DEFAULT_SORT_BY
      || $context->sort->sortOrder !== SearchSort::DEFAULT_SORT_ORDER) {
      $query['sort_by'] = $context->sort->sortBy;
      $query['sort_order'] = $context->sort->sortOrder;
    }

    if ($context->spatial->mode === SpatialMode::Viewport && $context->spatial->viewport !== NULL) {
      $query['map_bounds'] = $context->spatial->viewport->toQueryValue();
    }

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function toArray(SearchContext $context): array {
    return [
      'geo' => $context->geo instanceof GeoContext ? $this->geoToArray($context->geo) : NULL,
      'filters' => [
        'operationType' => $context->filters->operationType,
        'assetType' => $context->filters->assetType,
        'surface' => $this->rangeToArray($context->filters->surface),
        'budget' => $this->rangeToArray($context->filters->budget),
        'capacity' => $this->rangeToArray($context->filters->capacity),
      ],
      'sort' => [
        'sortBy' => $context->sort->sortBy,
        'sortOrder' => $context->sort->sortOrder,
      ],
      'spatial' => [
        'mode' => $context->spatial->mode->value,
        'viewport' => $context->spatial->viewport?->toQueryValue(),
      ],
      'langcode' => $context->langcode,
      'countryCode' => $context->countryCode,
      'locationRequired' => $context->locationRequired,
      'isValid' => $context->isValid,
      'invalidReason' => $context->invalidReason,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildSeoPathFromQuery(string $langcode, array $query): ?string {
    $operationType = $this->firstQueryScalar($query['operation_type'] ?? NULL);
    $assetType = $this->firstQueryScalar($query['asset_type'] ?? NULL);

    $seoPrefix = $this->searchPathResolver->buildSeoFilterPathPrefix(
      $langcode,
      $operationType,
      $assetType,
    );
    if ($seoPrefix === NULL) {
      return NULL;
    }

    $path = rtrim($seoPrefix, '/');
    $zoneSlug = $this->resolveZoneSlugFromQuery($query);
    if ($zoneSlug !== NULL) {
      $path .= '/' . $zoneSlug;
    }

    return $path . '/';
  }

  /**
   * Builds an absolute redirect target from context and remaining request query.
   *
   * @param array<string, mixed> $remainingQuery
   */
  public function buildRedirectTarget(
    SearchContext $context,
    string $langcode,
    array $remainingQuery = [],
  ): string {
    $path = $this->languagePrefix($langcode) . $this->buildSeoPath($context, $langcode);
    $query = array_merge($this->buildQueryParams($context), $remainingQuery);
    $query = $this->filterNonEmptyQueryParams($query);

    if ($query === []) {
      return $path;
    }

    return $path . '?' . http_build_query($query);
  }

  private function isFlexibleBase(SearchContext $context): bool {
    return $this->searchPathResolver->buildSeoFilterPathPrefix(
      $context->langcode,
      $context->filters->operationType,
      $context->filters->assetType,
    ) === NULL;
  }

  /**
   * @param array<string, mixed> $query
   */
  private function resolveZoneSlugFromQuery(array $query): ?string {
    $zoneSlug = $this->firstQueryScalar($query['zone'] ?? NULL);
    if ($zoneSlug !== NULL) {
      return strtolower($zoneSlug);
    }

    $locality = $this->firstQueryScalar($query['locality'] ?? NULL);
    if ($locality === NULL) {
      return NULL;
    }

    $countryCode = $this->resolveCountryCode();
    if (preg_match('/^\d{2,3}$/', $locality) === 1) {
      $zone = $this->geoZoneRepository->findByPostalPrefix($locality, $countryCode);
      return $zone?->slug;
    }

    return NULL;
  }

  /**
   * @return array<string, float|null>|null
   */
  private function rangeToArray(?RangeFilter $range): ?array {
    if ($range === NULL || $range->isEmpty()) {
      return NULL;
    }

    return [
      'min' => $range->min,
      'max' => $range->max,
    ];
  }

  /**
   * @return array<string, mixed>
   */
  private function geoToArray(GeoContext $geo): array {
    return [
      'id' => $geo->id,
      'slug' => $geo->slug,
      'type' => $geo->type->value,
      'label' => $geo->label,
      'lat' => $geo->lat,
      'lng' => $geo->lng,
      'bbox' => $geo->bbox->toConfigArray(),
      'postalPrefixes' => $geo->postalPrefixes,
      'radiusM' => $geo->radiusM,
      'precision' => $geo->precision->value,
      'source' => $geo->source,
    ];
  }

  /**
   * @param array<string, mixed> $query
   */
  private function appendRangeParams(
    array &$query,
    string $legacyKey,
    ?RangeFilter $range,
    string $minKey,
    string $maxKey,
  ): void {
    if ($range === NULL || $range->isEmpty()) {
      return;
    }
    if ($range->min !== NULL) {
      $query[$minKey] = $range->min;
    }
    if ($range->max !== NULL) {
      $query[$maxKey] = $range->max;
    }
  }

  private function languagePrefix(string $langcode): string {
    $defaultLangcode = $this->languageManager->getDefaultLanguage()->getId();
    return ($langcode !== $defaultLangcode) ? '/' . $langcode : '';
  }

  private function resolveCountryCode(): string {
    $code = Settings::get('ps_country_code');
    return is_string($code) && $code !== '' ? strtolower($code) : 'com';
  }

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
   * @param array<string, mixed> $query
   *
   * @return array<string, mixed>
   */
  private function filterNonEmptyQueryParams(array $query): array {
    $filtered = [];
    foreach ($query as $key => $value) {
      if (is_array($value)) {
        $nested = $this->filterNonEmptyQueryParams($value);
        if ($nested !== []) {
          $filtered[$key] = $nested;
        }
        continue;
      }
      if ($value !== NULL && $value !== '') {
        $filtered[$key] = $value;
      }
    }

    return $filtered;
  }

}
