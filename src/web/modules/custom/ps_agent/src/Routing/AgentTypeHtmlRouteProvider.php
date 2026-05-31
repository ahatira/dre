<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for AgentType entities.
 *
 * Extends AdminHtmlRouteProvider to disable translation links on collection.
 */
final class AgentTypeHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type): ?Route {
    $route = parent::getCollectionRoute($entity_type);

    if ($route) {
      // Disable translation tab on collection page.
      $route->setOption('_admin_route', TRUE);
      $route->setOption('_config_translation_entity_type', NULL);
    }

    return $route;
  }

}
