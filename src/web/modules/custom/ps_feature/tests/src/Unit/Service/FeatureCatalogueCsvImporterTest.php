<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigOverride;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\ps_feature\Entity\FeatureDefinition;
use Drupal\ps_feature\Entity\FeatureGroup;
use Drupal\ps_feature\Service\FeatureCatalogueCsvImporter;
use Drupal\ps_feature\Service\FeatureCatalogueCsvMapper;
use Drupal\ps_core\Service\ImportGovernanceGlobalResolver;
use Drupal\ps_feature\Service\FeatureCatalogueGovernance;
use Drupal\ps_feature\Service\FeatureTypeManager;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @coversDefaultClass \Drupal\ps_feature\Service\FeatureCatalogueCsvImporter
 */
#[CoversClass(FeatureCatalogueCsvImporter::class)]
#[Group('ps_feature')]
final class FeatureCatalogueCsvImporterTest extends UnitTestCase {

  private string $tempFile;

  protected function setUp(): void {
    parent::setUp();
    $this->tempFile = tempnam(sys_get_temp_dir(), 'ps_feature_csv_');
  }

  protected function tearDown(): void {
    if (file_exists($this->tempFile)) {
      unlink($this->tempFile);
    }
    parent::tearDown();
  }

  public function testImportCreatesNewDefinition(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur\nTEC_SURFACE_TOTALE,Équipements,Total surface,Nombre\n",
    );

    $importer = $this->buildImporter(
      definitionLoadMap: ['tec_surface_totale' => NULL],
      groupExists: TRUE,
      expectCreate: TRUE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
    self::assertSame([], $result['errors']);
  }

  public function testImportUpdatesExistingDefinition(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur\nTEC_SURFACE_TOTALE,Équipements,Total surface updated,Nombre\n",
    );

    $existing = $this->createMock(FeatureDefinition::class);
    $existing->method('isTypeLocked')->willReturn(FALSE);
    $existing->method('set')->willReturnSelf();
    $existing->method('setSource')->willReturnSelf();
    $existing->method('setInternallyLocked')->willReturnSelf();
    $existing->method('setSourceTracking')->willReturnSelf();
    $existing->expects(self::once())->method('save');

    $importer = $this->buildImporter(
      definitionLoadMap: ['tec_surface_totale' => $existing],
      groupExists: TRUE,
      expectCreate: FALSE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
  }

  public function testImportRespectsTypeLockedOnUpdate(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur\nTEC_SURFACE_TOTALE,Équipements,Total surface updated,Texte\n",
    );

    $existing = $this->createMock(FeatureDefinition::class);
    $existing->method('isTypeLocked')->willReturn(TRUE);
    $typeDriverUpdates = 0;
    $existing->method('set')->willReturnCallback(function (string $key, mixed $value) use (&$typeDriverUpdates, $existing): FeatureDefinition {
      if ($key === 'type_driver') {
        $typeDriverUpdates++;
      }
      return $existing;
    });
    $existing->method('setSource')->willReturnSelf();
    $existing->method('setInternallyLocked')->willReturnSelf();
    $existing->method('setSourceTracking')->willReturnSelf();
    $existing->expects(self::once())->method('save');

    $importer = $this->buildImporter(
      definitionLoadMap: ['tec_surface_totale' => $existing],
      groupExists: TRUE,
      expectCreate: FALSE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $typeDriverUpdates);
  }

  public function testImportUsesDefaultGroupWhenCategoryEmpty(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur\nTEC_NO_CATEGORY,,Label without category,Nombre\n",
    );

    $importer = $this->buildImporter(
      definitionLoadMap: ['tec_no_category' => NULL],
      groupExists: TRUE,
      expectCreate: TRUE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
    self::assertSame([], $result['errors']);
  }

  public function testImportSkipsUnknownCategory(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur\nTEC_UNKNOWN,Unknown,Label,Nombre\n",
    );

    $importer = $this->buildImporter(
      definitionLoadMap: [],
      groupExists: TRUE,
      expectCreate: FALSE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(0, $result['imported']);
    self::assertSame(1, $result['skipped']);
    self::assertStringContainsString('unknown categorie', $result['errors'][0]);
  }

  public function testDryRunDoesNotPersist(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur\nTEC_SURFACE_TOTALE,Équipements,Total surface,Nombre\n",
    );

    $definitionStorage = $this->createMock(EntityStorageInterface::class);
    $definitionStorage->method('load')->willReturn(NULL);
    $definitionStorage->expects(self::never())->method('create');

    $group = $this->createMock(FeatureGroup::class);
    $groupStorage = $this->createMock(EntityStorageInterface::class);
    $groupStorage->method('load')->willReturn($group);

    $importer = $this->buildImporterWithStorages(
      $definitionStorage,
      $groupStorage,
      $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile, TRUE);

    self::assertTrue($result['dry_run']);
    self::assertSame(1, $result['imported']);
  }

  public function testImportAppliesLabelTranslation(): void {
    file_put_contents(
      $this->tempFile,
      "code,categorie,libelle,type_valeur,libelle_fr\nTEC_SURFACE_TOTALE,Équipements,Total surface,Nombre,Surface totale\n",
    );

    $newDefinition = $this->createMock(FeatureDefinition::class);
    $newDefinition->expects(self::once())->method('save');

    $definitionStorage = $this->createMock(EntityStorageInterface::class);
    $definitionStorage->method('load')->willReturn(NULL);
    $definitionStorage->expects(self::once())->method('create')->willReturn($newDefinition);

    $group = $this->createMock(FeatureGroup::class);
    $groupStorage = $this->createMock(EntityStorageInterface::class);
    $groupStorage->method('load')->willReturn($group);

    $frLanguage = $this->createMock(LanguageInterface::class);
    $override = $this->createMock(LanguageConfigOverride::class);
    $override->expects(self::once())->method('set')->with('label', 'Surface totale')->willReturnSelf();
    $override->expects(self::once())->method('save')->willReturnSelf();

    $languageManager = $this->createMock(ConfigurableLanguageManagerInterface::class);
    $languageManager->method('getLanguages')->willReturn(['fr' => $frLanguage]);
    $languageManager
      ->expects(self::once())
      ->method('getLanguageConfigOverride')
      ->with('fr', 'ps_feature.feature_definition.tec_surface_totale')
      ->willReturn($override);

    $importer = $this->buildImporterWithStorages($definitionStorage, $groupStorage, $languageManager);
    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
  }

  /**
   * @param array<string, \Drupal\ps_feature\Entity\FeatureDefinition|null> $definitionLoadMap
   */
  private function buildImporter(
    array $definitionLoadMap,
    bool $groupExists,
    bool $expectCreate,
    LanguageManagerInterface $languageManager,
  ): FeatureCatalogueCsvImporter {
    $newDefinition = $this->createMock(FeatureDefinition::class);
    if ($expectCreate) {
      $newDefinition->expects(self::once())->method('save');
    }

    $definitionStorage = $this->createMock(EntityStorageInterface::class);
    $definitionStorage
      ->method('load')
      ->willReturnCallback(static fn(string $id) => $definitionLoadMap[$id] ?? NULL);
    if ($expectCreate) {
      $definitionStorage
        ->expects(self::once())
        ->method('create')
        ->willReturn($newDefinition);
    }

    $groupStorage = $this->createMock(EntityStorageInterface::class);
    $groupStorage->method('load')->willReturn($groupExists ? $this->createMock(FeatureGroup::class) : NULL);

    return $this->buildImporterWithStorages($definitionStorage, $groupStorage, $languageManager);
  }

  private function buildImporterWithStorages(
    EntityStorageInterface $definitionStorage,
    EntityStorageInterface $groupStorage,
    LanguageManagerInterface $languageManager,
  ): FeatureCatalogueCsvImporter {
    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager
      ->method('getStorage')
      ->willReturnCallback(static function (string $entityType) use ($definitionStorage, $groupStorage): EntityStorageInterface {
        return match ($entityType) {
          'fb_feature_definition' => $definitionStorage,
          'fb_feature_group' => $groupStorage,
          default => throw new \InvalidArgumentException("Unexpected entity type: $entityType"),
        };
      });

    $featureTypeManager = $this->createMock(FeatureTypeManager::class);
    $featureTypeManager->method('getAllTypes')->willReturn([
      'flag' => 'Flag',
      'yes_no' => 'Yes/No',
      'numeric' => 'Numeric',
      'text' => 'Text',
      'date' => 'Date',
    ]);

    return new FeatureCatalogueCsvImporter(
      $entityTypeManager,
      $languageManager,
      new FeatureCatalogueCsvMapper(),
      $featureTypeManager,
      $this->buildCatalogueGovernance(),
    );
  }

  private function buildCatalogueGovernance(): FeatureCatalogueGovernance {
    $featureGovernanceConfig = $this->createMock(\Drupal\Core\Config\ImmutableConfig::class);
    $featureGovernanceConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => match ($key) {
        'csv_import.lock_on_import' => TRUE,
        default => NULL,
      },
    );

    $globalGovernanceConfig = $this->createMock(\Drupal\Core\Config\ImmutableConfig::class);
    $globalGovernanceConfig->method('get')->willReturnCallback(
      static fn(string $key): mixed => match ($key) {
        'import.global_lock_strategy.source_config' => 'ps_migrate.import_pipeline_settings',
        'import.global_lock_strategy.source_key' => 'lock_strategy_default',
        default => NULL,
      },
    );

    $migrateConfig = $this->createMock(\Drupal\Core\Config\ImmutableConfig::class);

    $configFactory = $this->createMock(\Drupal\Core\Config\ConfigFactoryInterface::class);
    $configFactory->method('get')->willReturnMap([
      [FeatureCatalogueGovernance::CONFIG_NAME, $featureGovernanceConfig],
      [ImportGovernanceGlobalResolver::CONFIG_NAME, $globalGovernanceConfig],
      ['ps_migrate.import_pipeline_settings', $migrateConfig],
    ]);

    return new FeatureCatalogueGovernance(
      $configFactory,
      $this->createMock(\Drupal\ps_core\Service\EntityProtectionManagerInterface::class),
      $this->createMock(EntityTypeManagerInterface::class),
      new ImportGovernanceGlobalResolver(
        $configFactory,
        $this->createMock(\Drupal\Core\Extension\ModuleHandlerInterface::class),
      ),
    );
  }

  /**
   * @param string[] $langcodes
   */
  private function buildLanguageManager(array $langcodes): LanguageManagerInterface {
    $languages = [];
    foreach ($langcodes as $langcode) {
      $languages[$langcode] = $this->createMock(LanguageInterface::class);
    }

    $languageManager = $this->createMock(LanguageManagerInterface::class);
    $languageManager->method('getLanguages')->willReturn($languages);

    return $languageManager;
  }

}
