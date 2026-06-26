<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_offer\Plugin\ImportGovernance\OfferImportGovernancePolicy;
use Drupal\ps_offer\Service\OfferImportGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_offer\Plugin\ImportGovernance\OfferImportGovernancePolicy
 */
#[CoversClass(OfferImportGovernancePolicy::class)]
#[Group('ps_offer')]
final class OfferImportGovernancePolicyTest extends UnitTestCase {

  /**
   * @covers ::getEntityTypeIds
   */
  public function testGetEntityTypeIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(['node'], $policy->getEntityTypeIds());
  }

  /**
   * @covers ::getBundleIds
   */
  public function testGetBundleIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(['offer'], $policy->getBundleIds());
  }

  /**
   * @covers ::getAdditionalPreservedProperties
   */
  public function testGetAdditionalPreservedPropertiesWhenReferenceOverwriteDisabled(): void {
    $importGovernance = $this->createMock(OfferImportGovernance::class);
    $importGovernance->method('allowCrmOverwriteReference')->willReturn(FALSE);

    $policy = $this->buildPolicy($importGovernance);

    self::assertSame(
      ['field_reference', 'field_reference_auto'],
      $policy->getAdditionalPreservedProperties($this->createMock(EntityInterface::class)),
    );
  }

  /**
   * @covers ::getAdditionalPreservedProperties
   */
  public function testGetAdditionalPreservedPropertiesWhenReferenceOverwriteEnabled(): void {
    $importGovernance = $this->createMock(OfferImportGovernance::class);
    $importGovernance->method('allowCrmOverwriteReference')->willReturn(TRUE);

    $policy = $this->buildPolicy($importGovernance);

    self::assertSame([], $policy->getAdditionalPreservedProperties($this->createMock(EntityInterface::class)));
  }

  /**
   * @covers ::shouldSkipProtectedRow
   */
  public function testShouldSkipProtectedRowDelegatesToImportGovernance(): void {
    $entity = $this->createMock(EntityInterface::class);
    $importGovernance = $this->createMock(OfferImportGovernance::class);
    $importGovernance->expects($this->once())
      ->method('shouldSkipProtectedRow')
      ->with($entity)
      ->willReturn(TRUE);

    $policy = $this->buildPolicy($importGovernance);

    self::assertTrue($policy->shouldSkipProtectedRow($entity));
  }

  /**
   * @covers ::getSupportedMigrationIds
   */
  public function testGetSupportedMigrationIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(['ps_offer_from_xml'], $policy->getSupportedMigrationIds());
  }

  /**
   * Builds an offer governance policy with a mocked import governance service.
   */
  private function buildPolicy(?OfferImportGovernance $importGovernance = NULL): OfferImportGovernancePolicy {
    return new OfferImportGovernancePolicy(
      [],
      'offer',
      [
        'admin_label' => 'Offers',
        'description' => 'Offer import governance.',
        'settings_route' => 'ps_offer.governance_domain_settings',
        'weight' => 10,
      ],
      $importGovernance ?? $this->createMock(OfferImportGovernance::class),
    );
  }

}
