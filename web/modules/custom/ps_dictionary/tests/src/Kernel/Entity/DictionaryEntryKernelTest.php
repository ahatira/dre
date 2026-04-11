<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Kernel\Entity;

use Drupal\ps_dictionary\Entity\DictionaryEntry;
use Drupal\ps_dictionary\Entity\DictionaryType;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Kernel tests for DictionaryEntry entity persistence and storage.
 *
 * @coversDefaultClass \Drupal\ps_dictionary\Entity\DictionaryEntry
 * @group ps_dictionary
 */
class DictionaryEntryKernelTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ps_dictionary'];

  /**
   * Tests creating and saving a dictionary entry.
   *
   * @covers ::save
   */
  public function testCreateAndSaveEntry(): void {
    // Create type first.
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    // Create entry.
    $entry = DictionaryEntry::create([
      'id' => 'test_type.test_code',
      'dictionary_type' => 'test_type',
      'code' => 'TEST_CODE',
      'label' => 'Test Code Label',
    ]);
    $entry->save();

    // Load and verify.
    $loaded = DictionaryEntry::load('test_type.test_code');
    $this->assertNotNull($loaded);
    $this->assertEquals('TEST_CODE', $loaded->getCode());
    $this->assertEquals('Test Code Label', $loaded->getLabel());
  }

  /**
   * Tests metadata persistence.
   *
   * @covers ::getMetadata
   */
  public function testMetadataPersistence(): void {
    // Create type.
    $type = DictionaryType::create([
      'id' => 'currency',
      'label' => 'Currency',
    ]);
    $type->save();

    // Create entry with metadata.
    $metadata = [
      'symbol' => '€',
      'decimal_places' => 2,
      'iso_code' => 'EUR',
    ];

    $entry = DictionaryEntry::create([
      'id' => 'currency.eur',
      'dictionary_type' => 'currency',
      'code' => 'EUR',
      'label' => 'Euro',
      'metadata' => $metadata,
    ]);
    $entry->save();

    // Load and verify metadata.
    $loaded = DictionaryEntry::load('currency.eur');
    $this->assertEquals($metadata, $loaded->getMetadata());
  }

  /**
   * Tests status flag persistence.
   *
   * @covers ::isActive
   */
  public function testStatusPersistence(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    $entry = DictionaryEntry::create([
      'id' => 'test_type.inactive',
      'dictionary_type' => 'test_type',
      'code' => 'INACTIVE',
      'label' => 'Inactive',
      'status' => FALSE,
    ]);
    $entry->save();

    $loaded = DictionaryEntry::load('test_type.inactive');
    $this->assertFalse($loaded->isActive());
  }

  /**
   * Tests weight field persistence.
   *
   * @covers ::getWeight
   */
  public function testWeightPersistence(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    $entry = DictionaryEntry::create([
      'id' => 'test_type.weighted',
      'dictionary_type' => 'test_type',
      'code' => 'WEIGHTED',
      'label' => 'Weighted Entry',
      'weight' => 10,
    ]);
    $entry->save();

    $loaded = DictionaryEntry::load('test_type.weighted');
    $this->assertEquals(10, $loaded->getWeight());
  }

  /**
   * Tests deprecated flag persistence.
   *
   * @covers ::isDeprecated
   */
  public function testDeprecatedPersistence(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    $entry = DictionaryEntry::create([
      'id' => 'test_type.deprecated',
      'dictionary_type' => 'test_type',
      'code' => 'DEPRECATED',
      'label' => 'Deprecated Code',
      'deprecated' => TRUE,
    ]);
    $entry->save();

    $loaded = DictionaryEntry::load('test_type.deprecated');
    $this->assertTrue($loaded->isDeprecated());
  }

  /**
   * Tests deleting an entry.
   *
   * @covers ::delete
   */
  public function testDeleteEntry(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    $entry = DictionaryEntry::create([
      'id' => 'test_type.to_delete',
      'dictionary_type' => 'test_type',
      'code' => 'TO_DELETE',
      'label' => 'To Delete',
    ]);
    $entry->save();

    // Verify created.
    $this->assertNotNull(DictionaryEntry::load('test_type.to_delete'));

    // Delete.
    DictionaryEntry::load('test_type.to_delete')->delete();

    // Verify deleted.
    $this->assertNull(DictionaryEntry::load('test_type.to_delete'));
  }

  /**
   * Tests querying entries by type.
   *
   * @covers \Drupal\Core\Entity\EntityStorageInterface::loadByProperties
   */
  public function testQueryByType(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    // Create multiple entries.
    for ($i = 1; $i <= 3; $i++) {
      $entry = DictionaryEntry::create([
        'id' => "test_type.code$i",
        'dictionary_type' => 'test_type',
        'code' => "CODE$i",
        'label' => "Code $i",
      ]);
      $entry->save();
    }

    // Query by type.
    $storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_entry');
    $entries = $storage->loadByProperties(['dictionary_type' => 'test_type']);

    $this->assertCount(3, $entries);
  }

  /**
   * Tests description field.
   *
   * @covers ::getDescription
   */
  public function testDescriptionField(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    $entry = DictionaryEntry::create([
      'id' => 'test_type.with_desc',
      'dictionary_type' => 'test_type',
      'code' => 'WITH_DESC',
      'label' => 'With Description',
      'description' => 'This is a description',
    ]);
    $entry->save();

    $loaded = DictionaryEntry::load('test_type.with_desc');
    $this->assertEquals('This is a description', $loaded->getDescription());
  }

  /**
   * Tests setCode mutator.
   *
   * @covers ::setCode
   */
  public function testSetCodeMutator(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);
    $type->save();

    $entry = DictionaryEntry::create([
      'id' => 'test_type.original',
      'dictionary_type' => 'test_type',
      'code' => 'ORIGINAL',
      'label' => 'Original',
    ]);

    $entry->setCode('MODIFIED');
    $this->assertEquals('MODIFIED', $entry->getCode());
  }

}
