<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\ps_search\Api\ApiRateLimitService;
use Drupal\ps_search\Api\ApiRoutePaths;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Applies IP rate limits to ps_search public API routes.
 */
final class ApiRateLimitSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly ApiRateLimitService $rateLimitService,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [['onRequest', 28]],
    ];
  }

  /**
   * Blocks requests that exceed configured per-route IP limits.
   */
  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $routeName = $this->routeMatch->getRouteName();
    if ($routeName === NULL) {
      return;
    }

    $bucket = ApiRoutePaths::rateLimitBuckets()[$routeName] ?? NULL;
    if ($bucket === NULL) {
      return;
    }

    $request = $event->getRequest();
    if ($this->rateLimitService->isAllowed($request, $bucket)) {
      return;
    }

    $route = $this->routeMatch->getRouteObject();
    $isHtmx = (bool) ($route?->getOption('_htmx_route') ?? FALSE);
    if ($isHtmx) {
      $response = new Response('Rate limit exceeded.', Response::HTTP_TOO_MANY_REQUESTS);
      $response->headers->set('Retry-After', '60');
      $event->setResponse($response);
      return;
    }

    $response = new JsonResponse(['error' => 'rate_limit_exceeded'], Response::HTTP_TOO_MANY_REQUESTS);
    $response->headers->set('Retry-After', '60');
    $response->headers->set('X-Content-Type-Options', 'nosniff');
    $event->setResponse($response);
  }

}
