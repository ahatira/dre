<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_media\Unit\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_media\Plugin\ImportGovernance\MediaImportGovernancePolicy;
use Drupal\ps_media\Service\MediaImportGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_media\Plugin\ImportGovernance\MediaImportGovernancePolicy
 */
#[CoversClass(MediaImportGovernancePolicy::class)]
#[Group('ps_media')]
final class MediaImportGovernancePolicyTest extends UnitTestCase {

  /**
   * @covers ::getEntityTypeIds
   */
  public function testGetEntityTypeIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(['media'], $policy->getEntityTypeIds());
  }

  /**
   * @covers ::getAdditionalPreservedProperties
   */
  public function testGetAdditionalPreservedPropertiesWhenAltOverwriteDisabled(): void {
    $importGovernance = $this->createMock(MediaImportGovernance::class);
    $importGovernance->method('allowCrmOverwriteAlt')->willReturn(FALSE);

    $policy = $this->buildPolicy($importGovernance);

    self::assertSame(
      ['field_media_image/alt', 'field_media_link/title'],
      $policy->getAdditionalPreservedProperties($this->createMock(EntityInterface::class)),
    );
  }

  /**
   * @covers ::getSupportedMigrationIds
   */
  public function testGetSupportedMigrationIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(
      ['ps_media_from_xml', 'ps_media_virtual_tour_from_xml'],
      $policy->getSupportedMigrationIds(),
    );
  }

  private function buildPolicy(?MediaImportGovernance $importGovernance = NULL): MediaImportGovernancePolicy {
    return new MediaImportGovernancePolicy(
      [],
      'media',
      [
        'admin_label' => 'Media',
        'description' => 'Media import governance.',
        'settings_route' => 'ps_media.governance_domain_settings',
        'weight' => 40,
      ],
      $importGovernance ?? $this->createMock(MediaImportGovernance::class),
    );
  }

}
