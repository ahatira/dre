<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Api\RequestValidator;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\ps_search\Service\LocationSuggestBuilder;
use Drupal\ps_search\Service\SearchFilterQueryBuilder;
use Drupal\ps_search\Service\SearchSolrCircuitBreaker;
use Drupal\search_api\Entity\Index;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * JSON endpoints for search counts and location autocomplete.
 */
final class SearchCountController extends ControllerBase {

  public function __construct(
    private readonly LocationSearchFilter $locationSearchFilter,
    private readonly SearchFilterQueryBuilder $filterQueryBuilder,
    private readonly LocationSuggestBuilder $locationSuggestBuilder,
    private readonly RequestValidator $requestValidator,
    private readonly ?SearchSolrCircuitBreaker $circuitBreaker = NULL,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.location_filter'),
      $container->get('ps_search.filter_query_builder'),
      $container->get('ps_search.location_suggest_builder'),
      $container->get('ps_search.api.request_validator'),
      $container->get('ps_search.solr_circuit_breaker'),
    );
  }

  /**
   * Returns result count as JSON.
   */
  public function count(Request $request): JsonResponse {
    $validationError = $this->requestValidator->validateBusinessFilters($request);
    if ($validationError !== NULL) {
      return $validationError;
    }

    if ($this->circuitBreaker?->isUnavailable()) {
      return new JsonResponse(['count' => 0, 'error' => 'query_failed'], 200);
    }

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
      $this->circuitBreaker?->recordSuccess();
    }
    catch (\Throwable $exception) {
      $this->circuitBreaker?->recordFailure($exception);
      return new JsonResponse(['count' => 0, 'error' => 'query_failed'], 200);
    }

    $response = new JsonResponse(['count' => $count]);
    $response->setMaxAge(60);
    $response->setPublic();
    $response->headers->set('X-Content-Type-Options', 'nosniff');

    return $response;
  }

  /**
   * Returns location suggestions for autocomplete.
   */
  public function suggest(Request $request): JsonResponse {
    $validationError = $this->requestValidator->validateLocationSuggest($request);
    if ($validationError !== NULL) {
      return $validationError;
    }

    $query = $this->requestValidator->sanitizeText($request->query->get('q'));
    if ($query === NULL || mb_strlen($query) < 2) {
      return new JsonResponse(['groups' => [], 'suggestions' => []]);
    }

    $limitRaw = $request->query->get('limit');
    $limit = is_numeric($limitRaw) ? (int) $limitRaw : 8;

    $payload = $this->locationSuggestBuilder->build($query, $limit);

    $response = new JsonResponse($payload);
    $response->setPrivate();
    $response->setMaxAge(60);
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    return $response;
  }

  /**
   * Fetches structured location data for multiple cities.
   */
  public function locationData(Request $request): JsonResponse {
    $validationError = $this->requestValidator->validateLocationData($request);
    if ($validationError !== NULL) {
      return $validationError;
    }

    $localitiesRaw = $request->query->all('localities');
    if (!is_array($localitiesRaw) || empty($localitiesRaw)) {
      return new JsonResponse(['data' => []]);
    }

    $localities = array_slice(array_map(fn($l) => $this->requestValidator->sanitizeText($l), $localitiesRaw), 0, 10);
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
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    return $response;
  }

}
