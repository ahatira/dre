<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Search\Context\SearchContextStorageMapper;
use Drupal\ps_search\ValueObject\SearchContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * Serializes current search state into storable alert criteria.
 */
final class SearchAlertCriteriaSerializer {

  public const SCHEMA_VERSION_LEGACY = 1;

  public const SCHEMA_VERSION_CONTEXT = 2;

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly LanguageManagerInterface $languageManager,
    private readonly SearchContextSerializerInterface $contextSerializer,
    private readonly SearchContextStorageMapper $contextStorageMapper,
    private readonly SearchEngineSettingsReader $engineSettings,
  ) {}

  /**
   * Builds normalized criteria from the current HTTP request.
   *
   * @return array<string, mixed>
   *   Flat criteria blob compatible with SearchQueryFactory replay.
   */
  public function fromRequest(Request $request): array {
    if ($this->engineSettings->isSearchContextEnabled()) {
      return $this->fromSearchContextRequest($request);
    }

    return $this->fromLegacyRequest($request);
  }

  /**
   * Encodes criteria as JSON.
   *
   * @param array<string, mixed> $criteria
   *   Normalized criteria array.
   */
  public function toJson(array $criteria): string {
    return (string) json_encode($this->normalize($criteria), JSON_THROW_ON_ERROR);
  }

  /**
   * Builds a deduplication hash for email + criteria pairs.
   *
   * @param array<string, mixed> $criteria
   *   Normalized criteria array.
   */
  public function hash(array $criteria): string {
    return hash('sha256', $this->toJson($criteria));
  }

  /**
   * Rebuilds a GET request for SearchQueryFactory replay.
   *
   * @param array<string, mixed> $criteria
   *   Stored criteria blob.
   */
  public function buildRequest(array $criteria): Request {
    if ($this->isContextCriteria($criteria)) {
      return $this->buildRequestFromContext($criteria);
    }

    return $this->buildLegacyRequest($criteria);
  }

  /**
   * Returns a stable normalized criteria array for storage and hashing.
   *
   * @param array<string, mixed> $criteria
   *   Raw criteria array.
   *
   * @return array<string, mixed>
   *   Sorted criteria array.
   */
  public function normalizeCriteria(array $criteria): array {
    return $this->normalize($criteria);
  }

  /**
   * Builds v2 alert criteria from a resolved SearchContext.
   *
   * @return array<string, mixed>
   *   Context-based criteria blob.
   */
  private function fromSearchContextRequest(Request $request): array {
    $context = $this->contextSerializer->fromRequest($request);
    $moreCriteria = $this->extractMoreCriteria($request);

    $criteria = [
      'schema_version' => self::SCHEMA_VERSION_CONTEXT,
      'context' => $this->contextStorageMapper->export($context, $moreCriteria),
      'search_url' => $request->getUri(),
      'search_path' => $request->getPathInfo(),
      'langcode' => $context->langcode,
    ];

    return $this->normalize($criteria);
  }

  /**
   * Builds legacy flat alert criteria from query parameters.
   *
   * @return array<string, mixed>
   *   Legacy criteria blob.
   */
  private function fromLegacyRequest(Request $request): array {
    $criteria = [
      'schema_version' => self::SCHEMA_VERSION_LEGACY,
    ];

    foreach ([
      'operation_type',
      'asset_type',
      'surface_min',
      'surface_max',
      'budget_min',
      'budget_max',
      'capacity_min',
      'capacity_max',
      'map_bounds',
      'sort_by',
      'sort_order',
      'lang',
    ] as $key) {
      $value = $this->readScalar($request, $key);
      if ($value !== NULL && $value !== '') {
        $criteria[$key] = $value;
      }
    }

    $locations = $this->readScalar($request, 'locations');
    if ($locations !== NULL && $locations !== '') {
      $criteria['locations'] = $locations;
    }

    $localityTokens = $this->locationSearchFilter->extractTokensFromRequest($request);
    if ($localityTokens !== []) {
      $criteria['locality'] = $localityTokens;
    }

    $moreCriteria = $this->extractMoreCriteria($request);
    if ($moreCriteria !== []) {
      $criteria['more_criteria'] = $moreCriteria;
    }

    $searchUrl = $this->readScalar($request, 'search_url');
    $criteria['search_url'] = ($searchUrl !== NULL && $searchUrl !== '')
      ? $searchUrl
      : $request->getUri();

    $searchPath = $this->readScalar($request, 'search_path');
    $criteria['search_path'] = ($searchPath !== NULL && $searchPath !== '')
      ? $searchPath
      : $request->getPathInfo();

    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    if ($langcode !== '') {
      $criteria['langcode'] = $langcode;
    }

    return $this->normalize($criteria);
  }

  /**
   * Rebuilds a replay request from stored v2 criteria.
   *
   * @param array<string, mixed> $criteria
   *   Stored v2 criteria blob.
   */
  private function buildRequestFromContext(array $criteria): Request {
    $contextData = is_array($criteria['context'] ?? NULL) ? $criteria['context'] : [];
    $context = $this->contextStorageMapper->import($contextData);

    $path = is_string($criteria['search_path'] ?? NULL) && $criteria['search_path'] !== ''
      ? $criteria['search_path']
      : $this->contextSerializer->buildSeoPath($context, $context->langcode);

    $query = $this->contextSerializer->buildQueryParams($context);
    $query = $this->appendMoreCriteriaToQuery($query, $context->filters->moreCriteria);

    $request = Request::create($path, 'GET', $query);
    $request->attributes->set(SearchContext::REQUEST_ATTRIBUTE, $context);

    return $request;
  }

  /**
   * Rebuilds a replay request from stored legacy criteria.
   *
   * @param array<string, mixed> $criteria
   *   Stored legacy criteria blob.
   */
  private function buildLegacyRequest(array $criteria): Request {
    $query = [];
    foreach ($criteria as $key => $value) {
      if ($key === 'more_criteria' && is_array($value)) {
        foreach ($value as $filterKey => $filterValue) {
          if (is_array($filterValue)) {
            foreach ($filterValue as $item) {
              $query[sprintf('%s[]', $filterKey)][] = $item;
            }
          }
          else {
            $query[$filterKey] = $filterValue;
          }
        }
        continue;
      }
      if ($key === 'locality' && is_array($value)) {
        foreach ($value as $token) {
          $query['locality[]'][] = $token;
        }
        continue;
      }
      if (in_array($key, ['search_url', 'search_path', 'langcode', 'schema_version', 'context'], TRUE)) {
        continue;
      }
      $query[$key] = $value;
    }

    return Request::create('', 'GET', $query);
  }

  /**
   * Appends mf_* filters to a rebuilt query string.
   *
   * @param array<string, mixed> $query
   *   Base query parameters.
   * @param array<string, mixed> $moreCriteria
   *   Stored more-criteria values.
   *
   * @return array<string, mixed>
   *   Query with more-criteria appended.
   */
  private function appendMoreCriteriaToQuery(array $query, array $moreCriteria): array {
    foreach ($moreCriteria as $filterKey => $filterValue) {
      if (!is_string($filterKey) || !str_starts_with($filterKey, 'mf_')) {
        continue;
      }
      if (is_array($filterValue)) {
        foreach ($filterValue as $item) {
          $query[sprintf('%s[]', $filterKey)][] = $item;
        }
      }
      else {
        $query[$filterKey] = $filterValue;
      }
    }

    return $query;
  }

  /**
   * Whether stored criteria uses the SearchContext schema.
   *
   * @param array<string, mixed> $criteria
   *   Stored criteria blob.
   */
  private function isContextCriteria(array $criteria): bool {
    return (int) ($criteria['schema_version'] ?? self::SCHEMA_VERSION_LEGACY) >= self::SCHEMA_VERSION_CONTEXT
      && is_array($criteria['context'] ?? NULL);
  }

  /**
   * Normalizes a criteria array for stable storage and hashing.
   *
   * @param array<string, mixed> $criteria
   *   Raw criteria array.
   *
   * @return array<string, mixed>
   *   Sorted criteria array.
   */
  private function normalize(array $criteria): array {
    ksort($criteria);
    if (isset($criteria['locality']) && is_array($criteria['locality'])) {
      sort($criteria['locality']);
    }
    if (isset($criteria['more_criteria']) && is_array($criteria['more_criteria'])) {
      ksort($criteria['more_criteria']);
    }
    if (isset($criteria['context']) && is_array($criteria['context'])) {
      $criteria['context'] = $this->normalizeNested($criteria['context']);
    }
    return $criteria;
  }

  /**
   * Recursively sorts nested context arrays for stable hashing.
   *
   * @param array<string, mixed> $data
   *   Nested context array.
   *
   * @return array<string, mixed>
   *   Sorted nested array.
   */
  private function normalizeNested(array $data): array {
    ksort($data);
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $data[$key] = $this->normalizeNested($value);
      }
    }

    return $data;
  }

  /**
   * Extracts mf_* feature filter values from the request query.
   *
   * @return array<string, mixed>
   *   More criteria keyed by filter machine name.
   */
  private function extractMoreCriteria(Request $request): array {
    $more = [];
    foreach ($request->query->all() as $key => $value) {
      if (!is_string($key) || !str_starts_with($key, 'mf_')) {
        continue;
      }
      if (is_array($value)) {
        $more[$key] = array_values(array_filter($value, static fn ($item) => $item !== '' && $item !== NULL));
      }
      elseif ($value !== '' && $value !== NULL) {
        $more[$key] = $value;
      }
    }
    return $more;
  }

  /**
   * Reads a scalar query parameter from the request.
   */
  private function readScalar(Request $request, string $key): ?string {
    if (!$request->query->has($key)) {
      return NULL;
    }
    $value = $request->query->get($key);
    if (is_array($value)) {
      return NULL;
    }
    return (string) $value;
  }

}
