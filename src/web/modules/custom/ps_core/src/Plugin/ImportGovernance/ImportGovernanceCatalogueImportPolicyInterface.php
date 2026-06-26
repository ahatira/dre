<?php

declare(strict_types=1);

namespace Drupal\ps_core\Plugin\ImportGovernance;

/**
 * Optional catalogue import rules exposed to migrate and import services.
 *
 * Implement on a policy plugin when imports need domain defaults beyond row
 * protection (default group, offer stubs, label sync, etc.).
 */
interface ImportGovernanceCatalogueImportPolicyInterface {

  /**
   * Default feature group ID when an import row has no group/category code.
   */
  public function getDefaultImportGroupId(): string;

  /**
   * Whether offer imports may create stub catalogue definitions.
   */
  public function shouldCreateStubDefinitionForMissingOfferValue(): bool;

  /**
   * Whether offer imports may update translated feature definition labels.
   */
  public function shouldSyncDefinitionLabelsFromOfferImport(): bool;

}
