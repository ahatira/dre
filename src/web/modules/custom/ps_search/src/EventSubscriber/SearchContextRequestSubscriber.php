<?php

declare(strict_types=1);

namespace Drupal\ps_search\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\ps_search\Contract\SearchContextResolverInterface;
use Drupal\ps_search\ValueObject\SearchContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Attaches a resolved SearchContext to search HTTP requests.
 *
 * Runs at priority 34 — after cross-language slug redirect (33), before
 * canonical redirect (31). Re-parses SEO paths on every request so geo
 * context survives route cache hits (fix refresh SEO bug).
 */
final class SearchContextRequestSubscriber implements EventSubscriberInterface {

  private const ENGINE_SETTINGS = 'ps_search.engine_settings';

  public function __construct(
    private readonly SearchContextResolverInterface $contextResolver,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::REQUEST => [['onRequest', 34]],
    ];
  }

  public function onRequest(RequestEvent $event): void {
    if (!$event->isMainRequest()) {
      return;
    }

    if (!$this->isSearchContextEnabled()) {
      return;
    }

    $request = $event->getRequest();
    if (!$this->contextResolver->isSearchRequest($request)) {
      return;
    }

    $context = $this->contextResolver->resolve($request);
    $request->attributes->set(SearchContext::REQUEST_ATTRIBUTE, $context);
  }

  private function isSearchContextEnabled(): bool {
    $features = $this->configFactory->get(self::ENGINE_SETTINGS)->get('features') ?? [];
    return (bool) ($features['use_search_context'] ?? FALSE);
  }

}
