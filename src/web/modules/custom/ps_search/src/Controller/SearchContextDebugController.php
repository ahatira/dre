<?php

declare(strict_types=1);

namespace Drupal\ps_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\ps_search\Contract\SearchContextSerializerInterface;
use Drupal\ps_search\Service\SearchEngineSettingsReader;
use Drupal\ps_search\ValueObject\SearchContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Debug endpoint exposing the resolved SearchContext for the current request.
 */
final class SearchContextDebugController extends ControllerBase {

  public function __construct(
    private readonly SearchContextSerializerInterface $serializer,
    private readonly SearchEngineSettingsReader $engineSettings,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('ps_search.context_serializer'),
      $container->get('ps_search.engine_settings_reader'),
    );
  }

  /**
   * Returns resolved search context as JSON (admin / dev tooling).
   */
  public function context(Request $request): JsonResponse {
    if (!$this->engineSettings->isSearchContextEnabled()) {
      return new JsonResponse(['error' => 'search_context_disabled'], 403);
    }

    if (!$this->currentUser()->hasPermission('access ps_core config section')) {
      return new JsonResponse(['error' => 'access_denied'], 403);
    }

    $resolved = $this->serializer->fromRequest($request);
    $request->attributes->set(SearchContext::REQUEST_ATTRIBUTE, $resolved);

    return new JsonResponse([
      'context' => $this->serializer->toArray($resolved),
      'seoPath' => $this->serializer->buildSeoPath($resolved, $resolved->langcode),
      'query' => $this->serializer->buildQueryParams($resolved),
    ]);
  }

}
