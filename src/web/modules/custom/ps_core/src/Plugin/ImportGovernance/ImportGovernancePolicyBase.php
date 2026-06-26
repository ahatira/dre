<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\PluginBase;

/**
 * Base class for import governance policy plugins.
 */
abstract class ImportGovernancePolicyBase extends PluginBase implements ImportGovernancePolicyInterface {

  /**
   * {@inheritdoc}
   */
  public function getAdminLabel(): string {
    return (string) ($this->pluginDefinition['admin_label'] ?? $this->getPluginId());
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminDescription(): string {
    return (string) ($this->pluginDefinition['description'] ?? '');
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsRouteName(): ?string {
    $route = trim((string) ($this->pluginDefinition['settings_route'] ?? ''));
    return $route !== '' ? $route : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    return (int) ($this->pluginDefinition['weight'] ?? 0);
  }

  /**
   * {@inheritdoc}
   */
  public function getBundleIds(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalPreservedProperties(EntityInterface $entity): array {
    return [];
  }

}
