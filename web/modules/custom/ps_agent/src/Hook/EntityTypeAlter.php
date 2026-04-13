<?php

declare(strict_types=1);

namespace Drupal\ps_agent\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Hook implementations for entity type alterations.
 *
 * @see hook_entity_type_alter()
 */
final class EntityTypeAlter
{
  /**
   * Implements hook_entity_type_alter().
   *
   * Ensures the agent entity has the correct bundle class.
   */
    #[Hook('entity_type_alter')]
    public function entityTypeAlter(array &$entityTypes): void
    {
        if (isset($entityTypes['agent'])) {
            $entityTypes['agent']->set('bundle_entity_type', 'agent_type');
        }
    }
}
