<?php

declare(strict_types=1);

namespace Drupal\ps_core\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\MetadataBag;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Persists anonymous sessions when a CSRF seed was generated.
 *
 * Drupal discards sessions that only contain metadata (CSRF seed) and never
 * sends a session cookie. Anonymous AJAX actions then fail CSRF validation
 * because each request starts with a new session.
 */
final class AnonymousCsrfSessionSubscriber implements EventSubscriberInterface {

  private const SESSION_KEY = 'ps_anonymous_csrf';

  public function __construct(
    private readonly AccountProxyInterface $currentUser,
    private readonly MetadataBag $metadataBag,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => ['onResponse', -1024],
    ];
  }

  public function onResponse(ResponseEvent $event): void {
    if (!$event->isMainRequest() || $this->currentUser->isAuthenticated()) {
      return;
    }

    if ($this->metadataBag->getCsrfTokenSeed() === NULL) {
      return;
    }

    $session = $event->getRequest()->getSession();
    if ($session->get(self::SESSION_KEY) !== 1) {
      $session->set(self::SESSION_KEY, 1);
    }
  }

}
