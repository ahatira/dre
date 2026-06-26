<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;

/**
 * Optional post-import synchronization rules for a governance domain.
 *
 * Implement on a policy plugin when migrate POST_IMPORT needs domain settings.
 */
interface ImportGovernancePostImportPolicyInterface {

  /**
   * Migration IDs handled by this post-import policy.
   *
   * @return string[]
   *   Migrate migration plugin IDs.
   */
  public function getSupportedMigrationIds(): array;

  /**
   * Whether entities present in the XML snapshot should be reactivated.
   */
  public function shouldReactivatePresentInXml(): bool;

  /**
   * Whether a group should be deactivated when absent from XML.
   */
  public function shouldDeactivateMissingGroup(
    EntityInterface $group,
    bool $shouldBeActive,
  ): bool;

  /**
   * Whether a definition should be deactivated when absent from XML.
   */
  public function shouldDeactivateMissingDefinition(
    EntityInterface $definition,
    bool $shouldBeActive,
  ): bool;

  /**
   * Definition fields synchronized from XML for non-protected rows.
   *
   * @return string[]
   *   Normalized field names.
   */
  public function getPresentInXmlSyncFields(): array;

}
