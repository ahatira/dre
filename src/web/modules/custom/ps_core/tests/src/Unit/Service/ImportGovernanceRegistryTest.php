<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit\Service;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceCatalogueImportPolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernancePostImportPolicyInterface;
use Drupal\ps_core\Plugin\ImportGovernance\ImportGovernanceSnapshotPostImportPolicyInterface;
use Drupal\ps_core\Service\ImportGovernancePolicyManager;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_core\Service\ImportGovernanceRegistry
 */
#[CoversClass(ImportGovernanceRegistry::class)]
#[Group('ps_core')]
final class ImportGovernanceRegistryTest extends UnitTestCase {

  /**
   * @covers ::getPolicyForEntityType
   */
  public function testGetPolicyForEntityTypeReturnsMatchingPolicy(): void {
    $featuresPolicy = $this->createMock(ImportGovernancePolicyInterface::class);
    $featuresPolicy->method('getEntityTypeIds')->willReturn([
      'fb_feature_definition',
      'fb_feature_group',
    ]);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->with('features')->willReturn($featuresPolicy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertSame($featuresPolicy, $registry->getPolicyForEntityType('fb_feature_definition'));
    self::assertNull($registry->getPolicyForEntityType('node'));
  }

  /**
   * @covers ::getPolicyForEntity
   */
  public function testGetPolicyForEntityMatchesBundleSpecificPolicy(): void {
    $entity = $this->createMock(EntityInterface::class);
    $entity->method('getEntityTypeId')->willReturn('node');
    $entity->method('bundle')->willReturn('offer');

    $offerPolicy = $this->createMock(ImportGovernancePolicyInterface::class);
    $offerPolicy->method('getEntityTypeIds')->willReturn(['node']);
    $offerPolicy->method('getBundleIds')->willReturn(['offer']);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'offer' => ['weight' => 10],
    ]);
    $policyManager->method('createInstance')->with('offer')->willReturn($offerPolicy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertSame($offerPolicy, $registry->getPolicyForEntity($entity));
  }

  /**
   * @covers ::getPolicyForEntityType
   */
  public function testGetPolicyForEntityTypeIgnoresBundleSpecificPolicies(): void {
    $offerPolicy = $this->createMock(ImportGovernancePolicyInterface::class);
    $offerPolicy->method('getEntityTypeIds')->willReturn(['node']);
    $offerPolicy->method('getBundleIds')->willReturn(['offer']);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'offer' => ['weight' => 10],
    ]);
    $policyManager->method('createInstance')->willReturn($offerPolicy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertNull($registry->getPolicyForEntityType('node'));
  }

  /**
   * @covers ::getPolicyForEntity
   */
  public function testGetPolicyForEntityDelegatesToEntityType(): void {
    $entity = $this->createMock(EntityInterface::class);
    $entity->method('getEntityTypeId')->willReturn('fb_feature_group');

    $policy = $this->createMock(ImportGovernancePolicyInterface::class);
    $policy->method('getEntityTypeIds')->willReturn(['fb_feature_group']);
    $policy->method('getBundleIds')->willReturn([]);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->willReturn($policy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertSame($policy, $registry->getPolicyForEntity($entity));
  }

  /**
   * @covers ::getPostImportPolicyForMigration
   */
  public function testGetPostImportPolicyForMigrationReturnsMatchingPolicy(): void {
    $postImportPolicy = $this->createMock(ImportGovernancePostImportPolicyInterface::class);
    $postImportPolicy->method('getSupportedMigrationIds')->willReturn([
      'ps_feature_groups_from_xml',
      'ps_feature_definitions_from_xml',
    ]);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->with('features')->willReturn($postImportPolicy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertSame(
      $postImportPolicy,
      $registry->getPostImportPolicyForMigration('ps_feature_definitions_from_xml'),
    );
    self::assertNull($registry->getPostImportPolicyForMigration('ps_offer_from_xml'));
  }

  /**
   * @covers ::getPostImportPolicyForMigration
   */
  public function testGetPostImportPolicyForMigrationIgnoresPoliciesWithoutPostImport(): void {
    $policy = $this->createMock(ImportGovernancePolicyInterface::class);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->willReturn($policy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertNull($registry->getPostImportPolicyForMigration('ps_feature_groups_from_xml'));
  }

  /**
   * @covers ::getCatalogueImportPolicyForEntityType
   */
  public function testGetCatalogueImportPolicyForEntityTypeReturnsMatchingPolicy(): void {
    $cataloguePolicy = new TestCatalogueImportPolicy();

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->with('features')->willReturn($cataloguePolicy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertSame(
      $cataloguePolicy,
      $registry->getCatalogueImportPolicyForEntityType('fb_feature_definition'),
    );
  }

  /**
   * @covers ::getCatalogueImportPolicyForEntityType
   */
  public function testGetCatalogueImportPolicyForEntityTypeIgnoresNonCataloguePolicies(): void {
    $policy = $this->createMock(ImportGovernancePolicyInterface::class);
    $policy->method('getEntityTypeIds')->willReturn(['fb_feature_definition']);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->willReturn($policy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertNull($registry->getCatalogueImportPolicyForEntityType('fb_feature_definition'));
  }

  /**
   * @covers ::getSnapshotPostImportPolicyForMigration
   */
  public function testGetSnapshotPostImportPolicyForMigrationReturnsMatchingPolicy(): void {
    $snapshotPolicy = $this->createMock(ImportGovernanceSnapshotPostImportPolicyInterface::class);
    $snapshotPolicy->method('getSupportedMigrationIds')->willReturn([
      'ps_offer_from_xml',
      'ps_agent_from_xml',
    ]);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'offer' => ['weight' => 10],
    ]);
    $policyManager->method('createInstance')->with('offer')->willReturn($snapshotPolicy);

    $registry = new ImportGovernanceRegistry($policyManager);

    self::assertSame(
      $snapshotPolicy,
      $registry->getSnapshotPostImportPolicyForMigration('ps_offer_from_xml'),
    );
    self::assertNull($registry->getSnapshotPostImportPolicyForMigration('ps_feature_groups_from_xml'));
  }

}

/**
 * Test double implementing catalogue import rules and entity coverage.
 */
final class TestCatalogueImportPolicy implements ImportGovernancePolicyInterface, ImportGovernanceCatalogueImportPolicyInterface {

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
    return 'Features governance.';
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingsRouteName(): ?string {
    return NULL;
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
    return ['fb_feature_definition', 'fb_feature_group'];
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
    return 'informations_complementaires';
  }

  /**
   * {@inheritdoc}
   */
  public function shouldCreateStubDefinitionForMissingOfferValue(): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function shouldSyncDefinitionLabelsFromOfferImport(): bool {
    return TRUE;
  }

}
