<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\language\Config\LanguageConfigOverride;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\ps_dictionary\Service\DictionaryImportGovernance;
use Drupal\ps_dictionary\Entity\DictionaryTypeInterface;
use Drupal\ps_dictionary\Service\DictionaryCsvImporter;
use Drupal\Tests\UnitTestCase;

/**
 * Unit tests for DictionaryCsvImporter.
 *
 * @group ps_dictionary
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Drupal\ps_dictionary\Service\DictionaryCsvImporter::class)]
final class DictionaryCsvImporterTest extends UnitTestCase {

  /**
   * A temporary CSV file created during each test.
   */
  private string $tempFile;

  #[\Override]
  protected function setUp(): void {
    parent::setUp();
    $this->tempFile = tempnam(sys_get_temp_dir(), 'ps_dict_csv_');
  }

  #[\Override]
  protected function tearDown(): void {
    if (file_exists($this->tempFile)) {
      unlink($this->tempFile);
    }
    parent::tearDown();
  }

  /**
   */
  public function testImportCreatesNewEntries(): void {
    file_put_contents($this->tempFile, "type,code,label,weight\nasset_type,BUR,Bureau,1\n");

    $importer = $this->buildImporter(
      entryLoadMap: ['asset_type.bur' => NULL],
      typeExists: TRUE,
      expectSave: TRUE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
    self::assertSame([], $result['errors']);
  }

  /**
   */
  public function testImportUpdatesExistingEntry(): void {
    file_put_contents($this->tempFile, "type,code,label,weight\nasset_type,BUR,Bureau Updated,2\n");

    $existing = $this->createMock(DictionaryEntryInterface::class);
    $existing->expects(self::exactly(2))->method('set')->willReturnSelf();
    $existing->expects(self::once())->method('save');

    $importer = $this->buildImporter(
      entryLoadMap: ['asset_type.bur' => $existing],
      typeExists: TRUE,
      expectSave: FALSE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
  }

  /**
   */
  public function testImportSkipsRowWhenTypeDoesNotExist(): void {
    file_put_contents($this->tempFile, "type,code,label,weight\nunknown_type,FOO,Foo,1\n");

    $importer = $this->buildImporter(
      entryLoadMap: [],
      typeExists: FALSE,
      expectSave: FALSE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(0, $result['imported']);
    self::assertSame(1, $result['skipped']);
    self::assertCount(1, $result['errors']);
    self::assertStringContainsString('unknown_type', $result['errors'][0]);
  }

  /**
   */
  public function testImportReturnsErrorForMissingFile(): void {
    $importer = $this->buildImporter(entryLoadMap: [], typeExists: FALSE, expectSave: FALSE, languageManager: $this->buildLanguageManager([]));

    $result = $importer->importFromCsv('/non/existent/path.csv');

    self::assertSame(0, $result['imported']);
    self::assertCount(1, $result['errors']);
    self::assertStringContainsString('not found', $result['errors'][0]);
  }

  /**
   */
  public function testImportReturnsErrorForMissingHeader(): void {
    file_put_contents($this->tempFile, '');

    $importer = $this->buildImporter(entryLoadMap: [], typeExists: FALSE, expectSave: FALSE, languageManager: $this->buildLanguageManager([]));

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(0, $result['imported']);
    self::assertCount(1, $result['errors']);
    self::assertStringContainsString('empty', $result['errors'][0]);
  }

  /**
   */
  public function testImportReturnsErrorForMissingColumns(): void {
    file_put_contents($this->tempFile, "type,code\nfoo,BAR\n");

    $importer = $this->buildImporter(entryLoadMap: [], typeExists: FALSE, expectSave: FALSE, languageManager: $this->buildLanguageManager([]));

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(0, $result['imported']);
    self::assertCount(1, $result['errors']);
    self::assertStringContainsString('Missing columns', $result['errors'][0]);
  }

  /**
   */
  public function testImportWithTypeFilterSkipsOtherTypes(): void {
    file_put_contents(
      $this->tempFile,
      "type,code,label,weight\nasset_type,BUR,Bureau,1\ncurrency,EUR,Euro,1\n",
    );

    $importer = $this->buildImporter(
      entryLoadMap: ['asset_type.bur' => NULL],
      typeExists: TRUE,
      expectSave: TRUE,
      languageManager: $this->buildLanguageManager([]),
    );

    $result = $importer->importFromCsv($this->tempFile, 'asset_type');

    self::assertSame(1, $result['imported']);
    self::assertSame(1, $result['skipped']);
  }

  /**
   */
  public function testImportSkipsEmptyCodeOrLabel(): void {
    file_put_contents($this->tempFile, "type,code,label,weight\nasset_type,,Bureau,1\nasset_type,BUR,,1\n");

    $importer = $this->buildImporter(entryLoadMap: [], typeExists: TRUE, expectSave: FALSE, languageManager: $this->buildLanguageManager([]));

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(0, $result['imported']);
    self::assertSame(2, $result['skipped']);
    self::assertCount(2, $result['errors']);
  }

  public function testImportAppliesLabelTranslationsWhenColumnsAreProvided(): void {
    file_put_contents(
      $this->tempFile,
      "type,code,label,weight,label_fr\nasset_type,BUR,Office,1,Bureau\n",
    );

    $newEntry = $this->createMock(DictionaryEntryInterface::class);
    $newEntry->expects(self::once())->method('save');

    $entryStorage = $this->createMock(EntityStorageInterface::class);
    $entryStorage->method('load')->willReturn(NULL);
    $entryStorage->expects(self::once())->method('create')->willReturn($newEntry);

    $typeStorage = $this->createMock(EntityStorageInterface::class);
    $typeStorage->method('load')->willReturn($this->createMock(DictionaryTypeInterface::class));

    $etm = $this->createMock(EntityTypeManagerInterface::class);
    $etm->method('getStorage')->willReturnCallback(static function (string $entityType) use ($entryStorage, $typeStorage): EntityStorageInterface {
      return match ($entityType) {
        'ps_dictionary_entry' => $entryStorage,
        'ps_dictionary_type' => $typeStorage,
        default => throw new \InvalidArgumentException("Unexpected entity type: $entityType"),
      };
    });

    $frLanguage = $this->createMock(LanguageInterface::class);

    $override = $this->createMock(LanguageConfigOverride::class);
    $override->expects(self::once())->method('set')->with('label', 'Bureau')->willReturnSelf();
    $override->expects(self::once())->method('save')->willReturnSelf();

    $languageManager = $this->createMock(ConfigurableLanguageManagerInterface::class);
    $languageManager->method('getLanguages')->willReturn(['fr' => $frLanguage]);
    $languageManager
      ->expects(self::once())
      ->method('getLanguageConfigOverride')
      ->with('fr', 'ps_dictionary.entry.asset_type.bur')
      ->willReturn($override);

    $importer = new DictionaryCsvImporter(
      $etm,
      $languageManager,
      $this->createMock(DictionaryImportGovernance::class),
    );
    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
    self::assertSame([], $result['errors']);
  }

  public function testImportSkipsUnavailableTranslationLanguageSilently(): void {
    file_put_contents(
      $this->tempFile,
      "type,code,label,weight,label_es\nasset_type,BUR,Office,1,Oficina\n",
    );

    $importer = $this->buildImporter(
      entryLoadMap: ['asset_type.bur' => NULL],
      typeExists: TRUE,
      expectSave: TRUE,
      languageManager: $this->buildConfigurableLanguageManager(['fr']),
    );

    $result = $importer->importFromCsv($this->tempFile);

    self::assertSame(1, $result['imported']);
    self::assertSame(0, $result['skipped']);
    self::assertSame([], $result['errors']);
  }

  // ---------------------------------------------------------------------------
  // Helpers
  // ---------------------------------------------------------------------------

  /**
   * Builds an importer with mocked entity type manager.
   *
   * @param array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|null> $entryLoadMap
   *   Map of entry ID → existing entity or NULL.
   * @param bool $typeExists
   *   Whether loading a dictionary type should return an entity.
   * @param bool $expectSave
   *   Whether a new entity create+save is expected (for new entries).
   */
  private function buildImporter(
    array $entryLoadMap,
    bool $typeExists,
    bool $expectSave,
    LanguageManagerInterface $languageManager,
  ): DictionaryCsvImporter {
    $newEntry = $this->createMock(DictionaryEntryInterface::class);
    if ($expectSave) {
      $newEntry->expects(self::once())->method('save');
    }

    $entryStorage = $this->createMock(EntityStorageInterface::class);
    $entryStorage
      ->method('load')
      ->willReturnCallback(static fn(string $id) => $entryLoadMap[$id] ?? NULL);
    if ($expectSave) {
      $entryStorage
        ->expects(self::once())
        ->method('create')
        ->willReturn($newEntry);
    }

    $typeStorage = $this->createMock(EntityStorageInterface::class);
    $typeMock = $typeExists ? $this->createMock(DictionaryTypeInterface::class) : NULL;
    $typeStorage->method('load')->willReturn($typeMock);

    $etm = $this->createMock(EntityTypeManagerInterface::class);
    $etm
      ->method('getStorage')
      ->willReturnCallback(static function (string $entityType) use ($entryStorage, $typeStorage): EntityStorageInterface {
        return match ($entityType) {
          'ps_dictionary_entry' => $entryStorage,
          'ps_dictionary_type'  => $typeStorage,
          default               => throw new \InvalidArgumentException("Unexpected entity type: $entityType"),
        };
      });

    return new DictionaryCsvImporter(
      $etm,
      $languageManager,
      $this->createMock(DictionaryImportGovernance::class),
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

  /**
   * @param string[] $langcodes
   */
  private function buildConfigurableLanguageManager(array $langcodes): ConfigurableLanguageManagerInterface {
    $languages = [];
    foreach ($langcodes as $langcode) {
      $languages[$langcode] = $this->createMock(LanguageInterface::class);
    }

    $languageManager = $this->createMock(ConfigurableLanguageManagerInterface::class);
    $languageManager->method('getLanguages')->willReturn($languages);

    return $languageManager;
  }

}
