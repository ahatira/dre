<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_price\Kernel;

use Drupal\entity_test\Entity\EntityTest;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests ps_price field validation.
 *
 * @group ps_price
 */
final class PriceValidationTest extends KernelTestBase {

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

    // Create a ps_price field.
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
   * Tests validation of negative amount.
   */
  public function testNegativeAmountValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => -100.00,
        'currency_code' => 'EUR',
      ],
    ]);

    $violations = $entity->validate();
    $this->assertGreaterThan(0, $violations->count());

    $found = FALSE;
    foreach ($violations as $violation) {
      if (str_contains((string) $violation->getMessage(), 'negative')) {
        $found = TRUE;
        break;
      }
    }
    $this->assertTrue($found, 'Expected validation error for negative amount.');
  }

  /**
   * Tests validation of missing currency.
   */
  public function testMissingCurrencyValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => 100.00,
        'currency_code' => NULL,
      ],
    ]);

    $violations = $entity->validate();

    // Debug: dump all violations.
    if ($violations->count() === 0) {
      $this->fail('No validation errors found, but expected currency error.');
    }

    $found = FALSE;
    foreach ($violations as $violation) {
      $message = (string) $violation->getMessage();
      if (str_contains($message, 'currency') ||
          str_contains($message, 'Currency') ||
          str_contains($message, 'missing') ||
          str_contains($message, 'required')) {
        $found = TRUE;
        break;
      }
    }
    $this->assertTrue($found, 'Expected validation error for missing currency. Violations count: ' . $violations->count());
  }

  /**
   * Tests validation of invalid dictionary codes.
   */
  public function testInvalidDictionaryCodeValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => 100.00,
        'currency_code' => 'EUR',
        'unit_code' => 'INVALID_CODE',
      ],
    ]);

    $violations = $entity->validate();
    $this->assertGreaterThan(0, $violations->count());

    $found = FALSE;
    foreach ($violations as $violation) {
      if (str_contains((string) $violation->getMessage(), 'valid') ||
          str_contains((string) $violation->getMessage(), 'unit')) {
        $found = TRUE;
        break;
      }
    }
    $this->assertTrue($found, 'Expected validation error for invalid unit code.');
  }

  /**
   * Tests validation passes with valid data.
   */
  public function testValidDataPassesValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => 1250.50,
        'currency_code' => 'EUR',
        'unit_code' => 'SUR',
        'period_code' => 'ANN',
        'is_vat_excluded' => TRUE,
      ],
    ]);

    $violations = $entity->validate();

    // Filter out only violations for field_test_price.
    $priceViolations = [];
    foreach ($violations as $violation) {
      if (str_contains($violation->getPropertyPath(), 'field_test_price')) {
        $priceViolations[] = $violation;
      }
    }

    $this->assertCount(0, $priceViolations, 'Valid data should not produce validation errors.');
  }

  /**
   * Tests that on-request without amount is accepted.
   */
  public function testOnRequestWithoutAmountPassesValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'is_on_request' => TRUE,
      ],
    ]);

    $violations = $entity->validate();
    $priceViolations = [];
    foreach ($violations as $violation) {
      if (str_contains($violation->getPropertyPath(), 'field_test_price')) {
        $priceViolations[] = $violation;
      }
    }

    $this->assertCount(0, $priceViolations, 'On-request price without amount should be valid.');
  }

  /**
   * Tests that on-request with amount is rejected.
   */
  public function testOnRequestWithAmountFailsValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'amount' => 390.00,
        'currency_code' => 'EUR',
        'is_on_request' => TRUE,
      ],
    ]);

    $violations = $entity->validate();
    $this->assertGreaterThan(0, $violations->count());

    $found = FALSE;
    foreach ($violations as $violation) {
      if (str_contains((string) $violation->getMessage(), 'on request')) {
        $found = TRUE;
        break;
      }
    }
    $this->assertTrue($found, 'Expected validation error for amount + on-request combination.');
  }

  /**
   * Tests that missing amount without on-request is rejected.
   */
  public function testMissingAmountWithoutOnRequestFailsValidation(): void {
    $entity = EntityTest::create([
      'name' => 'Test Entity',
      'field_test_price' => [
        'currency_code' => 'EUR',
        'is_on_request' => FALSE,
      ],
    ]);

    $violations = $entity->validate();
    $this->assertGreaterThan(0, $violations->count());

    $found = FALSE;
    foreach ($violations as $violation) {
      if (str_contains((string) $violation->getMessage(), 'Provide an amount')) {
        $found = TRUE;
        break;
      }
    }
    $this->assertTrue($found, 'Expected validation error when no amount and not on-request.');
  }

}
