<?php

declare(strict_types=1);

namespace Drupal\ps_homepage\Hook;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\node\NodeInterface;
use Drupal\ps_homepage\Service\HomepageLayoutStructureSynchronizer;

/**
 * Entity hooks for homepage layout translation synchronization.
 */
final class HomepageLayoutEntityHooks {

  public function __construct(
    private readonly HomepageLayoutStructureSynchronizer $structureSynchronizer,
  ) {}

  /**
   * Implements hook_entity_presave().
   */
  #[Hook('entity_presave')]
  public function entityPresave(EntityInterface $entity): void {
    if (!$entity instanceof NodeInterface || $entity->bundle() !== 'page') {
      return;
    }

    if (!$entity->hasField('layout_builder__layout')) {
      return;
    }

    $this->structureSynchronizer->synchronize($entity);
  }

}
