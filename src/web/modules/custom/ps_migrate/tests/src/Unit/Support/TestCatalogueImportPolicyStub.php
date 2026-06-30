<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Support;

use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceCatalogueImportPolicyInterface;

/**
 * Configurable catalogue import policy stub.
 */
final class TestCatalogueImportPolicyStub implements ImportGovernanceCatalogueImportPolicyInterface {

  public function __construct(
    private readonly string $defaultGroupId = 'additional',
    private readonly bool $createStub = FALSE,
    private readonly bool $syncLabels = TRUE,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getDefaultImportGroupId(): string {
    return $this->defaultGroupId;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateStubDefinitionForMissingOfferValue(): bool {
    return $this->createStub;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSyncDefinitionLabelsFromOfferImport(): bool {
    return $this->syncLabels;
  }

}
