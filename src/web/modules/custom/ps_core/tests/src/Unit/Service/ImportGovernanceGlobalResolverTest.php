<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_core\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\ps_core\ImportGovernance\ImportGovernanceLockStrategy;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_core\Service\ImportGovernanceGlobalResolver
 */
#[CoversClass(ImportGovernanceGlobalResolver::class)]
#[Group('ps_core')]
final class ImportGovernanceGlobalResolverTest extends UnitTestCase {

  /**
   * @covers ::getGlobalLockStrategy
   */
  public function testGetGlobalLockStrategyReadsConfiguredSource(): void {
    $resolver = $this->buildResolver(
      governance: ['import.show_domain_inheritance_hints' => TRUE],
      migrate: ['lock_strategy_default' => ImportGovernanceLockStrategy::SKIP_FIELD],
    );

    self::assertSame(
      ImportGovernanceLockStrategy::SKIP_FIELD,
      $resolver->getGlobalLockStrategy(),
    );
  }

  /**
   * @covers ::getGlobalLockStrategy
   */
  public function testGetGlobalLockStrategyFallsBackWhenInvalid(): void {
    $resolver = $this->buildResolver(
      migrate: ['lock_strategy_default' => 'invalid'],
    );

    self::assertSame(
      ImportGovernanceLockStrategy::LOG_ONLY,
      $resolver->getGlobalLockStrategy(),
    );
  }

  /**
   * @covers ::buildInheritanceHintMarkup
   */
  public function testBuildInheritanceHintMarkupReturnsEmptyWhenDisabled(): void {
    $resolver = $this->buildResolver(
      governance: ['import.show_domain_inheritance_hints' => FALSE],
    );

    self::assertSame('', $resolver->buildInheritanceHintMarkup());
  }

  /**
   * @covers ::buildInheritanceHintMarkup
   */
  public function testBuildInheritanceHintMarkupIncludesStrategyLabel(): void {
    $resolver = $this->buildResolver(
      governance: ['import.show_domain_inheritance_hints' => TRUE],
      migrate: ['lock_strategy_default' => ImportGovernanceLockStrategy::SKIP_ROW],
      migrateModuleEnabled: FALSE,
    );

    self::assertStringContainsString(
      'Inherits from global CRM strategy:',
      $resolver->buildInheritanceHintMarkup(),
    );
    self::assertStringContainsString(
      'Skip row — do not update protected entities',
      $resolver->buildInheritanceHintMarkup(),
    );
  }

  /**
   * @covers ::getGlobalLockStrategySettingsRouteName
   */
  public function testGetGlobalLockStrategySettingsRouteNameRequiresMigrateModule(): void {
    $enabled = $this->buildResolver(migrateModuleEnabled: TRUE);
    $disabled = $this->buildResolver(migrateModuleEnabled: FALSE);

    self::assertSame(
      'ps_migrate.import_pipeline_settings',
      $enabled->getGlobalLockStrategySettingsRouteName(),
    );
    self::assertNull($disabled->getGlobalLockStrategySettingsRouteName());
  }

  /**
   * @covers ::buildGlobalPipelineSettingsLinkMarkup
   */
  public function testBuildGlobalPipelineSettingsLinkMarkupWithoutMigrateModule(): void {
    $resolver = $this->buildResolver(migrateModuleEnabled: FALSE);

    self::assertSame(
      'CRM import pipeline settings',
      $resolver->buildGlobalPipelineSettingsLinkMarkup('CRM import pipeline settings'),
    );
  }

  /**
   * Builds a resolver with mocked config and module handler.
   *
   * @param array<string, mixed> $governance
   * @param array<string, mixed> $migrate
   */
  private function buildResolver(
    array $governance = [],
    array $migrate = [],
    bool $migrateModuleEnabled = TRUE,
  ): ImportGovernanceGlobalResolver {
    $governance += [
      'import.global_lock_strategy.source_config' => 'ps_migrate.import_pipeline_settings',
      'import.global_lock_strategy.source_key' => 'lock_strategy_default',
      'import.show_domain_inheritance_hints' => TRUE,
    ];

    $governanceConfig = $this->createMock(ImmutableConfig::class);
    $governanceConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => $governance[$key] ?? NULL,
    );

    $migrateConfig = $this->createMock(ImmutableConfig::class);
    $migrateConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => $migrate[$key] ?? NULL,
    );

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnMap([
      [ImportGovernanceGlobalResolver::CONFIG_NAME, $governanceConfig],
      ['ps_migrate.import_pipeline_settings', $migrateConfig],
    ]);

    $moduleHandler = $this->createMock(ModuleHandlerInterface::class);
    $moduleHandler->method('moduleExists')->with('ps_migrate')->willReturn($migrateModuleEnabled);

    return new ImportGovernanceGlobalResolver($configFactory, $moduleHandler);
  }

}
