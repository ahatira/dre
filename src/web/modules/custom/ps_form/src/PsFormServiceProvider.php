<?php

declare(strict_types=1);

namespace Drupal\ps_form;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\ps_form\Form\PsFormErrorHandler;

/**
 * Overrides the form error handler to hide the top error summary.
 */
final class PsFormServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    if ($container->hasDefinition('form_error_handler')) {
      $container->getDefinition('form_error_handler')
        ->setClass(PsFormErrorHandler::class);
    }
  }

}
