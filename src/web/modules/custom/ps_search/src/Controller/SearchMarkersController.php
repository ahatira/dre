<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Api\RequestValidator;
use Drupal\ps_search\Service\SearchMarkersBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns zone-scoped offer markers for the property search map.
 */
final class SearchMarkersController extends ControllerBase {

  public function __construct(
    private readonly SearchMarkersBuilder $markersBuilder,
    private readonly RequestValidator $requestValidator,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.markers_builder'),
      $container->get('ps_search.api.request_validator'),
    );
  }

  /**
   * Returns markers as JSON.
   */
  public function markers(Request $request): CacheableJsonResponse {
    $validationError = $this->requestValidator->validateBusinessFilters($request);
    if ($validationError !== NULL) {
      return $validationError;
    }

    $payload = $this->markersBuilder->build($request);

    $response = new CacheableJsonResponse($payload);
    $maxAge = max(0, (int) ($this->config('ps_search.api_cache_settings')->get('markers_ttl') ?? 60));
    $cache = (new CacheableMetadata())
      ->setCacheMaxAge($maxAge)
      ->setCacheTags([
        'search_api_list:offers',
        'config:ps_search.map_zone_settings',
        'config:ps_search.api_cache_settings',
      ])
      ->setCacheContexts(['url.query_args']);
    $response->addCacheableDependency($cache);
    $response->headers->set('X-Content-Type-Options', 'nosniff');

    return $response;
  }

}
