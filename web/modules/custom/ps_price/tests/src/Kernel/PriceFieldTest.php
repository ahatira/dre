<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_price\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the ps_price field type.
 *
 * @group ps_price
 */
final class PriceFieldTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'user',
    'system',
    'entity_test',
    'ps',
    'ps_dictionary',
    'ps_price',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test');
    $this->installConfig(['ps', 'ps_dictionary', 'ps_price']);

    // Create a ps_price field for testing.
    FieldStorageConfig::create([
      'field_name' => 'field_test_price',
      'entity_type' => 'entity_test',
      'type' => 'ps_price',
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_test_price',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
      'label' => 'Test Price',
    ])->save();
  }

  /**
   * Tests basic field storage and retrieval.
   */
  public function testPriceFieldStorage(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => 1250.50,
        'currency_code' => 'EUR',
        'unit_code' => 'SUR',
        'period_code' => 'ANN',
        'value_type_code' => NULL,
        'is_from' => TRUE,
        'is_vat_excluded' => TRUE,
        'is_charges_included' => FALSE,
      ],
    ]);
    $entity->save();

    // Reload and verify.
    $loaded = EntityTest::load($entity->id());
    $this->assertNotNull($loaded);

    $price = $loaded->get('field_test_price')->first();
    $this->assertEquals(1250.50, $price->amount);
    $this->assertEquals('EUR', $price->currency_code);
    $this->assertEquals('SUR', $price->unit_code);
    $this->assertEquals('ANN', $price->period_code);
    $this->assertTrue((bool) $price->is_from);
    $this->assertTrue((bool) $price->is_vat_excluded);
    $this->assertFalse((bool) $price->is_charges_included);
  }

  /**
   * Tests isEmpty() method.
   */
  public function testPriceFieldIsEmpty(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => NULL,
        'currency_code' => 'EUR',
      ],
    ]);

    $price = $entity->get('field_test_price')->first();
    $this->assertFalse($price->isEmpty());

    // Set amount.
    $price->amount = 100.00;
    $this->assertFalse($price->isEmpty());
  }

  /**
   * Tests getter methods.
   */
  public function testPriceFieldGetters(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => 500.00,
        'currency_code' => 'EUR',
        'unit_code' => 'GLO',
        'period_code' => 'MEN',
        'value_type_code' => 'MIN',
      ],
    ]);

    $price = $entity->get('field_test_price')->first();
    $this->assertEquals(500.00, $price->getAmount());
    $this->assertEquals('EUR', $price->getCurrencyCode());
    $this->assertEquals('GLO', $price->getUnitCode());
    $this->assertEquals('MEN', $price->getPeriodCode());
    $this->assertEquals('MIN', $price->getValueTypeCode());
  }

  /**
   * Tests multiple values.
   */
  public function testPriceFieldMultipleValues(): void {
    // Create multi-value field storage.
    FieldStorageConfig::create([
      'field_name' => 'field_test_prices',
      'entity_type' => 'entity_test',
      'type' => 'ps_price',
      'cardinality' => -1,
    ])->save();

    FieldConfig::create([
      'field_name' => 'field_test_prices',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
      'label' => 'Test Prices',
    ])->save();

    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_prices' => [
        [
          'amount' => 100.00,
          'currency_code' => 'EUR',
          'unit_code' => 'SUR',
          'period_code' => 'ANN',
        ],
        [
          'amount' => 200.00,
          'currency_code' => 'EUR',
          'unit_code' => 'GLO',
          'period_code' => 'MEN',
        ],
      ],
    ]);
    $entity->save();

    $loaded = EntityTest::load($entity->id());
    $prices = $loaded->get('field_test_prices');

    $this->assertCount(2, $prices);
    $this->assertEquals(100.00, $prices[0]->amount);
    $this->assertEquals(200.00, $prices[1]->amount);
  }

}
