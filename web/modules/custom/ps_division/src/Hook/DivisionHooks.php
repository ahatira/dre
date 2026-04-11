<?php

declare(strict_types=1);

namespace Drupal\ps_division\Hook;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\ps_division\Entity\DivisionInterface;

/**
 * Hook implementations for Division entity operations.
 */
final class DivisionHooks {

  /**
   * Implements hook_ENTITY_TYPE_insert() for ps_division.
   *
   * Invalidates division cache when entity is created.
   */
  #[Hook('ps_division_insert')]
  public function divisionInsert(EntityInterface $entity): void {
    if ($entity instanceof DivisionInterface) {
      Cache::invalidateTags(['ps_division_list']);
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_update() for ps_division.
   *
   * Invalidates division cache when entity is updated.
   */
  #[Hook('ps_division_update')]
  public function divisionUpdate(EntityInterface $entity): void {
    if ($entity instanceof DivisionInterface) {
      Cache::invalidateTags(['ps_division_list']);
    }
  }

  /**
   * Implements hook_ENTITY_TYPE_delete() for ps_division.
   *
   * Invalidates division cache when entity is deleted.
   */
  #[Hook('ps_division_delete')]
  public function divisionDelete(EntityInterface $entity): void {
    if ($entity instanceof DivisionInterface) {
      Cache::invalidateTags(['ps_division_list']);
    }
  }

}
