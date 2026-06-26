<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Entity\EntityInterface;

/**
 * Applies configured snapshot field values to governed entities.
 */
final class ImportGovernanceSnapshotSynchronizer {

  public function __construct(
    private readonly EntityProtectionManagerInterface $protectionManager,
  ) {}

  /**
   * Synchronizes entity fields from a snapshot row.
   *
   * @param array<string, mixed> $snapshot
   *   Snapshot values keyed by field or property name.
   * @param string[] $fieldNames
   *   Fields to synchronize.
   * @param string $strategy
   *   Merge strategy passed to the protection manager.
   *
   * @return bool
   *   TRUE when at least one field changed.
   */
  public function synchronizeFields(
    EntityInterface $entity,
    array $snapshot,
    array $fieldNames,
    string $strategy = 'EXTERNAL_WINS',
  ): bool {
    $changed = FALSE;

    foreach ($fieldNames as $fieldName) {
      if (!is_string($fieldName) || $fieldName === '') {
        continue;
      }
      if ($this->protectionManager->applyMergeStrategy($entity, $snapshot, $fieldName, $strategy)) {
        $changed = TRUE;
      }
    }

    return $changed;
  }

}
