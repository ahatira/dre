<?php

declare(strict_types=1);

namespace Drupal\entity_browser_generic_embed\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Route subscriber placeholder for entity embed dialog overrides.
 */
final class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    // Intentionally left minimal for generic scaffold.
  }

}
