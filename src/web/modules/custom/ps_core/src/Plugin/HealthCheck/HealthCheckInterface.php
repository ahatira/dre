<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\HealthCheck;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\ps_core\HealthCheck\HealthCheckResult;

/**
 * Platform health check plugin.
 */
interface HealthCheckInterface extends PluginInspectionInterface {

  /**
   * Returns the group key for dashboard clustering.
   */
  public function getGroup(): string;

  /**
   * Runs the health check.
   */
  public function run(): HealthCheckResult;

}
