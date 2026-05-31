<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Agent entities with bundle support.
 */
final class AgentHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type): \Symfony\Component\Routing\RouteCollection {
    $collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();

    // Override the add-form route to include the bundle parameter.
    if ($add_form_route = $this->getAddFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.add_form", $add_form_route);
    }

    // Add the add-page route which redirects to the add-form.
    if ($add_page_route = $this->getAddPageRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.add_page", $add_page_route);
    }

    return $collection;
  }

  /**
   * Gets the add-form route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getAddFormRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('add-form')) {
      $entity_type_id = $entity_type->id();
      $bundle_entity_type_id = $entity_type->getBundleEntityType();
      $route = new Route($entity_type->getLinkTemplate('add-form'));
      $route
        ->setDefaults([
          '_entity_form' => "{$entity_type_id}.add",
          'entity_type_id' => $entity_type_id,
          '_title_callback' => '\Drupal\Core\Entity\Controller\EntityController::addBundleTitle',
          'bundle_parameter' => $bundle_entity_type_id,
        ])
        ->setRequirement('_entity_create_access', "{$entity_type_id}:{{$bundle_entity_type_id}}")
        ->setOption('parameters', [
          $bundle_entity_type_id => ['type' => "entity:{$bundle_entity_type_id}"],
        ]);

      return $route;
    }

    return NULL;
  }

  /**
   * Gets the add-page route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getAddPageRoute(EntityTypeInterface $entity_type): ?Route {
    if ($entity_type->hasLinkTemplate('add-page')) {
      $entity_type_id = $entity_type->id();
      $route = new Route($entity_type->getLinkTemplate('add-page'));
      $route
        ->setDefaults([
          '_controller' => '\Drupal\Core\Entity\Controller\EntityController::addPage',
          '_title_callback' => '\Drupal\Core\Entity\Controller\EntityController::addTitle',
          'entity_type_id' => $entity_type_id,
        ])
        ->setRequirement('_entity_create_any_access', $entity_type_id);

      return $route;
    }

    return NULL;
  }

}
