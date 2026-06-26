<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_agent\Unit\Plugin\ImportGovernance;

use Drupal\Core\Entity\EntityInterface;
use Drupal\ps_agent\Plugin\ImportGovernance\AgentImportGovernancePolicy;
use Drupal\ps_agent\Service\AgentImportGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_agent\Plugin\ImportGovernance\AgentImportGovernancePolicy
 */
#[CoversClass(AgentImportGovernancePolicy::class)]
#[Group('ps_agent')]
final class AgentImportGovernancePolicyTest extends UnitTestCase {

  /**
   * @covers ::getEntityTypeIds
   */
  public function testGetEntityTypeIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(['ps_agent'], $policy->getEntityTypeIds());
  }

  /**
   * @covers ::getAdditionalPreservedProperties
   */
  public function testGetAdditionalPreservedPropertiesWhenContactOverwriteDisabled(): void {
    $importGovernance = $this->createMock(AgentImportGovernance::class);
    $importGovernance->method('allowCrmOverwriteContact')->willReturn(FALSE);

    $policy = $this->buildPolicy($importGovernance);

    self::assertSame(['email', 'phone'], $policy->getAdditionalPreservedProperties($this->createMock(EntityInterface::class)));
  }

  /**
   * @covers ::getSupportedMigrationIds
   */
  public function testGetSupportedMigrationIds(): void {
    $policy = $this->buildPolicy();

    self::assertSame(['ps_agent_from_xml'], $policy->getSupportedMigrationIds());
  }

  private function buildPolicy(?AgentImportGovernance $importGovernance = NULL): AgentImportGovernancePolicy {
    return new AgentImportGovernancePolicy(
      [],
      'agent',
      [
        'admin_label' => 'Agents',
        'description' => 'Agent import governance.',
        'settings_route' => 'ps_agent.governance_domain_settings',
        'weight' => 20,
      ],
      $importGovernance ?? $this->createMock(AgentImportGovernance::class),
    );
  }

}
