<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\EventSubscriber;

use Drupal\Core\Ajax\AjaxResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Strips fixed off-canvas width so Bootstrap theme defaults apply.
 */
final class FavoriteOffcanvasDialogSubscriber implements EventSubscriberInterface {

  public static function getSubscribedEvents(): array {
    return [
      KernelEvents::RESPONSE => 'onResponse',
    ];
  }

  public function onResponse(ResponseEvent $event): void {
    if ($event->getRequest()->attributes->get('_route') !== 'ps_favorite.offcanvas') {
      return;
    }

    $response = $event->getResponse();
    if (!$response instanceof AjaxResponse) {
      return;
    }

    $commands = &$response->getCommands();
    foreach ($commands as &$command) {
      if (($command['command'] ?? '') !== 'openDialog') {
        continue;
      }

      $classes = $command['dialogOptions']['classes']['ui-dialog'] ?? '';
      if (!str_contains($classes, 'ps-favorite-offcanvas')) {
        continue;
      }

      unset($command['dialogOptions']['width']);
    }
  }

}
