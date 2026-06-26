<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_migrate\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigFactoryOverride;
use Drupal\language\Config\LanguageConfigOverride;
use Drupal\ps_core\Service\ImportGovernancePolicyManager;
use Drupal\ps_core\Service\ImportGovernanceRegistry;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Service\FeatureCanonicalGroupRegistry;
use Drupal\ps_feature\Service\FeatureDefinitionSource;
use Drupal\ps_migrate\Service\FeatureImportResolver;
use Drupal\ps_migrate\Service\FeatureMigrationKeyBuilder;
use Drupal\ps_migrate\Service\FeatureOfferValueImportHandler;
use Drupal\ps_migrate\Service\FeaturePayloadDefaultsNormalizer;
use Drupal\Tests\ps_migrate\Unit\Support\TestCatalogueImportPolicyStub;
use Drupal\Tests\ps_migrate\Unit\Support\TestFeatureCatalogueImportPolicy;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \Drupal\ps_migrate\Service\FeatureOfferValueImportHandler
 */
#[CoversClass(FeatureOfferValueImportHandler::class)]
#[Group('ps_migrate')]
final class FeatureOfferValueImportHandlerTest extends UnitTestCase {

  public function testResolveDefinitionReturnsExistingDefinition(): void {
    $definition = $this->createMock(FeatureDefinition::class);
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects(self::once())
      ->method('load')
      ->with('tec_existing')
      ->willReturn($definition);

    $handler = $this->buildHandler(
      definitionStorage: $storage,
      cataloguePolicy: new TestCatalogueImportPolicyStub(syncLabels: TRUE),
    );

    $result = $handler->resolveDefinitionForOfferItem([
      'feature_code' => 'TEC_EXISTING',
      'group_code' => 'EQUIPEMENTS',
    ]);

    self::assertSame($definition, $result);
  }

  public function testResolveDefinitionSkipsMissingDefinitionWhenConfigured(): void {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->expects(self::once())->method('load')->willReturn(NULL);
    $storage->expects(self::never())->method('create');

    $logger = $this->createMock(LoggerInterface::class);
    $logger->expects(self::once())->method('warning');

    $handler = $this->buildHandler(
      definitionStorage: $storage,
      cataloguePolicy: new TestCatalogueImportPolicyStub(createStub: FALSE, syncLabels: TRUE),
      logger: $logger,
    );

    self::assertNull($handler->resolveDefinitionForOfferItem([
      'feature_code' => 'TEC_UNKNOWN',
      'group_code' => 'EQUIPEMENTS',
    ]));
  }

  public function testResolveDefinitionCreatesStubWhenConfigured(): void {
    $stubDefinition = $this->createMock(FeatureDefinition::class);
    $stubDefinition->method('save')->willReturnSelf();

    $definitionStorage = $this->createMock(EntityStorageInterface::class);
    $definitionStorage->expects(self::atLeastOnce())->method('load')->willReturn(NULL);
    $definitionStorage->expects(self::once())->method('create')->with(self::callback(
      static function (array $values): bool {
        return $values['id'] === 'tec_stub'
          && $values['code'] === 'TEC_STUB'
          && $values['source'] === FeatureDefinitionSource::XML
          && $values['internal_lock'] === FALSE;
      },
    ))->willReturn($stubDefinition);

    $groupStorage = $this->createMock(EntityStorageInterface::class);
    $groupStorage->method('load')->willReturn(new \stdClass());

    $handler = $this->buildHandler(
      definitionStorage: $definitionStorage,
      groupStorage: $groupStorage,
      cataloguePolicy: new TestCatalogueImportPolicyStub(createStub: TRUE, syncLabels: TRUE),
    );

    self::assertInstanceOf(FeatureDefinition::class, $handler->resolveDefinitionForOfferItem([
      'feature_code' => 'TEC_STUB',
      'group_code' => 'EQUIPEMENTS',
      'label' => 'Stub label',
      'payload' => ['unit' => 'M2'],
      'type_driver' => 'numeric',
    ]));
  }

  public function testSyncDefinitionLabelSkipsWhenDisabled(): void {
    $languageConfigOverride = $this->createMock(LanguageConfigFactoryOverride::class);
    $languageConfigOverride->expects(self::never())->method('getOverride');

    $handler = $this->buildHandler(
      cataloguePolicy: new TestCatalogueImportPolicyStub(syncLabels: FALSE),
      languageConfigOverride: $languageConfigOverride,
    );

    $handler->syncDefinitionLabel('tec_surface', 'EN', 'Total surface');
  }

  public function testSyncDefinitionLabelWritesOverrideWhenEnabled(): void {
    $override = $this->createMock(LanguageConfigOverride::class);
    $override->method('get')->with('label')->willReturn('');
    $override->expects(self::once())->method('set')->with('label', 'Total surface')->willReturnSelf();
    $override->expects(self::once())->method('save');

    $languageConfigOverride = $this->createMock(LanguageConfigFactoryOverride::class);
    $languageConfigOverride->expects(self::once())
      ->method('getOverride')
      ->with('en', 'ps_feature.feature_definition.tec_surface')
      ->willReturn($override);

    $language = $this->createMock(LanguageInterface::class);
    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getLanguage')->with('en')->willReturn($language);

    $handler = $this->buildHandler(
      cataloguePolicy: new TestCatalogueImportPolicyStub(syncLabels: TRUE),
      languageConfigOverride: $languageConfigOverride,
      languageManager: $languageManager,
    );

    $handler->syncDefinitionLabel('tec_surface', 'EN', 'Total surface');
  }

  private function buildHandler(
    ?EntityStorageInterface $definitionStorage = NULL,
    ?EntityStorageInterface $groupStorage = NULL,
    ?TestCatalogueImportPolicyStub $cataloguePolicy = NULL,
    ?LanguageConfigFactoryOverride $languageConfigOverride = NULL,
    ?LanguageManagerInterface $languageManager = NULL,
    ?LoggerInterface $logger = NULL,
  ): FeatureOfferValueImportHandler {
    $definitionStorage ??= $this->createMock(EntityStorageInterface::class);
    $groupStorage ??= $this->createMock(EntityStorageInterface::class);
    $cataloguePolicy ??= new TestCatalogueImportPolicyStub();

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->willReturnMap([
      ['fb_feature_definition', $definitionStorage],
      ['fb_feature_group', $groupStorage],
    ]);

    $registry = $this->createRegistry($cataloguePolicy);

    return new FeatureOfferValueImportHandler(
      new FeatureImportResolver(
        new FeatureCanonicalGroupRegistry(),
        new FeatureMigrationKeyBuilder(),
        $entityTypeManager,
        $registry,
      ),
      $entityTypeManager,
      $registry,
      new FeaturePayloadDefaultsNormalizer(),
      $languageConfigOverride ?? $this->createMock(LanguageConfigFactoryOverride::class),
      $languageManager ?? $this->createMock(LanguageManagerInterface::class),
      $logger ?? $this->createMock(LoggerInterface::class),
    );
  }

  private function createRegistry(TestCatalogueImportPolicyStub $cataloguePolicy): ImportGovernanceRegistry {
    $policy = new TestFeatureCatalogueImportPolicy($cataloguePolicy);

    $policyManager = $this->createMock(ImportGovernancePolicyManager::class);
    $policyManager->method('getDefinitions')->willReturn([
      'features' => ['weight' => 0],
    ]);
    $policyManager->method('createInstance')->with('features')->willReturn($policy);

    return new ImportGovernanceRegistry($policyManager);
  }

}
