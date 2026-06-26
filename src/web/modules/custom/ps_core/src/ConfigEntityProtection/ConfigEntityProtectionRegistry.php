<?php

declare(strict_types=1);

namespace Drupal\ps_core\ConfigEntityProtection;

use Drupal\Core\Entity\EntityInterface;

/**
 * Registry of config entity types participating in entity protection.
 */
final class ConfigEntityProtectionRegistry {

  /**
   * Protection definitions keyed by entity type ID.
   *
   * @var array<string, \Drupal\ps_core\ConfigEntityProtection\ConfigEntityProtectionDefinition>
   */
  private array $definitions = [];

  /**
   * Constructs a ConfigEntityProtectionRegistry.
   *
   * @param iterable $definitions
   *   Tagged protection definition services.
   */
  public function __construct(iterable $definitions) {
    foreach ($definitions as $definition) {
      if (!$definition instanceof ConfigEntityProtectionDefinition) {
        continue;
      }
      $this->definitions[$definition->getEntityTypeId()] = $definition;
    }
  }

  /**
   * Returns whether an entity type is registered for config protection.
   */
  public function supportsEntityType(string $entityTypeId): bool {
    return isset($this->definitions[$entityTypeId]);
  }

  /**
   * Returns whether an entity participates in config entity protection.
   */
  public function supports(EntityInterface $entity): bool {
    return $this->supportsEntityType($entity->getEntityTypeId());
  }

  /**
   * Returns the protection definition for an entity type, if any.
   */
  public function getDefinition(string $entityTypeId): ?ConfigEntityProtectionDefinition {
    return $this->definitions[$entityTypeId] ?? NULL;
  }

  /**
   * Returns the protection definition for an entity, if any.
   */
  public function getDefinitionForEntity(EntityInterface $entity): ?ConfigEntityProtectionDefinition {
    return $this->getDefinition($entity->getEntityTypeId());
  }

}
