<?php

declare(strict_types=1);

namespace Drupal\ps_favorite\Repository;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;

final class FavoriteRepository implements FavoriteRepositoryInterface {

  public function __construct(
    private readonly Connection $database,
    private readonly TimeInterface $time,
  ) {}

  public function add(int $uid, string $entityTypeId, int $entityId): bool {
    if ($this->has($uid, $entityTypeId, $entityId)) {
      return FALSE;
    }

    $timestamp = $this->time->getCurrentTime();
    $this->database->insert('ps_favorite_item')
      ->fields([
        'uid' => $uid,
        'entity_type' => $entityTypeId,
        'entity_id' => $entityId,
        'created' => $timestamp,
        'changed' => $timestamp,
      ])
      ->execute();

    return TRUE;
  }

  public function remove(int $uid, string $entityTypeId, int $entityId): bool {
    $deleted = $this->database->delete('ps_favorite_item')
      ->condition('uid', $uid)
      ->condition('entity_type', $entityTypeId)
      ->condition('entity_id', $entityId)
      ->execute();

    return $deleted > 0;
  }

  public function has(int $uid, string $entityTypeId, int $entityId): bool {
    $query = $this->database->select('ps_favorite_item', 'favorite');
    $query->addExpression('1');
    $query->condition('uid', $uid);
    $query->condition('entity_type', $entityTypeId);
    $query->condition('entity_id', $entityId);
    $query->range(0, 1);

    return (bool) $query->execute()->fetchField();
  }

  public function getEntityIds(string $entityTypeId, int $uid, int $limit = 0, int $offset = 0): array {
    $query = $this->database->select('ps_favorite_item', 'favorite');
    $query->fields('favorite', ['entity_id']);
    $query->condition('uid', $uid);
    $query->condition('entity_type', $entityTypeId);
    $query->orderBy('changed', 'DESC');
    if ($limit > 0) {
      $query->range($offset, $limit);
    }

    return array_map('intval', $query->execute()->fetchCol());
  }

  public function count(string $entityTypeId, int $uid): int {
    $query = $this->database->select('ps_favorite_item', 'favorite');
    $query->addExpression('COUNT(*)');
    $query->condition('uid', $uid);
    $query->condition('entity_type', $entityTypeId);

    return (int) $query->execute()->fetchField();
  }

  public function getEntries(int $uid, int $limit = 0, int $offset = 0): array {
    $query = $this->database->select('ps_favorite_item', 'favorite');
    $query->fields('favorite', ['entity_type', 'entity_id']);
    $query->condition('uid', $uid);
    $query->orderBy('changed', 'DESC');
    if ($limit > 0) {
      $query->range($offset, $limit);
    }

    $entries = [];
    foreach ($query->execute()->fetchAll() as $row) {
      $entries[] = [
        'entity_type' => (string) $row->entity_type,
        'entity_id' => (int) $row->entity_id,
      ];
    }

    return $entries;
  }

  public function removeByEntity(string $entityTypeId, int $entityId): int {
    return $this->database->delete('ps_favorite_item')
      ->condition('entity_type', $entityTypeId)
      ->condition('entity_id', $entityId)
      ->execute();
  }

  public function mergeEntityIds(int $uid, string $entityTypeId, array $entityIds): void {
    $entityIds = array_values(array_unique(array_map('intval', $entityIds)));
    foreach ($entityIds as $entityId) {
      $this->add($uid, $entityTypeId, $entityId);
    }
  }

}
