<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_diagnostic\Kernel\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\ps_diagnostic\Entity\PsDiagnostic;

/**
 * Tests the 'ps_diagnostic' field type.
 *
 * @group ps_diagnostic
 * @coversDefaultClass \Drupal\ps_diagnostic\Plugin\Field\FieldType\DiagnosticItem
 */
final class DiagnosticItemTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'entity_test',
    'user',
    'system',
    'ps',
    'ps_dictionary',
    'ps_diagnostic',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('entity_test');
    $this->installEntitySchema('user');
    $this->installConfig(['ps_diagnostic']);

    // Create field storage and config.
    FieldStorageConfig::create([
      'field_name' => 'field_diagnostic_test',
      'entity_type' => 'entity_test',
      'type' => 'ps_diagnostic',
      'cardinality' => -1,
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_diagnostic_test',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
      'label' => 'Diagnostic Test',
    ])->save();
  }

  /**
   * Tests field storage and schema.
   *
   * @covers ::schema
   */
  public function testFieldSchema(): void {
    $fieldStorage = FieldStorageConfig::loadByName('entity_test', 'field_diagnostic_test');
    $this->assertNotNull($fieldStorage);

    $schema = $fieldStorage->getSchema();
    $this->assertArrayHasKey('columns', $schema);
    $this->assertArrayHasKey('indexes', $schema);

    // Verify all 7 columns exist.
    $this->assertArrayHasKey('type_id', $schema['columns']);
    $this->assertArrayHasKey('value', $schema['columns']);
    $this->assertArrayHasKey('class', $schema['columns']);
    $this->assertArrayHasKey('valid_from', $schema['columns']);
    $this->assertArrayHasKey('valid_to', $schema['columns']);
    $this->assertArrayHasKey('no_classification', $schema['columns']);
    $this->assertArrayHasKey('non_applicable', $schema['columns']);

    // Verify indexes.
    $this->assertArrayHasKey('type_id', $schema['indexes']);
    $this->assertArrayHasKey('value', $schema['indexes']);
    $this->assertArrayHasKey('class', $schema['indexes']);
  }

  /**
   * Tests field item creation and property access.
   *
   * @covers ::propertyDefinitions
   * @covers ::setValue
   */
  public function testFieldItemCreation(): void {
    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'value' => 150.0,
      'class' => 'C',
      'valid_from' => '2022-01-15',
      'valid_to' => '2032-01-15',
      'no_classification' => FALSE,
      'non_applicable' => FALSE,
    ]);

    $this->assertInstanceOf(FieldItemListInterface::class, $entity->field_diagnostic_test);
    $item = $entity->field_diagnostic_test->first();
    $this->assertInstanceOf(FieldItemInterface::class, $item);

    // Test property access.
    $this->assertSame('dpe', $item->type_id);
    $this->assertSame(150.0, $item->value);
    $this->assertSame('C', $item->class);
    $this->assertSame('2022-01-15', $item->valid_from);
    $this->assertSame('2032-01-15', $item->valid_to);
    $this->assertFalse((bool) $item->no_classification);
    $this->assertFalse((bool) $item->non_applicable);
  }

  /**
   * Tests isEmpty() method.
   *
   * @covers ::isEmpty
   * @dataProvider isEmptyProvider
   */
  public function testIsEmpty(array $values, bool $expected): void {
    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem($values);
    $item = $entity->field_diagnostic_test->first();

    $this->assertSame($expected, $item->isEmpty());
  }

  /**
   * Data provider for testIsEmpty().
   *
   * @return array<string, array<int, mixed>>
   *   Test cases.
   */
  public static function isEmptyProvider(): array {
    return [
      'completely empty' => [
        [],
        TRUE,
      ],
      'only type_id' => [
        ['type_id' => 'dpe'],
        FALSE,
      ],
      'only value' => [
        ['value' => 150.0],
        FALSE,
      ],
      'only class' => [
        ['class' => 'C'],
        FALSE,
      ],
      'only valid_from' => [
        ['valid_from' => '2022-01-15'],
        FALSE,
      ],
      'only valid_to' => [
        ['valid_to' => '2032-01-15'],
        FALSE,
      ],
      'only no_classification flag' => [
        ['no_classification' => TRUE],
        FALSE,
      ],
      'only non_applicable flag' => [
        ['non_applicable' => TRUE],
        FALSE,
      ],
      'all fields populated' => [
        [
          'type_id' => 'dpe',
          'value' => 150.0,
          'class' => 'C',
          'valid_from' => '2022-01-15',
          'valid_to' => '2032-01-15',
        ],
        FALSE,
      ],
      'empty strings treated as empty' => [
        [
          'type_id' => '',
          'value' => '',
          'class' => '',
          'valid_from' => '',
          'valid_to' => '',
        ],
        TRUE,
      ],
    ];
  }

  /**
   * Tests setValue() normalization.
   *
   * @covers ::setValue
   */
  public function testSetValueNormalization(): void {
    $entity = EntityTest::create();

    // Test numeric value normalization.
    $entity->field_diagnostic_test->appendItem([
      'value' => '150.5',
    ]);
    $item = $entity->field_diagnostic_test->first();
    $this->assertSame(150.5, $item->value);
    $this->assertIsFloat($item->value);

    // Test empty string → NULL for value.
    $entity->field_diagnostic_test[0]->setValue(['value' => '']);
    $this->assertNull($entity->field_diagnostic_test[0]->value);

    // Test empty string → NULL for class.
    $entity->field_diagnostic_test[0]->setValue(['class' => '']);
    $this->assertNull($entity->field_diagnostic_test[0]->class);

    // Test boolean casting.
    $entity->field_diagnostic_test[0]->setValue(['no_classification' => 1]);
    $this->assertTrue((bool) $entity->field_diagnostic_test[0]->no_classification);

    $entity->field_diagnostic_test[0]->setValue(['non_applicable' => 0]);
    $this->assertFalse((bool) $entity->field_diagnostic_test[0]->non_applicable);
  }

  /**
   * Tests field validation with valid data.
   *
   * @covers ::getConstraints
   */
  public function testValidationWithValidData(): void {
    // Create DPE diagnostic config entity for validation (only if doesn't exist).
    $storage = \Drupal::entityTypeManager()->getStorage('diagnostic');
    if (!$storage->load('dpe')) {
      PsDiagnostic::create([
        'id' => 'dpe',
        'label' => 'DPE',
        'unit' => 'kWh/m²/year',
        'icon' => 'energy',
        'classes' => [
          'a' => ['label' => 'A', 'color' => '#00A651', 'range_max' => 70],
          'b' => ['label' => 'B', 'color' => '#8DC63F', 'range_max' => 110],
          'c' => ['label' => 'C', 'color' => '#FFF200', 'range_max' => 180],
        ],
      ])->save();
    }

    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'value' => 150.0,
      'class' => 'C',
      'valid_from' => '2022-01-15',
      'valid_to' => '2032-01-15',
    ]);

    $violations = $entity->field_diagnostic_test->validate();
    $this->assertCount(0, $violations);
  }

  /**
   * Tests field validation with invalid type_id.
   *
   * @covers ::validateTypeId
   */
  public function testValidationWithInvalidTypeId(): void {
    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'invalid_type',
      'value' => 150.0,
      'class' => 'C',
    ]);

    $violations = $entity->field_diagnostic_test->validate();
    $this->assertGreaterThan(0, $violations->count());
  }

  /**
   * Tests field validation with invalid class label.
   *
   * @covers ::validateLabelCode
   * @dataProvider invalidClassProvider
   */
  public function testValidationWithInvalidClass(string $invalidClass): void {
    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'class' => $invalidClass,
    ]);

    $violations = $entity->field_diagnostic_test->validate();
    $this->assertGreaterThan(0, $violations->count());
  }

  /**
   * Data provider for testValidationWithInvalidClass().
   *
   * @return array<string, array<string>>
   *   Invalid class labels.
   */
  public static function invalidClassProvider(): array {
    return [
      'numeric only' => ['123'],
      'special characters' => ['A@B'],
      'lowercase with invalid chars' => ['a!b'],
      'spaces' => ['A B'],
    ];
  }

  /**
   * Tests field validation with valid class labels.
   *
   * @covers ::validateLabelCode
   * @dataProvider validClassProvider
   */
  public function testValidationWithValidClass(string $validClass): void {
    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem([
      'class' => $validClass,
    ]);

    $violations = $entity->field_diagnostic_test->validate();
    // Should have 0 violations for class validation specifically.
    $classViolations = [];
    foreach ($violations as $violation) {
      if (str_contains($violation->getMessage()->__toString(), 'label code')) {
        $classViolations[] = $violation;
      }
    }
    $this->assertCount(0, $classViolations);
  }

  /**
   * Data provider for testValidationWithValidClass().
   *
   * @return array<string, array<string>>
   *   Valid class labels.
   */
  public static function validClassProvider(): array {
    return [
      'simple letter' => ['A'],
      'plus suffix' => ['G+'],
      'double plus' => ['G++'],
      'minus suffix' => ['A-'],
      'double minus' => ['A--'],
      'multiple letters' => ['ABC'],
      'mixed case with plus' => ['Ab+'],
    ];
  }

  /**
   * Tests entity save and load with diagnostic field.
   *
   * @covers ::setValue
   */
  public function testEntitySaveAndLoad(): void {
    $entity = EntityTest::create();
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'value' => 200.0,
      'class' => 'D',
      'valid_from' => '2023-06-01',
      'valid_to' => '2033-06-01',
      'no_classification' => FALSE,
      'non_applicable' => FALSE,
    ]);
    $entity->save();

    // Reload entity.
    $loaded = EntityTest::load($entity->id());
    $this->assertNotNull($loaded);

    $item = $loaded->field_diagnostic_test->first();
    $this->assertSame('dpe', $item->type_id);
    $this->assertSame(200.0, $item->value);
    $this->assertSame('D', $item->class);
    $this->assertSame('2023-06-01', $item->valid_from);
    $this->assertSame('2033-06-01', $item->valid_to);
  }

  /**
   * Tests multiple field values.
   */
  public function testMultipleFieldValues(): void {
    $entity = EntityTest::create();

    // Add multiple diagnostic items.
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'dpe',
      'class' => 'A',
      'value' => 50.0,
    ]);
    $entity->field_diagnostic_test->appendItem([
      'type_id' => 'ges',
      'class' => 'B',
      'value' => 10.0,
    ]);

    $this->assertCount(2, $entity->field_diagnostic_test);
    $this->assertSame('dpe', $entity->field_diagnostic_test[0]->type_id);
    $this->assertSame('ges', $entity->field_diagnostic_test[1]->type_id);
  }

}
