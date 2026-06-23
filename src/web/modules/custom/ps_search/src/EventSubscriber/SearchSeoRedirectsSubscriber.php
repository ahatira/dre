<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\ps_search\Service\SearchSeoRedirectsReader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Applies configured SEO migration redirects as 301 after canonical handling.
 *
 * Priority 30 — runs after SearchCanonicalRedirectSubscriber (31).
 */
final class SearchSeoRedirectsSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly SearchSeoRedirectsReader $redirectsReader,
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
   * Redirects legacy SEO paths to their configured canonical targets.
   */
  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    $request = $event->getRequest();
    if ($request->isXmlHttpRequest()) {
      return;
    }

    if ($event->hasResponse()) {
      return;
    }

    $targetPath = $this->redirectsReader->resolveTarget($request->getPathInfo());
    if ($targetPath === NULL) {
      return;
    }

    $location = $this->buildRedirectLocation($request, $targetPath);
    $event->setResponse(new RedirectResponse($location, 301));
  }

  /**
   * Builds the redirect Location header, preserving the query string.
   */
  private function buildRedirectLocation(Request $request, string $targetPath): string {
    if (str_starts_with($targetPath, 'http://') || str_starts_with($targetPath, 'https://')) {
      return $targetPath;
    }

    $queryString = $request->getQueryString();
    if ($queryString !== NULL && $queryString !== '') {
      return $targetPath . '?' . $queryString;
    }

    return $targetPath;
  }

}
