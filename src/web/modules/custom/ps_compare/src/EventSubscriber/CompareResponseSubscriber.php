<?php

declare(strict_types=1);

namespace Drupal\ps_compare\EventSubscriber;

use Drupal\ps_compare\Service\CompareCookieState;
use Drupal\ps_compare\Service\CompareCookieStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 *
 */
final class CompareResponseSubscriber implements EventSubscriberInterface {

  public function __construct(
    private readonly CompareCookieStorage $cookieStorage,
    private readonly CompareCookieState $cookieState,
  ) {}

  /**
   *
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => 'onResponse',
    ];
  }

  /**
   *
   */
  public function onResponse(ResponseEvent $event): void {
    if (!$this->cookieState->hasPendingChanges()) {
      return;
    }

    $items = $this->cookieStorage->getAllItems();
    foreach ($this->cookieState->getClearedEntityTypes() as $entityTypeId) {
      unset($items[$entityTypeId]);
    }
    foreach ($this->cookieState->getOverrides() as $entityTypeId => $entityIds) {
      $items[$entityTypeId] = $entityIds;
    }

    $response = $event->getResponse();
    if ($items === []) {
      $response->headers->setCookie($this->cookieStorage->createExpiredCookie());
      return;
    }

    $response->headers->setCookie($this->cookieStorage->createCookie($items));
  }

}
