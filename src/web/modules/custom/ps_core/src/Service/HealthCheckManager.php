<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\ps_core\Annotation\HealthCheck;
use Drupal\ps_core\Plugin\HealthCheck\HealthCheckInterface;

/**
 * Discovers platform health check plugins from enabled modules.
 */
final class HealthCheckManager extends DefaultPluginManager {

  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler,
  ) {
    parent::__construct(
      'Plugin/HealthCheck',
      $namespaces,
      $module_handler,
      HealthCheckInterface::class,
      HealthCheck::class,
    );

    $this->alterInfo('ps_core_health_check_info');
    $this->setCacheBackend($cache_backend, 'ps_core_health_check_plugins');
  }

}
