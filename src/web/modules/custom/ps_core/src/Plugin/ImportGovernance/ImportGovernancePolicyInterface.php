<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\ImportGovernance;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Domain import governance policy for CRM/XML and related imports.
 */
interface ImportGovernancePolicyInterface extends PluginInspectionInterface {

  /**
   * Returns the admin-facing domain label.
   */
  public function getAdminLabel(): string;

  /**
   * Returns the admin-facing domain description.
   */
  public function getAdminDescription(): string;

  /**
   * Returns the route name of the domain settings form, if any.
   */
  public function getSettingsRouteName(): ?string;

  /**
   * Returns the sort weight in the governance hub.
   */
  public function getWeight(): int;

  /**
   * Returns entity type IDs covered by this policy.
   *
   * @return string[]
   */
  public function getEntityTypeIds(): array;

  /**
   * Returns bundle IDs when entity types expose bundles (e.g. node:offer).
   *
   * Empty means all bundles of the listed entity types.
   *
   * @return string[]
   */
  public function getBundleIds(): array;

  /**
   * Whether a protected entity row should be skipped entirely during import.
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool;

  /**
   * Whether protected entities should preserve internal field values on import.
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool;

  /**
   * Resolves the effective lock strategy for logging and diagnostics.
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string;

  /**
   * Returns additional destination properties to preserve on import rows.
   *
   * Used for domain-specific display or business fields beyond field locks.
   *
   * @return string[]
   */
  public function getAdditionalPreservedProperties(EntityInterface $entity): array;

}
