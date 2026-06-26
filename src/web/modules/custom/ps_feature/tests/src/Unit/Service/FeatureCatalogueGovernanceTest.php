<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_core\Service\EntityProtectionManagerInterface;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureCatalogueGovernance;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureCatalogueGovernance
 */
#[CoversClass(FeatureCatalogueGovernance::class)]
#[Group('ps_feature')]
final class FeatureCatalogueGovernanceTest extends UnitTestCase {

  /**
   * @covers ::resolveEffectiveLockStrategy
   */
  public function testResolveEffectiveLockStrategyInheritsGlobalDefault(): void {
    $governance = $this->buildGovernance(
      governance: ['crm_row_strategy_override' => FeatureCatalogueGovernance::STRATEGY_INHERIT],
      migrate: ['lock_strategy_default' => FeatureCatalogueGovernance::STRATEGY_SKIP_FIELD],
    );

    self::assertSame(
      FeatureCatalogueGovernance::STRATEGY_SKIP_FIELD,
      $governance->resolveEffectiveLockStrategy('fb_feature_definition'),
    );
  }

  /**
   * @covers ::resolveEffectiveLockStrategy
   */
  public function testResolveEffectiveLockStrategyUsesFeatureOverride(): void {
    $governance = $this->buildGovernance(
      governance: ['crm_row_strategy_override' => FeatureCatalogueGovernance::STRATEGY_SKIP_ROW],
      migrate: ['lock_strategy_default' => FeatureCatalogueGovernance::STRATEGY_SKIP_FIELD],
    );

    self::assertSame(
      FeatureCatalogueGovernance::STRATEGY_SKIP_ROW,
      $governance->resolveEffectiveLockStrategy('fb_feature_group'),
    );
  }

  /**
   * @covers ::shouldDeactivateMissingDefinition
   */
  public function testProtectedDefinitionStaysActiveWhenMissingFromXml(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('get')->with('status')->willReturn(TRUE);

    $protectionManager = $this->createMock(EntityProtectionManagerInterface::class);
    $protectionManager->method('isCatalogueProtected')->with($definition)->willReturn(TRUE);

    $governance = $this->buildGovernance(
      governance: [
        'missing_from_xml' => [
          'protected_definition_action' => FeatureCatalogueGovernance::ACTION_KEEP_ACTIVE,
        ],
      ],
      protectionManager: $protectionManager,
    );

    self::assertFalse($governance->shouldDeactivateMissingDefinition($definition, FALSE));
  }

  /**
   * @covers ::shouldDeactivateMissingDefinition
   */
  public function testNonProtectedDefinitionDeactivatesWhenConfigured(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $definition->method('get')->with('status')->willReturn(TRUE);

    $protectionManager = $this->createMock(EntityProtectionManagerInterface::class);
    $protectionManager->method('isCatalogueProtected')->with($definition)->willReturn(FALSE);

    $governance = $this->buildGovernance(
      governance: [
        'missing_from_xml' => [
          'definition_action' => FeatureCatalogueGovernance::ACTION_DEACTIVATE,
        ],
      ],
      protectionManager: $protectionManager,
    );

    self::assertTrue($governance->shouldDeactivateMissingDefinition($definition, FALSE));
  }

  /**
   * @covers ::getPresentInXmlSyncFields
   */
  public function testPresentInXmlSyncFieldsAreNormalized(): void {
    $governance = $this->buildGovernance(
      governance: [
        'present_in_xml' => [
          'sync_fields' => [' label ', 'code', 'code', ''],
        ],
      ],
    );

    self::assertSame(['label', 'code'], $governance->getPresentInXmlSyncFields());
  }

  /**
   * @covers ::getOfferValuesMissingDefinitionAction
   */
  public function testOfferValuesMissingDefinitionDefaultsToSkipLog(): void {
    $governance = $this->buildGovernance();

    self::assertSame(
      FeatureCatalogueGovernance::MISSING_DEFINITION_SKIP_LOG,
      $governance->getOfferValuesMissingDefinitionAction(),
    );
    self::assertFalse($governance->shouldCreateStubDefinitionForMissingOfferValue());
  }

  /**
   * @covers ::shouldSyncDefinitionLabelsFromOfferImport
   */
  public function testOfferValuesSyncDefinitionLabelsReadsConfig(): void {
    $governance = $this->buildGovernance(
      governance: [
        'offer_values' => [
          'sync_definition_labels' => FALSE,
        ],
      ],
    );

    self::assertFalse($governance->shouldSyncDefinitionLabelsFromOfferImport());
  }

  /**
   * @covers ::getDefaultImportGroupId
   */
  public function testDefaultImportGroupFallsBackWhenUnset(): void {
    $governance = $this->buildGovernance();

    self::assertSame('informations_complementaires', $governance->getDefaultImportGroupId());
  }

  /**
   * @covers ::getDefaultImportGroupId
   */
  public function testDefaultImportGroupUsesConfiguredGroup(): void {
    $groupStorage = $this->createMock(EntityStorageInterface::class);
    $groupStorage->method('load')->with('equipements')->willReturn($this->createMock(\Drupal\ps_feature\Entity\FeatureGroup::class));

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_group')->willReturn($groupStorage);

    $governance = $this->buildGovernance(
      governance: [
        'import_defaults' => [
          'default_group' => 'equipements',
        ],
      ],
      entityTypeManager: $entityTypeManager,
    );

    self::assertSame('equipements', $governance->getDefaultImportGroupId());
  }

  /**
   * @covers ::getDefaultImportGroupId
   */
  public function testDefaultImportGroupFallsBackWhenConfiguredGroupMissing(): void {
    $groupStorage = $this->createMock(EntityStorageInterface::class);
    $groupStorage->method('load')->with('missing_group')->willReturn(NULL);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('fb_feature_group')->willReturn($groupStorage);

    $governance = $this->buildGovernance(
      governance: [
        'import_defaults' => [
          'default_group' => 'missing_group',
        ],
      ],
      entityTypeManager: $entityTypeManager,
    );

    self::assertSame('informations_complementaires', $governance->getDefaultImportGroupId());
  }

  /**
   * Builds a governance service with mocked config.
   *
   * @param array<string, mixed> $governance
   * @param array<string, mixed> $migrate
   */
  private function buildGovernance(
    array $governance = [],
    array $migrate = [],
    ?EntityProtectionManagerInterface $protectionManager = NULL,
    ?EntityTypeManagerInterface $entityTypeManager = NULL,
  ): FeatureCatalogueGovernance {
    $governanceConfig = $this->createMock(ImmutableConfig::class);
    $governanceConfig->method('get')->willReturnCallback(
      static function (string $key) use ($governance): mixed {
        $parts = explode('.', $key);
        $value = $governance;
        foreach ($parts as $part) {
          if (!is_array($value) || !array_key_exists($part, $value)) {
            return NULL;
          }
          $value = $value[$part];
        }
        return $value;
      },
    );

    $migrateConfig = $this->createMock(ImmutableConfig::class);
    $migrateConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => $migrate[$key] ?? NULL,
    );

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnMap([
      [FeatureCatalogueGovernance::CONFIG_NAME, $governanceConfig],
      ['ps_migrate.import_pipeline_settings', $migrateConfig],
    ]);

    return new FeatureCatalogueGovernance(
      $configFactory,
      $protectionManager ?? $this->createMock(EntityProtectionManagerInterface::class),
      $entityTypeManager ?? $this->createMock(EntityTypeManagerInterface::class),
      $this->createGlobalResolver($migrate),
    );
  }

  /**
   * Builds a global resolver backed by migrate config values.
   *
   * @param array<string, mixed> $migrate
   */
  private function createGlobalResolver(array $migrate = []): ImportGovernanceGlobalResolver {
    $globalGovernanceConfig = $this->createMock(ImmutableConfig::class);
    $globalGovernanceConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => match ($key) {
        'import.global_lock_strategy.source_config' => 'ps_migrate.import_pipeline_settings',
        'import.global_lock_strategy.source_key' => 'lock_strategy_default',
        default => NULL,
      },
    );

    $migrateConfig = $this->createMock(ImmutableConfig::class);
    $migrateConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => $migrate[$key] ?? NULL,
    );

    $configFactory = $this->createMock(ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnMap([
      [ImportGovernanceGlobalResolver::CONFIG_NAME, $globalGovernanceConfig],
      ['ps_migrate.import_pipeline_settings', $migrateConfig],
    ]);

    $moduleHandler = $this->createMock(\Drupal\Core\Extension\ModuleHandlerInterface::class);
    $moduleHandler->method('moduleExists')->willReturn(TRUE);

    return new ImportGovernanceGlobalResolver($configFactory, $moduleHandler);
  }

}
