<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Api\RequestValidator;
use Drupal\ps_search\Service\IsochroneService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns isochrone geometry and map_bounds for the distance zone UI.
 */
final class SearchIsochroneController extends ControllerBase {

  public function __construct(
    private readonly IsochroneService $isochroneService,
    private readonly RequestValidator $requestValidator,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.isochrone'),
      $container->get('ps_search.api.request_validator'),
    );
  }

  /**
   * Builds an isochrone payload for map overlay and zone filtering.
   */
  public function isochrone(Request $request): JsonResponse {
    $validationError = $this->requestValidator->validateIsochrone($request);
    if ($validationError !== NULL) {
      return $validationError;
    }

    $lat = (float) $request->query->get('lat');
    $lng = (float) $request->query->get('lng');
    $transport = $this->requestValidator->parseIsochroneTransport($request);
    $minutes = $this->requestValidator->parseIsochroneMinutes($request);

    try {
      $payload = $this->isochroneService->build($lat, $lng, $transport, $minutes);
    }
    catch (\InvalidArgumentException) {
      return new JsonResponse(['error' => 'invalid_parameters'], 400);
    }
    catch (\RuntimeException) {
      return new JsonResponse(['error' => 'isochrone_unavailable'], 503);
    }

    $ttl = max(60, (int) ($this->config('ps_search.api_cache_settings')->get('isochrone_ttl') ?? 86400));
    $response = new JsonResponse($payload);
    $response->setPrivate();
    $response->setMaxAge(min($ttl, 86400));
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    return $response;
  }

}
