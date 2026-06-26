<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\ImportGovernance;

/**
 * Optional XML snapshot field synchronization rules for governed entities.
 */
interface ImportGovernanceSnapshotFieldSyncPolicyInterface {

  /**
   * Returns entity keys covered by snapshot field synchronization.
   *
   * @return string[]
   *   Keys encoded via ImportGovernanceSnapshotEntityKey.
   */
  public function getSnapshotFieldSyncEntityKeys(): array;

  /**
   * Returns configured snapshot sync fields for an entity key.
   *
   * @return string[]
   *   Normalized field or property names.
   */
  public function getSnapshotFieldSyncFields(string $entityKey): array;

}
