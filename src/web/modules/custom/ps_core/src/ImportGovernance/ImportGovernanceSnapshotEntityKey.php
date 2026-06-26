<?php

declare(strict_types=1);

namespace Drupal\ps_core\ImportGovernance;

/**
 * Encodes entity type and bundle into a snapshot sync configuration key.
 */
final class ImportGovernanceSnapshotEntityKey {

  /**
   * Builds a configuration key for an entity type and optional bundle.
   */
  public static function encode(string $entityTypeId, ?string $bundle = NULL): string {
    $entityTypeId = trim($entityTypeId);
    $bundle = trim((string) $bundle);

    if ($bundle !== '') {
      return $entityTypeId . '.' . $bundle;
    }

    return $entityTypeId;
  }

  /**
   * Decodes a configuration key into entity type and bundle parts.
   *
   * @return array{entity_type_id: string, bundle: string|null}
   *   Decoded entity identifiers.
   */
  public static function decode(string $entityKey): array {
    $entityKey = trim($entityKey);
    $parts = explode('.', $entityKey, 2);

    return [
      'entity_type_id' => $parts[0],
      'bundle' => isset($parts[1]) && $parts[1] !== '' ? $parts[1] : NULL,
    ];
  }

}
