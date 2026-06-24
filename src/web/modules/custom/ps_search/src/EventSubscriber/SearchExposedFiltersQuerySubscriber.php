<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\Service\SearchExposedFiltersQueryNormalizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Normalizes flat range query params before Views exposed filters render.
 *
 * Priority 30 — after RouterListener (32) and SearchCanonicalRedirect (31).
 */
final class SearchExposedFiltersQuerySubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly SearchContextResolverInterface $contextResolver,
    private readonly SearchExposedFiltersQueryNormalizer $queryNormalizer,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [['onRequest', 30]],
    ];
  }

  /**
   * Normalizes flat range query params before Views exposed filters render.
   */
  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();
    if (!$this->contextResolver->isSearchRequest($request)) {
      return;
    }

    $this->queryNormalizer->normalize($request);
  }

}
