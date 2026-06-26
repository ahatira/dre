<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;

/**
 * Optional XML snapshot post-import rules for content or config entities.
 */
interface ImportGovernanceSnapshotPostImportPolicyInterface extends ImportGovernanceSnapshotFieldSyncPolicyInterface {

  /**
   * Migration IDs handled by this post-import policy.
   *
   * @return string[]
   *   Migrate migration plugin IDs.
   */
  public function getSupportedMigrationIds(): array;

  /**
   * Whether entities present in the XML snapshot should be reactivated.
   */
  public function shouldReactivatePresentInSnapshot(): bool;

  /**
   * Whether an entity should be deactivated when absent from the XML snapshot.
   */
  public function shouldDeactivateMissingEntity(
    EntityInterface $entity,
    bool $shouldBeActive,
  ): bool;

}
