<?php

declare(strict_types=1);

namespace Drupal\ps_feature\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Modifies translation routes to use custom forms.
 */
class TranslationRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    // Override FeatureDefinition translation routes to use custom form.
    if ($route = $collection->get('config_translation.item.add.entity.fb_feature_definition.edit_form')) {
      $route->setDefault('_form', '\Drupal\ps_feature\Form\FeatureDefinitionTranslationForm');
    }
    
    if ($route = $collection->get('config_translation.item.edit.entity.fb_feature_definition.edit_form')) {
      $route->setDefault('_form', '\Drupal\ps_feature\Form\FeatureDefinitionTranslationForm');
    }
    
    // Override FeatureGroup translation routes to use custom form.
    if ($route = $collection->get('config_translation.item.add.entity.fb_feature_group.edit_form')) {
      $route->setDefault('_form', '\Drupal\ps_feature\Form\FeatureGroupTranslationForm');
    }
    
    if ($route = $collection->get('config_translation.item.edit.entity.fb_feature_group.edit_form')) {
      $route->setDefault('_form', '\Drupal\ps_feature\Form\FeatureGroupTranslationForm');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    // Run after config_translation module's route subscriber (priority -217).
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -300];
    return $events;
  }

}
