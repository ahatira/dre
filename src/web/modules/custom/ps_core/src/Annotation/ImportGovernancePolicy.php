<?php

declare(strict_types=1);

namespace Drupal\ps_core\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an import governance policy plugin for a PS domain.
 *
 * @Annotation
 */
final class ImportGovernancePolicy extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public string $id;

  /**
   * Admin label shown in the governance hub.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $admin_label;

  /**
   * Short description for the governance hub.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $description;

  /**
   * Route name of the domain governance settings form.
   *
   * @var string
   */
  public string $settings_route = '';

  /**
   * Sort weight in the governance hub.
   *
   * @var int
   */
  public int $weight = 0;

}
