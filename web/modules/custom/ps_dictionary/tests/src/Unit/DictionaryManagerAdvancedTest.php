<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit;

use Drupal\ps_dictionary\Entity\DictionaryTypeInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\ps_dictionary\Service\DictionaryManager;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Additional unit tests for DictionaryManager advanced features.
 *
 * @coversDefaultClass \Drupal\ps_dictionary\Service\DictionaryManager
 * @group ps_dictionary
 */
class DictionaryManagerAdvancedTest extends UnitTestCase {

  /**
   * The dictionary manager.
   */
  private DictionaryManager $manager;

  /**
   * The entity type manager mock.
   */
  private MockObject $entityTypeManager;

  /**
   * The cache mock.
   */
  private MockObject $cache;

  /**
   * The logger mock.
   */
  private MockObject $logger;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create mocks.
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->cache = $this->createMock(CacheBackendInterface::class);
    $this->logger = $this->createMock(LoggerChannelInterface::class);

    $loggerFactory = $this->createMock(LoggerChannelFactoryInterface::class);
    $loggerFactory->method('get')->willReturn($this->logger);

    $this->manager = new DictionaryManager(
      $this->entityTypeManager,
      $this->cache,
      $loggerFactory
    );
  }

  /**
   * Tests getMetadataValue returns correct value.
   *
   * @covers ::getMetadataValue
   */
  public function testGetMetadataValueReturnsValue(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getMetadataValue')->with('symbol', NULL)->willReturn('€');

    $this->mockGetEntry('currency', 'EUR', $entry);

    $value = $this->manager->getMetadataValue('currency', 'EUR', 'symbol');
    $this->assertEquals('€', $value);
  }

  /**
   * Tests getMetadataValue returns default when key missing.
   *
   * @covers ::getMetadataValue
   */
  public function testGetMetadataValueReturnsDefault(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getMetadataValue')->with('missing_key', 'default')->willReturn('default');

    $this->mockGetEntry('currency', 'EUR', $entry);

    $value = $this->manager->getMetadataValue('currency', 'EUR', 'missing_key', 'default');
    $this->assertEquals('default', $value);
  }

  /**
   * Tests getMetadataValue returns NULL when entry not found.
   *
   * @covers ::getMetadataValue
   */
  public function testGetMetadataValueWithNonExistentEntry(): void {
    $this->mockGetEntry('currency', 'NONEXISTENT', NULL);

    $value = $this->manager->getMetadataValue('currency', 'NONEXISTENT', 'symbol');
    $this->assertNull($value);
  }

  /**
   * Tests getMetadataTyped returns typed value.
   *
   * @covers ::getMetadataTyped
   */
  public function testGetMetadataTypedReturnsCorrectType(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getMetadataTyped')->with('decimal_places', 'int', NULL)->willReturn(2);

    $this->mockGetEntry('currency', 'EUR', $entry);

    $value = $this->manager->getMetadataTyped('currency', 'EUR', 'decimal_places', 'int');
    $this->assertIsInt($value);
    $this->assertEquals(2, $value);
  }

  /**
   * Tests getMetadataTyped returns default for missing key.
   *
   * @covers ::getMetadataTyped
   */
  public function testGetMetadataTypedReturnsDefault(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getMetadataTyped')->with('missing', 'string', 'default_value')->willReturn('default_value');

    $this->mockGetEntry('currency', 'EUR', $entry);

    $value = $this->manager->getMetadataTyped('currency', 'EUR', 'missing', 'string', 'default_value');
    $this->assertEquals('default_value', $value);
  }

  /**
   * Tests getAvailableTypes returns all types.
   *
   * @covers ::getAvailableTypes
   */
  public function testGetAvailableTypesReturnsAllTypes(): void {
    $types = [
      'property_type' => $this->createMockType('property_type', 'Property Type'),
      'transaction_type' => $this->createMockType('transaction_type', 'Transaction Type'),
      'currency' => $this->createMockType('currency', 'Currency'),
    ];

    $storage = $this->createMock(ConfigEntityStorageInterface::class);
    $storage->method('loadMultiple')->willReturn($types);

    $this->entityTypeManager->method('getStorage')
      ->with('ps_dictionary_type')
      ->willReturn($storage);

    $this->cache->method('get')->willReturn(NULL);

    $result = $this->manager->getAvailableTypes();

    $this->assertIsArray($result);
    $this->assertArrayHasKey('property_type', $result);
    $this->assertArrayHasKey('transaction_type', $result);
    $this->assertArrayHasKey('currency', $result);
    $this->assertEquals('Property Type', $result['property_type']);
  }

  /**
   * Tests getAvailableTypes returns empty for no types.
   *
   * @covers ::getAvailableTypes
   */
  public function testGetAvailableTypesEmpty(): void {
    $storage = $this->createMock(ConfigEntityStorageInterface::class);
    $storage->method('loadMultiple')->willReturn([]);

    $this->entityTypeManager->method('getStorage')
      ->with('ps_dictionary_type')
      ->willReturn($storage);

    $this->cache->method('get')->willReturn(NULL);

    $result = $this->manager->getAvailableTypes();

    $this->assertEmpty($result);
  }

  /**
   * Tests sorting by weight.
   *
   * @covers ::getEntries
   */
  public function testEntriesSortedByWeight(): void {
    $entry1 = $this->createMockEntry('Z_CODE', TRUE, FALSE, 10);
    $entry1->method('getLabel')->willReturn('Z Code');
    $entry1->method('label')->willReturn('Z Code');

    $entry2 = $this->createMockEntry('A_CODE', TRUE, FALSE, 5);
    $entry2->method('getLabel')->willReturn('A Code');
    $entry2->method('label')->willReturn('A Code');

    $this->mockLoadEntries('property_type', [
      'property_type_z_code' => $entry1,
      'property_type_a_code' => $entry2,
    ]);

    $entries = $this->manager->getEntries('property_type');
    $codes = array_map(fn($e) => $e->getCode(), array_values($entries));

    // Entry with weight 5 should come before weight 10.
    $this->assertEquals('A_CODE', $codes[0]);
    $this->assertEquals('Z_CODE', $codes[1]);
  }

  /**
   * Tests sorting by label when weights equal.
   *
   * @covers ::getEntries
   */
  public function testEntriesSortedByLabelWhenWeightEqual(): void {
    $entry1 = $this->createMockEntry('ZEBRA', TRUE, FALSE, 0);
    $entry1->method('getLabel')->willReturn('Zebra');
    $entry1->method('label')->willReturn('Zebra');

    $entry2 = $this->createMockEntry('APPLE', TRUE, FALSE, 0);
    $entry2->method('getLabel')->willReturn('Apple');
    $entry2->method('label')->willReturn('Apple');

    $this->mockLoadEntries('property_type', [
      'property_type_zebra' => $entry1,
      'property_type_apple' => $entry2,
    ]);

    $entries = $this->manager->getEntries('property_type');
    $labels = array_map(fn($e) => $e->getLabel(), array_values($entries));

    // Apple should come before Zebra alphabetically.
    $this->assertEquals('Apple', $labels[0]);
    $this->assertEquals('Zebra', $labels[1]);
  }

  /**
   * Tests case-insensitive code matching.
   *
   * @covers ::getEntry
   */
  public function testCaseInsensitiveCodeMatching(): void {
    $entry = $this->createMockEntry('SALE', TRUE, FALSE);

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry,
    ]);

    // Should match even with different case.
    $result1 = $this->manager->getEntry('property_type', 'SALE');
    $result2 = $this->manager->getEntry('property_type', 'sale');
    $result3 = $this->manager->getEntry('property_type', 'Sale');

    $this->assertSame($result1, $result2);
    $this->assertSame($result2, $result3);
  }

  /**
   * Creates a mock dictionary entry.
   *
   * @param string $code
   *   The entry code.
   * @param bool $isActive
   *   Whether entry is active.
   * @param bool $isDeprecated
   *   Whether entry is deprecated.
   * @param int $weight
   *   The weight.
   *
   * @return \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|\PHPUnit\Framework\MockObject\MockObject
   *   The mock entry.
   */
  private function createMockEntry(
    string $code,
    bool $isActive = TRUE,
    bool $isDeprecated = FALSE,
    int $weight = 0,
  ): MockObject {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('isActive')->willReturn($isActive);
    $entry->method('isDeprecated')->willReturn($isDeprecated);
    $entry->method('getWeight')->willReturn($weight);
    $entry->method('getCode')->willReturn($code);

    return $entry;
  }

  /**
   * Creates a mock dictionary type.
   *
   * @param string $id
   *   The type ID.
   * @param string $label
   *   The type label.
   *
   * @return \Drupal\ps_dictionary\Entity\DictionaryTypeInterface|\PHPUnit\Framework\MockObject\MockObject
   *   The mock type.
   */
  private function createMockType(string $id, string $label): MockObject {
    $type = $this->createMock(DictionaryTypeInterface::class);
    $type->method('id')->willReturn($id);
    $type->method('label')->willReturn($label);

    return $type;
  }

  /**
   * Mocks loadEntries behavior.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface> $entries
   *   Entries to return.
   */
  private function mockLoadEntries(string $type, array $entries): void {
    $storage = $this->createMock(ConfigEntityStorageInterface::class);
    $storage->method('loadByProperties')
      ->with(['dictionary_type' => $type])
      ->willReturn($entries);

    $this->entityTypeManager->method('getStorage')
      ->with('ps_dictionary_entry')
      ->willReturn($storage);

    $this->cache->method('get')->willReturn(NULL);
  }

  /**
   * Mocks getEntry behavior via loadEntries.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param string $code
   *   Entry code.
   * @param \Drupal\ps_dictionary\Entity\DictionaryEntryInterface|null $entry
   *   Entry to return or NULL.
   */
  private function mockGetEntry(
    string $type,
    string $code,
    ?DictionaryEntryInterface $entry,
  ): void {
    $entries = $entry ? ["${type}_" . strtolower($code) => $entry] : [];
    $this->mockLoadEntries($type, $entries);
  }

}
