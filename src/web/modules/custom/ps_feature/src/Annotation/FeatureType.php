<?php

namespace Drupal\ps_feature\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Feature Type annotation object.
 *
 * @Annotation
 */
class FeatureType extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public string $id;

  /**
   * The human-readable name of the feature type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * A brief description of the feature type.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $description;

  /**
   * The JSON schema for the payload.
   *
   * @var array
   */
  public array $payload_schema = [];

}
