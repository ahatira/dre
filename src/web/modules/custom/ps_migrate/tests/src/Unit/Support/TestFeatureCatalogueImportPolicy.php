<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Support;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceCatalogueImportPolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;

/**
 * Adapter exposing catalogue import rules through the governance policy API.
 */
final class TestFeatureCatalogueImportPolicy implements ImportGovernancePolicyInterface, ImportGovernanceCatalogueImportPolicyInterface {

  /**
   * Feature entity types covered by the test policy.
   *
   * @var string[]
   */
  private const FEATURE_ENTITY_TYPES = [
    'fb_feature_definition',
    'fb_feature_group',
  ];

  /**
   * Constructs a test feature catalogue import policy adapter.
   */
  public function __construct(
    private readonly ImportGovernanceCatalogueImportPolicyInterface $cataloguePolicy,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getPluginId(): string {
    return 'features';
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginDefinition(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminLabel(): string {
    return 'Features';
  }

  /**
   * {@inheritdoc}
   */
  public function getAdminDescription(): string {
    return 'Feature catalogue governance.';
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsRouteName(): ?string {
    return 'ps_feature.governance_domain_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight(): int {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityTypeIds(): array {
    return self::FEATURE_ENTITY_TYPES;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundleIds(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSkipProtectedRow(EntityInterface $entity): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldPreserveProtectedFields(EntityInterface $entity): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function resolveEffectiveLockStrategy(string $entityTypeId): string {
    return 'log_only';
  }

  /**
   * {@inheritdoc}
   */
  public function getAdditionalPreservedProperties(EntityInterface $entity): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultImportGroupId(): string {
    return $this->cataloguePolicy->getDefaultImportGroupId();
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateStubDefinitionForMissingOfferValue(): bool {
    return $this->cataloguePolicy->shouldCreateStubDefinitionForMissingOfferValue();
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSyncDefinitionLabelsFromOfferImport(): bool {
    return $this->cataloguePolicy->shouldSyncDefinitionLabelsFromOfferImport();
  }

}
