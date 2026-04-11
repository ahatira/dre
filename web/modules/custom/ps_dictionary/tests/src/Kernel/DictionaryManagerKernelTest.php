<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Kernel tests for DictionaryManager service with real config.
 *
 * Tests the manager with pre-configured dictionaries.
 *
 * @coversDefaultClass \Drupal\ps_dictionary\Service\DictionaryManager
 * @group ps_dictionary
 */
class DictionaryManagerKernelTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'ps',
    'ps_dictionary',
  ];

  /**
   * Dictionary manager service.
   *
   * @var \Drupal\ps_dictionary\Service\DictionaryManagerInterface
   */
  protected $dictionaryManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installConfig(['ps_dictionary']);
    $this->dictionaryManager = \Drupal::service('ps_dictionary.manager');
  }

  /**
   * Tests loading pre-configured dictionary types.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager
   */
  public function testPreConfiguredTypes(): void {
    $types = \Drupal::entityTypeManager()
      ->getStorage('ps_dictionary_type')
      ->loadMultiple();

    $this->assertGreaterThan(0, count($types));
    $this->assertArrayHasKey('property_type', $types);
    $this->assertArrayHasKey('transaction_type', $types);
    $this->assertArrayHasKey('currency', $types);
  }

  /**
   * Tests loading pre-configured entries.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::getEntries
   */
  public function testPreConfiguredEntries(): void {
    $entries = $this->dictionaryManager->getEntries('property_type');
    $this->assertGreaterThan(0, count($entries));
  }

  /**
   * Tests isValid with pre-configured entry.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::isValid
   */
  public function testIsValidWithRealData(): void {
    $result = $this->dictionaryManager->isValid('property_type', 'ACT');
    $this->assertTrue($result);
  }

  /**
   * Tests getLabel with pre-configured entry.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::getLabel
   */
  public function testGetLabelWithRealData(): void {
    $label = $this->dictionaryManager->getLabel('property_type', 'ACT');
    $this->assertIsString($label);
    $this->assertNotEmpty($label);
  }

  /**
   * Tests getOptions with real data.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::getOptions
   */
  public function testGetOptionsWithRealData(): void {
    $options = $this->dictionaryManager->getOptions('property_type');
    $this->assertIsArray($options);
    $this->assertArrayHasKey('ACT', $options);
  }

  /**
   * Tests getEntry with real data.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::getEntry
   */
  public function testGetEntryWithRealData(): void {
    $entry = $this->dictionaryManager->getEntry('property_type', 'ACT');
    $this->assertNotNull($entry);
    $this->assertEquals('ACT', $entry->getCode());
  }

  /**
   * Tests cache invalidation.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::clearCache
   */
  public function testCacheInvalidation(): void {
    // Load entries first.
    $this->dictionaryManager->getEntries('property_type');

    // Clear cache.
    $this->dictionaryManager->clearCache('property_type');

    // Load again - should work fine.
    $entries = $this->dictionaryManager->getEntries('property_type');
    $this->assertGreaterThan(0, count($entries));
  }

  /**
   * Tests non-existent type returns empty array.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::getEntries
   */
  public function testNonExistentTypeReturnsEmpty(): void {
    $entries = $this->dictionaryManager->getEntries('nonexistent_type');
    $this->assertEmpty($entries);
  }

  /**
   * Tests isValid with non-existent type.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::isValid
   */
  public function testIsValidWithNonExistentType(): void {
    $result = $this->dictionaryManager->isValid('nonexistent_type', 'SALE');
    $this->assertFalse($result);
  }

  /**
   * Tests getLabel with non-existent code.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::getLabel
   */
  public function testGetLabelWithNonExistentCode(): void {
    $label = $this->dictionaryManager->getLabel('property_type', 'NONEXISTENT');
    $this->assertNull($label);
  }

  /**
   * Tests isDeprecated with real data.
   *
   * @covers \Drupal\ps_dictionary\Service\DictionaryManager::isDeprecated
   */
  public function testIsDeprecatedWithRealData(): void {
    // Most entries are not deprecated by default.
    $result = $this->dictionaryManager->isDeprecated('property_type', 'SALE');
    $this->assertFalse($result);
  }

}
