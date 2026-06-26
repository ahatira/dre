<?php

declare(strict_types=1);

namespace Drupal\ps_core\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceCatalogueImportPolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePostImportPolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;

/**
 * Resolves import governance policies for entities and admin navigation.
 */
class ImportGovernanceRegistry {

  public function __construct(
    private readonly ImportGovernancePolicyManager $policyManager,
  ) {}

  /**
   * Returns the policy covering an entity, if any.
   */
  public function getPolicyForEntity(EntityInterface $entity): ?ImportGovernancePolicyInterface {
    foreach ($this->getPolicies() as $policy) {
      if ($this->policyAppliesToEntity($policy, $entity)) {
        return $policy;
      }
    }

    return NULL;
  }

  /**
   * Returns the policy covering an entity type, if any.
   *
   * Bundle-specific policies are resolved via getPolicyForEntity() only.
   */
  public function getPolicyForEntityType(string $entityTypeId): ?ImportGovernancePolicyInterface {
    foreach ($this->getPolicies() as $policy) {
      if ($policy->getBundleIds() !== []) {
        continue;
      }
      if (in_array($entityTypeId, $policy->getEntityTypeIds(), TRUE)) {
        return $policy;
      }
    }

    return NULL;
  }

  /**
   * Checks whether a policy applies to a concrete entity instance.
   */
  private function policyAppliesToEntity(
    ImportGovernancePolicyInterface $policy,
    EntityInterface $entity,
  ): bool {
    $entityTypeId = $entity->getEntityTypeId();
    if (!in_array($entityTypeId, $policy->getEntityTypeIds(), TRUE)) {
      return FALSE;
    }

    $bundles = $policy->getBundleIds();
    if ($bundles === []) {
      return TRUE;
    }

    if (!method_exists($entity, 'bundle')) {
      return FALSE;
    }

    return in_array($entity->bundle(), $bundles, TRUE);
  }

  /**
   * Returns all registered policies sorted by weight.
   *
   * @return \Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface[]
   *   Registered import governance policies keyed by plugin ID.
   */
  public function getPolicies(): array {
    $definitions = $this->policyManager->getDefinitions();
    uasort(
      $definitions,
      static fn(array $a, array $b): int => ($a['weight'] ?? 0) <=> ($b['weight'] ?? 0),
    );

    $policies = [];
    foreach (array_keys($definitions) as $pluginId) {
      $policies[$pluginId] = $this->policyManager->createInstance($pluginId);
    }

    return $policies;
  }

  /**
   * Returns catalogue import rules for an entity type, if any.
   */
  public function getCatalogueImportPolicyForEntityType(
    string $entityTypeId,
  ): ?ImportGovernanceCatalogueImportPolicyInterface {
    $policy = $this->getPolicyForEntityType($entityTypeId);
    return $policy instanceof ImportGovernanceCatalogueImportPolicyInterface
      ? $policy
      : NULL;
  }

  /**
   * Returns the post-import policy for a migration, if any.
   */
  public function getPostImportPolicyForMigration(string $migrationId): ?ImportGovernancePostImportPolicyInterface {
    foreach ($this->getPolicies() as $policy) {
      if (!$policy instanceof ImportGovernancePostImportPolicyInterface) {
        continue;
      }
      if (in_array($migrationId, $policy->getSupportedMigrationIds(), TRUE)) {
        return $policy;
      }
    }

    return NULL;
  }

  /**
   * Returns the snapshot post-import policy for a migration, if any.
   */
  public function getSnapshotPostImportPolicyForMigration(
    string $migrationId,
  ): ?ImportGovernanceSnapshotPostImportPolicyInterface {
    foreach ($this->getPolicies() as $policy) {
      if (!$policy instanceof ImportGovernanceSnapshotPostImportPolicyInterface) {
        continue;
      }
      if (in_array($migrationId, $policy->getSupportedMigrationIds(), TRUE)) {
        return $policy;
      }
    }

    return NULL;
  }

}
