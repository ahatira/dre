<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\OfferSection;

use Drupal\Core\Plugin\PluginBase;

/**
 * Base class for offer section plugins.
 */
abstract class OfferSectionBase extends PluginBase implements OfferSectionInterface {

  /**
   * {@inheritdoc}
   */
  public function getDefaultLabel(): string {
    return (string) ($this->pluginDefinition['label'] ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultIcon(): string {
    return (string) ($this->pluginDefinition['default_icon'] ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminLabel(): string {
    return (string) ($this->pluginDefinition['admin_label'] ?? $this->getDefaultLabel());
  }

}
