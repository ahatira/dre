<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Service\SearchMarkersBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns zone-scoped offer markers for the property search map.
 */
final class SearchMarkersController extends ControllerBase {

  public function __construct(
    private readonly SearchMarkersBuilder $markersBuilder,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.markers_builder'),
    );
  }

  /**
   * Returns markers as JSON.
   */
  public function markers(Request $request): JsonResponse {
    $payload = $this->markersBuilder->build($request);
    $response = new JsonResponse($payload);
    $response->setMaxAge(60);
    $response->setPublic();
    $response->headers->set('Cache-Control', 'public, max-age=60');

    return $response;
  }

}
