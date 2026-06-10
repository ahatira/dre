<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\LocationSuggestBuilder;
use Drupal\ps_search\Service\SearchFilterQueryBuilder;
use Drupal\search_api\Entity\Index;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns the number of search results matching the given filter parameters.
 *
 * Used by the Search Filter Bar JS to update the "Afficher X résultats" button
 * in real time without navigating away from the current page.
 *
 * GET /ps-search/count
 *   ?operation_type=LOC      (optional)
 *   &asset_type=BUR          (optional)
 *   &locality=Paris          (optional, free text — approximate)
 *   &surface_min=100         (optional, positive number in m²)
 *   &surface_max=500         (optional, positive number in m²)
 *   &budget_min=100          (optional, positive number in €/m²/year)
 *   &budget_max=5000         (optional, positive number in €/m²/year)
 */
final class SearchCountController extends ControllerBase {

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
    private readonly LocationSuggestBuilder $locationSuggestBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.location_filter'),
      $container->get('ps_search.filter_query_builder'),
      $container->get('ps_search.location_suggest_builder'),
    );
  }

  /**
   * Returns result count as JSON.
   */
  public function count(Request $request): JsonResponse {
    $index = Index::load('offers');
    if (!$index) {
      return new JsonResponse(['count' => 0, 'error' => 'index_unavailable'], 503);
    }

    $query = $index->query();
    $query->range(0, 0);
    $this->filterQueryBuilder->applyBusinessFilters($query, $request);

    try {
      $results = $query->execute();
      $count = (int) $results->getResultCount();
    }
    catch (\Exception) {
      return new JsonResponse(['count' => 0, 'error' => 'query_failed'], 200);
    }

    $response = new JsonResponse(['count' => $count]);
    // Short cache: count changes when offers are added/removed.
    $response->setMaxAge(60);
    $response->setPublic();

    return $response;
  }

  /**
   * Returns location suggestions for autocomplete.
   */
  public function suggest(Request $request): JsonResponse {
    $queryRaw = $request->query->get('q');
    $query = $this->sanitizeText($queryRaw);
    if ($query === NULL || mb_strlen($query) < 2) {
      return new JsonResponse(['groups' => [], 'suggestions' => []]);
    }

    $limitRaw = $request->query->get('limit');
    $limit = is_numeric($limitRaw) ? (int) $limitRaw : 8;

    $payload = $this->locationSuggestBuilder->build($query, $limit);

    $response = new JsonResponse($payload);
    $response->setPrivate();
    $response->setMaxAge(60);
    return $response;
  }

  /**
   * Fetches structured location data for multiple cities.
   *
   * Endpoint: /ps-search/location-data?localities[]=Paris&localities[]=Nancy
   */
  public function locationData(Request $request): JsonResponse {
    $localitiesRaw = $request->query->all('localities');
    if (!is_array($localitiesRaw) || empty($localitiesRaw)) {
      return new JsonResponse(['data' => []]);
    }

    $localities = array_slice(array_map(fn($l) => $this->sanitizeText($l), $localitiesRaw), 0, 10);
    $localities = array_filter($localities, fn($l) => $l !== NULL);

    if (empty($localities)) {
      return new JsonResponse(['data' => []]);
    }

    $data = [];
    foreach ($localities as $token) {
      $data[] = $this->locationSearchFilter->resolveTokenMetadata($token);
    }

    $response = new JsonResponse(['data' => $data]);
    $response->setPrivate();
    $response->setMaxAge(60);
    return $response;
  }

  /**
   * Sanitizes free text: letters, digits, spaces, hyphens, apostrophes — max 100 chars.
   */
  private function sanitizeText(mixed $value): ?string {
    if (!is_string($value) || trim($value) === '') {
      return NULL;
    }
    $cleaned = preg_replace('/[^\p{L}\p{N}\s\-\']/u', '', substr(trim($value), 0, 100));
    return $cleaned !== '' ? $cleaned : NULL;
  }

}
