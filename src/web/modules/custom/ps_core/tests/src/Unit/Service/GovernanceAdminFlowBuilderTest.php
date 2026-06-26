<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\ps_core\Service\AdminFlowchartBuilder;
use Drupal\ps_core\Service\GovernanceAdminFlowBuilder;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_core\Service\GovernanceAdminFlowBuilder
 */
#[CoversClass(GovernanceAdminFlowBuilder::class)]
#[Group('ps_core')]
final class GovernanceAdminFlowBuilderTest extends UnitTestCase {

  /**
   * @covers ::buildFlowRenderArray
   */
  public function testBuildFlowRenderArrayIncludesGlobalStrategyAndDomains(): void {
    $policy = new class {

      public function getSettingsRouteName(): string {
        return 'ps_offer.governance_domain_settings';
      }

      public function getAdminLabel(): string {
        return 'Offers';
      }

      public function getAdminDescription(): string {
        return 'Offer CRM import lock strategy and reference protection.';
      }

    };

    $registry = $this->createMock(ImportGovernanceRegistry::class);
    $registry->method('getPolicies')->willReturn(['offer' => $policy]);

    $globalResolver = $this->createMock(ImportGovernanceGlobalResolver::class);
    $globalResolver->method('getGlobalLockStrategyLabel')->willReturn('Skip row');
    $globalResolver->method('shouldShowDomainInheritanceHints')->willReturn(TRUE);

    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->method('moduleExists')->with('ps_migrate')->willReturn(TRUE);

    $flowchartBuilder = new AdminFlowchartBuilder();
    $flowchartBuilder->setStringTranslation($this->getStringTranslationStub());

    $builder = new GovernanceAdminFlowBuilder(
      $flowchartBuilder,
      $registry,
      $globalResolver,
      $moduleHandler,
    );
    $builder->setStringTranslation($this->getStringTranslationStub());

    $build = $builder->buildFlowRenderArray();

    self::assertSame('details', $build['#type']);
    self::assertArrayHasKey('diagram', $build);
    self::assertSame(
      'pipeline',
      $build['diagram']['main_0']['#attributes']['data-flow-step'],
    );
    self::assertSame(
      'Skip row',
      $build['diagram']['main_0']['meta']['#value'],
    );

    $domainGrid = NULL;
    foreach ($build['diagram'] as $element) {
      if (!is_array($element)) {
        continue;
      }
      $classes = $element['#attributes']['class'] ?? [];
      if (in_array('ps-admin-flowchart__domain-hub', $classes, TRUE)) {
        $domainGrid = $element;
        break;
      }
    }
    self::assertIsArray($domainGrid);
    self::assertArrayHasKey('domain_offer', $domainGrid['grid']);
    self::assertSame(
      'Skip row',
      $build['diagram']['entity_decision']['branches']['protected']['lane']['lane_0']['meta']['#value'],
    );
  }

}
