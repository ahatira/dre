<?php

declare(strict_types=1);

namespace Drupal\ps_compare\Repository;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;

/**
 *
 */
final class CompareRepository implements CompareRepositoryInterface {

  public function __construct(
    private readonly Connection $database,
    private readonly TimeInterface $time,
  ) {}

  /**
   *
   */
  public function add(int $uid, string $entityTypeId, int $entityId): bool {
    if ($this->has($uid, $entityTypeId, $entityId)) {
      return FALSE;
    }

    $weight = $this->getNextWeight($uid);
    $this->database->insert('ps_compare_item')
      ->fields([
        'uid' => $uid,
        'entity_type' => $entityTypeId,
        'entity_id' => $entityId,
        'weight' => $weight,
        'created' => $this->time->getCurrentTime(),
      ])
      ->execute();

    return TRUE;
  }

  /**
   *
   */
  public function remove(int $uid, string $entityTypeId, int $entityId): bool {
    $deleted = $this->database->delete('ps_compare_item')
      ->condition('uid', $uid)
      ->condition('entity_type', $entityTypeId)
      ->condition('entity_id', $entityId)
      ->execute();

    return $deleted > 0;
  }

  /**
   *
   */
  public function has(int $uid, string $entityTypeId, int $entityId): bool {
    $query = $this->database->select('ps_compare_item', 'compare');
    $query->addExpression('1');
    $query->condition('uid', $uid);
    $query->condition('entity_type', $entityTypeId);
    $query->condition('entity_id', $entityId);
    $query->range(0, 1);

    return (bool) $query->execute()->fetchField();
  }

  /**
   *
   */
  public function getEntityIds(string $entityTypeId, int $uid): array {
    $query = $this->database->select('ps_compare_item', 'compare');
    $query->fields('compare', ['entity_id']);
    $query->condition('uid', $uid);
    $query->condition('entity_type', $entityTypeId);
    $query->orderBy('weight', 'ASC');

    return array_map('intval', $query->execute()->fetchCol());
  }

  /**
   *
   */
  public function count(string $entityTypeId, int $uid): int {
    $query = $this->database->select('ps_compare_item', 'compare');
    $query->addExpression('COUNT(*)');
    $query->condition('uid', $uid);
    $query->condition('entity_type', $entityTypeId);

    return (int) $query->execute()->fetchField();
  }

  /**
   *
   */
  public function getEntries(int $uid): array {
    $query = $this->database->select('ps_compare_item', 'compare');
    $query->fields('compare', ['entity_type', 'entity_id', 'weight']);
    $query->condition('uid', $uid);
    $query->orderBy('weight', 'ASC');

    $entries = [];
    foreach ($query->execute()->fetchAll() as $row) {
      $entries[] = [
        'entity_type' => (string) $row->entity_type,
        'entity_id' => (int) $row->entity_id,
        'weight' => (int) $row->weight,
      ];
    }

    return $entries;
  }

  /**
   *
   */
  public function removeByEntity(string $entityTypeId, int $entityId): int {
    return (int) $this->database->delete('ps_compare_item')
      ->condition('entity_type', $entityTypeId)
      ->condition('entity_id', $entityId)
      ->execute();
  }

  /**
   *
   */
  public function mergeEntityIds(int $uid, string $entityTypeId, array $entityIds): void {
    $entityIds = array_values(array_unique(array_map('intval', $entityIds)));
    foreach ($entityIds as $entityId) {
      $this->add($uid, $entityTypeId, $entityId);
    }
  }

  /**
   *
   */
  private function getNextWeight(int $uid): int {
    $query = $this->database->select('ps_compare_item', 'compare');
    $query->addExpression('MAX(weight)');
    $query->condition('uid', $uid);
    $max = $query->execute()->fetchField();

    return $max === NULL ? 0 : ((int) $max + 1);
  }

}
