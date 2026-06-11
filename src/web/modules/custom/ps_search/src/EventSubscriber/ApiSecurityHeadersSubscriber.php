<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\ps_search\Api\ApiRoutePaths;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Adds security headers to ps_search API responses.
 */
final class ApiSecurityHeadersSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => [['onResponse', 0]],
    ];
  }

  /**
   * Adds nosniff header to API responses.
   */
  public function onResponse(ResponseEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $routeName = $this->routeMatch->getRouteName();
    if ($routeName === NULL || !in_array($routeName, ApiRoutePaths::protectedRouteNames(), TRUE)) {
      return;
    }

    $response = $event->getResponse();
    $response->headers->set('X-Content-Type-Options', 'nosniff');
  }

}
