<?php

declare(strict_types=1);

namespace Drupal\ui_suite_bnp_companion;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\ui_suite_bnp_companion\EventSubscriber\ActiveLinkResponseFilter;

/**
 * Replace core's response_filter.active_link service with our own.
 */
class UiSuiteBootstrapCompanionServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    if ($container->hasDefinition('response_filter.active_link')) {
      $definition = $container->getDefinition('response_filter.active_link');
      $definition->setClass(ActiveLinkResponseFilter::class);
    }
  }

}
