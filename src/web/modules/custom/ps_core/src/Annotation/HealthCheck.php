<?php

declare(strict_types=1);

namespace Drupal\ps_core\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Property Search platform health check plugin.
 *
 * @Annotation
 */
final class HealthCheck extends Plugin {

  /**
   * The plugin ID.
   */
  public string $id;

  /**
   * Admin label shown on the health dashboard.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * Group key used to cluster cards on the overview page.
   */
  public string $group;

  /**
   * Sort weight within the group.
   */
  public int $weight = 0;

}
