<?php

declare(strict_types=1);

namespace Drupal\ps_search\Service;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Serializes current search state into storable alert criteria.
 */
final class SearchAlertCriteriaSerializer {

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly LanguageManagerInterface $languageManager,
  ) {}

  /**
   * Builds normalized criteria from the current HTTP request.
   *
   * @return array<string, mixed>
   *   Flat criteria blob compatible with SearchQueryFactory replay.
   */
  public function fromRequest(Request $request): array {
    $criteria = [];

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
      if (in_array($key, ['search_url', 'search_path', 'langcode'], TRUE)) {
        continue;
      }
      $query[$key] = $value;
    }

    return Request::create('', 'GET', $query);
  }

  /**
   * Returns a stable normalized criteria array for storage and hashing.
   *
   * @param array<string, mixed> $criteria
   *
   * @return array<string, mixed>
   */
  public function normalizeCriteria(array $criteria): array {
    return $this->normalize($criteria);
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
    return $criteria;
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
