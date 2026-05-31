<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Repository;

interface FavoriteRepositoryInterface {

  public function add(int $uid, string $entityTypeId, int $entityId): bool;

  public function remove(int $uid, string $entityTypeId, int $entityId): bool;

  public function has(int $uid, string $entityTypeId, int $entityId): bool;

  /**
   * @return int[]
   *   Entity IDs ordered by most recently changed first.
   */
  public function getEntityIds(string $entityTypeId, int $uid, int $limit = 0, int $offset = 0): array;

  public function count(string $entityTypeId, int $uid): int;

  /**
   * @return array<int, array{entity_type: string, entity_id: int}>
   *   Favorite entries ordered by most recent first.
   */
  public function getEntries(int $uid, int $limit = 0, int $offset = 0): array;

  public function removeByEntity(string $entityTypeId, int $entityId): int;

  /**
   * @param int[] $entityIds
   *   Entity IDs to merge.
   */
  public function mergeEntityIds(int $uid, string $entityTypeId, array $entityIds): void;
}
