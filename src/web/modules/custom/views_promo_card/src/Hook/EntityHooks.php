<?php

declare(strict_types=1);

namespace Drupal\views_promo_card\Hook;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;

/**
 * Entity hooks for cache invalidation.
 */
final class EntityHooks {

  /**
   * Constructs EntityHooks.
   */
  public function __construct(
    private readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator,
  ) {}

  /**
   * Implements hook_entity_insert().
   */
  #[Hook('entity_insert')]
  public function entityInsert(EntityInterface $entity): void {
    $this->invalidateForEntity($entity);
  }

  /**
   * Implements hook_entity_update().
   */
  #[Hook('entity_update')]
  public function entityUpdate(EntityInterface $entity): void {
    $this->invalidateForEntity($entity);
  }

  /**
   * Implements hook_entity_delete().
   */
  #[Hook('entity_delete')]
  public function entityDelete(EntityInterface $entity): void {
    $this->invalidateForEntity($entity);
  }

  /**
   * Invalidates promo card cache tags.
   */
  private function invalidateForEntity(EntityInterface $entity): void {
    $type = $entity->getEntityTypeId();
    if (!in_array($type, ['promo_card', 'promo_card_placement'], TRUE)) {
      return;
    }
    $this->cacheTagsInvalidator->invalidateTags([
      'promo_card_list',
      'promo_card_placement_list',
    ]);
  }

}
