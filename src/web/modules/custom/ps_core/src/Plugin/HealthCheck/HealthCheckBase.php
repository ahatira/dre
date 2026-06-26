<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\HealthCheck;

use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Base class for platform health check plugins.
 */
abstract class HealthCheckBase extends PluginBase implements HealthCheckInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getGroup(): string {
    return (string) ($this->pluginDefinition['group'] ?? 'general');
  }

  /**
   * Returns the translated plugin label.
   */
  protected function getLabel(): string {
    return (string) $this->pluginDefinition['label'];
  }

}
