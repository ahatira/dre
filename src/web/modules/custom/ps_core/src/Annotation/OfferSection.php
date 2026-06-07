<?php

declare(strict_types=1);

namespace Drupal\ps_core\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an offer detail section plugin.
 *
 * @Annotation
 */
final class OfferSection extends Plugin {

  /**
   * The plugin ID (config key).
   *
   * @var string
   */
  public string $id;

  /**
   * Human-readable name shown in admin settings.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $admin_label;

  /**
   * Default section heading label.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

  /**
   * Default UI Icon pack:id value.
   *
   * @var string
   */
  public string $default_icon = '';

  /**
   * Sort weight in the admin form.
   *
   * @var int
   */
  public int $weight = 0;

}
