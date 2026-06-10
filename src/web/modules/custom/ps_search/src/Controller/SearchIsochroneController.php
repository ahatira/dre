<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
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
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.isochrone'),
    );
  }

  /**
   * Builds an isochrone payload for map overlay and zone filtering.
   */
  public function isochrone(Request $request): JsonResponse {
    $latRaw = $request->query->get('lat');
    $lngRaw = $request->query->get('lng');
    if (!is_numeric($latRaw) || !is_numeric($lngRaw)) {
      return new JsonResponse(['error' => 'invalid_center'], 400);
    }

    $transport = strtolower(trim((string) $request->query->get('transport', 'walking')));
    $minutesRaw = $request->query->get('minutes');
    $minutes = is_numeric($minutesRaw) ? (int) $minutesRaw : 5;

    try {
      $payload = $this->isochroneService->build(
        (float) $latRaw,
        (float) $lngRaw,
        $transport,
        $minutes,
      );
    }
    catch (\InvalidArgumentException) {
      return new JsonResponse(['error' => 'invalid_parameters'], 400);
    }
    catch (\RuntimeException) {
      return new JsonResponse(['error' => 'isochrone_unavailable'], 503);
    }

    $response = new JsonResponse($payload);
    $response->setPrivate();
    $response->setMaxAge(300);
    return $response;
  }

}
