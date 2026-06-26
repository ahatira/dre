<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_feature\Plugin\ImportGovernance\FeatureImportGovernancePolicy;
use Drupal\ps_feature\Service\FeatureCatalogueGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_feature\Plugin\ImportGovernance\FeatureImportGovernancePolicy
 */
#[CoversClass(FeatureImportGovernancePolicy::class)]
#[Group('ps_feature')]
final class FeatureImportGovernancePolicyTest extends UnitTestCase {

  /**
   * @covers ::getSupportedMigrationIds
   */
  public function testGetSupportedMigrationIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(
      ['ps_feature_groups_from_xml', 'ps_feature_definitions_from_xml'],
      $policy->getSupportedMigrationIds(),
    );
  }

  /**
   * @covers ::shouldReactivatePresentInXml
   */
  public function testShouldReactivatePresentInXmlDelegatesToCatalogueGovernance(): void {
    $catalogueGovernance = $this->createMock(FeatureCatalogueGovernance::class);
    $catalogueGovernance->expects($this->once())
      ->method('shouldReactivatePresentInXml')
      ->willReturn(TRUE);

    $policy = $this->buildPolicy($catalogueGovernance);

    self::assertTrue($policy->shouldReactivatePresentInXml());
  }

  /**
   * @covers ::shouldDeactivateMissingGroup
   */
  public function testShouldDeactivateMissingGroupDelegatesToCatalogueGovernance(): void {
    $group = $this->createMock(EntityInterface::class);
    $catalogueGovernance = $this->createMock(FeatureCatalogueGovernance::class);
    $catalogueGovernance->expects($this->once())
      ->method('shouldDeactivateMissingGroup')
      ->with($group, FALSE)
      ->willReturn(TRUE);

    $policy = $this->buildPolicy($catalogueGovernance);

    self::assertTrue($policy->shouldDeactivateMissingGroup($group, FALSE));
  }

  /**
   * @covers ::getPresentInXmlSyncFields
   */
  public function testGetPresentInXmlSyncFieldsDelegatesToCatalogueGovernance(): void {
    $catalogueGovernance = $this->createMock(FeatureCatalogueGovernance::class);
    $catalogueGovernance->expects($this->once())
      ->method('getPresentInXmlSyncFields')
      ->willReturn(['label', 'payload_defaults']);

    $policy = $this->buildPolicy($catalogueGovernance);

    self::assertSame(['label', 'payload_defaults'], $policy->getPresentInXmlSyncFields());
  }

  /**
   * @covers ::getDefaultImportGroupId
   */
  public function testGetDefaultImportGroupIdDelegatesToCatalogueGovernance(): void {
    $catalogueGovernance = $this->createMock(FeatureCatalogueGovernance::class);
    $catalogueGovernance->expects($this->once())
      ->method('getDefaultImportGroupId')
      ->willReturn('equipements');

    $policy = $this->buildPolicy($catalogueGovernance);

    self::assertSame('equipements', $policy->getDefaultImportGroupId());
  }

  /**
   * @covers ::shouldCreateStubDefinitionForMissingOfferValue
   */
  public function testShouldCreateStubDefinitionDelegatesToCatalogueGovernance(): void {
    $catalogueGovernance = $this->createMock(FeatureCatalogueGovernance::class);
    $catalogueGovernance->expects($this->once())
      ->method('shouldCreateStubDefinitionForMissingOfferValue')
      ->willReturn(TRUE);

    $policy = $this->buildPolicy($catalogueGovernance);

    self::assertTrue($policy->shouldCreateStubDefinitionForMissingOfferValue());
  }

  /**
   * @covers ::shouldSyncDefinitionLabelsFromOfferImport
   */
  public function testShouldSyncDefinitionLabelsDelegatesToCatalogueGovernance(): void {
    $catalogueGovernance = $this->createMock(FeatureCatalogueGovernance::class);
    $catalogueGovernance->expects($this->once())
      ->method('shouldSyncDefinitionLabelsFromOfferImport')
      ->willReturn(FALSE);

    $policy = $this->buildPolicy($catalogueGovernance);

    self::assertFalse($policy->shouldSyncDefinitionLabelsFromOfferImport());
  }

  /**
   * Builds a feature governance policy with a mocked catalogue service.
   */
  private function buildPolicy(?FeatureCatalogueGovernance $catalogueGovernance = NULL): FeatureImportGovernancePolicy {
    return new FeatureImportGovernancePolicy(
      [],
      'features',
      [
        'admin_label' => 'Features',
        'description' => 'Feature catalogue governance.',
        'settings_route' => 'ps_feature.governance_domain_settings',
        'weight' => 0,
      ],
      $catalogueGovernance ?? $this->createMock(FeatureCatalogueGovernance::class),
    );
  }

}
