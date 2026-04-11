<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Kernel\Entity;

use Drupal\ps_dictionary\Entity\DictionaryType;
use Drupal\KernelTests\Core\Entity\EntityKernelTestBase;

/**
 * Kernel tests for DictionaryType entity persistence and storage.
 *
 * @coversDefaultClass \Drupal\ps_dictionary\Entity\DictionaryType
 * @group ps_dictionary
 */
class DictionaryTypeKernelTest extends EntityKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ps_dictionary'];

  /**
   * Tests creating and saving a dictionary type.
   *
   * @covers ::save
   */
  public function testCreateAndSaveType(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Dictionary Type',
    ]);
    $type->save();

    // Load and verify.
    $loaded = DictionaryType::load('test_type');
    $this->assertNotNull($loaded);
    $this->assertEquals('Test Dictionary Type', $loaded->getLabel());
  }

  /**
   * Tests description field persistence.
   *
   * @covers ::getDescription
   */
  public function testDescriptionPersistence(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
      'description' => 'This is a test type',
    ]);
    $type->save();

    $loaded = DictionaryType::load('test_type');
    $this->assertEquals('This is a test type', $loaded->getDescription());
  }

  /**
   * Tests locked flag persistence.
   *
   * @covers ::isLocked
   */
  public function testLockedPersistence(): void {
    $type = DictionaryType::create([
      'id' => 'locked_type',
      'label' => 'Locked Type',
      'locked' => TRUE,
    ]);
    $type->save();

    $loaded = DictionaryType::load('locked_type');
    $this->assertTrue($loaded->isLocked());
  }

  /**
   * Tests metadata schema persistence.
   *
   * @covers ::getMetadataSchema
   */
  public function testMetadataSchemaPersistence(): void {
    $schema = 'field1: type1' . PHP_EOL . 'field2: type2';
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
      'metadata_schema' => $schema,
    ]);
    $type->save();

    $loaded = DictionaryType::load('test_type');
    $this->assertEquals($schema, $loaded->getMetadataSchema());
  }

  /**
   * Tests setDescription mutator.
   *
   * @covers ::setDescription
   */
  public function testSetDescriptionMutator(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);

    $type->setDescription('New description');
    $this->assertEquals('New description', $type->getDescription());
  }

  /**
   * Tests setMetadataSchema mutator.
   *
   * @covers ::setMetadataSchema
   */
  public function testSetMetadataSchemaMutator(): void {
    $type = DictionaryType::create([
      'id' => 'test_type',
      'label' => 'Test Type',
    ]);

    $schema = 'new_field: string';
    $type->setMetadataSchema($schema);
    $this->assertEquals($schema, $type->getMetadataSchema());
  }

  /**
   * Tests deleting a type.
   *
   * @covers ::delete
   */
  public function testDeleteType(): void {
    $type = DictionaryType::create([
      'id' => 'to_delete',
      'label' => 'To Delete',
    ]);
    $type->save();

    // Verify created.
    $this->assertNotNull(DictionaryType::load('to_delete'));

    // Delete.
    DictionaryType::load('to_delete')->delete();

    // Verify deleted.
    $this->assertNull(DictionaryType::load('to_delete'));
  }

  /**
   * Tests loading all types.
   *
   * @covers \Drupal\Core\Entity\EntityStorageInterface::loadMultiple
   */
  public function testLoadAllTypes(): void {
    // Create test types.
    for ($i = 1; $i <= 2; $i++) {
      $type = DictionaryType::create([
        'id' => "type$i",
        'label' => "Type $i",
      ]);
      $type->save();
    }

    // Load all.
    $storage = \Drupal::entityTypeManager()->getStorage('ps_dictionary_type');
    $types = $storage->loadMultiple();

    $this->assertGreaterThanOrEqual(2, count($types));
  }

}
