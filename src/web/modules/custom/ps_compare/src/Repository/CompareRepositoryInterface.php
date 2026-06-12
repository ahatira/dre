<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Repository;

/**
 *
 */
interface CompareRepositoryInterface {

  /**
   *
   */
  public function add(int $uid, string $entityTypeId, int $entityId): bool;

  /**
   *
   */
  public function remove(int $uid, string $entityTypeId, int $entityId): bool;

  /**
   *
   */
  public function has(int $uid, string $entityTypeId, int $entityId): bool;

  /**
   * @return int[]
   *   Entity IDs in FIFO order (weight ASC).
   */
  public function getEntityIds(string $entityTypeId, int $uid): array;

  /**
   *
   */
  public function count(string $entityTypeId, int $uid): int;

  /**
   * @return array<int, array{entity_type: string, entity_id: int, weight: int}>
   *   Entries in FIFO order.
   */
  public function getEntries(int $uid): array;

  /**
   *
   */
  public function removeByEntity(string $entityTypeId, int $entityId): int;

  /**
   * @param int[] $entityIds
   *   Entity IDs to merge in FIFO order.
   */
  public function mergeEntityIds(int $uid, string $entityTypeId, array $entityIds): void;

}
