<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit;

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
 * Unit tests for DictionaryManager service.
 *
 * @coversDefaultClass \Drupal\ps_dictionary\Service\DictionaryManager
 * @group ps_dictionary
 */
class DictionaryManagerTest extends UnitTestCase {

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
   * Tests isValid returns TRUE for active entry.
   *
   * @covers ::isValid
   */
  public function testIsValidWithActiveEntry(): void {
    $entry = $this->createMockEntry('SALE', TRUE, FALSE);

    $this->mockGetEntry('property_type', 'SALE', $entry);

    $result = $this->manager->isValid('property_type', 'SALE');
    $this->assertTrue($result);
  }

  /**
   * Tests isValid returns FALSE for inactive entry.
   *
   * @covers ::isValid
   */
  public function testIsValidWithInactiveEntry(): void {
    $entry = $this->createMockEntry('SALE', FALSE, FALSE);

    $this->mockGetEntry('property_type', 'SALE', $entry);

    $result = $this->manager->isValid('property_type', 'SALE');
    $this->assertFalse($result);
  }

  /**
   * Tests isValid returns FALSE for non-existent entry.
   *
   * @covers ::isValid
   */
  public function testIsValidWithNonExistentEntry(): void {
    $this->mockGetEntry('property_type', 'NONEXISTENT', NULL);

    $result = $this->manager->isValid('property_type', 'NONEXISTENT');
    $this->assertFalse($result);
  }

  /**
   * Tests getLabel returns label for active entry.
   *
   * @covers ::getLabel
   */
  public function testGetLabelWithActiveEntry(): void {
    $entry = $this->createMockEntry('SALE', TRUE, FALSE);
    $entry->method('getLabel')->willReturn('For Sale');

    $this->mockGetEntry('property_type', 'SALE', $entry);

    $label = $this->manager->getLabel('property_type', 'SALE');
    $this->assertEquals('For Sale', $label);
  }

  /**
   * Tests getLabel returns NULL for inactive entry.
   *
   * @covers ::getLabel
   */
  public function testGetLabelWithInactiveEntry(): void {
    $entry = $this->createMockEntry('SALE', FALSE, FALSE);

    $this->mockGetEntry('property_type', 'SALE', $entry);

    $label = $this->manager->getLabel('property_type', 'SALE');
    $this->assertNull($label);
  }

  /**
   * Tests getLabel returns NULL for non-existent entry.
   *
   * @covers ::getLabel
   */
  public function testGetLabelWithNonExistentEntry(): void {
    $this->mockGetEntry('property_type', 'NONEXISTENT', NULL);

    $label = $this->manager->getLabel('property_type', 'NONEXISTENT');
    $this->assertNull($label);
  }

  /**
   * Tests getOptions returns correct format.
   *
   * @covers ::getOptions
   */
  public function testGetOptionsReturnsArrayFormat(): void {
    $entry1 = $this->createMockEntry('SALE', TRUE, FALSE, 0);
    $entry1->method('getLabel')->willReturn('For Sale');
    $entry1->method('label')->willReturn('For Sale');
    $entry1->method('getCode')->willReturn('SALE');

    $entry2 = $this->createMockEntry('RENT', TRUE, FALSE, 1);
    $entry2->method('getLabel')->willReturn('For Rent');
    $entry2->method('label')->willReturn('For Rent');
    $entry2->method('getCode')->willReturn('RENT');

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry1,
      'property_type_rent' => $entry2,
    ]);

    $options = $this->manager->getOptions('property_type');

    $this->assertIsArray($options);
    $this->assertArrayHasKey('SALE', $options);
    $this->assertArrayHasKey('RENT', $options);
    $this->assertEquals('For Sale', $options['SALE']);
    $this->assertEquals('For Rent', $options['RENT']);
  }

  /**
   * Tests getOptions excludes inactive entries.
   *
   * @covers ::getOptions
   */
  public function testGetOptionsExcludesInactiveByDefault(): void {
    $entry1 = $this->createMockEntry('SALE', TRUE, FALSE);
    $entry1->method('getLabel')->willReturn('For Sale');
    $entry1->method('label')->willReturn('For Sale');
    $entry1->method('getCode')->willReturn('SALE');

    $entry2 = $this->createMockEntry('INACTIVE', FALSE, FALSE);
    $entry2->method('getLabel')->willReturn('Inactive');
    $entry2->method('label')->willReturn('Inactive');
    $entry2->method('getCode')->willReturn('INACTIVE');

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry1,
      'property_type_inactive' => $entry2,
    ]);

    $options = $this->manager->getOptions('property_type', TRUE);

    $this->assertArrayHasKey('SALE', $options);
    $this->assertArrayNotHasKey('INACTIVE', $options);
  }

  /**
   * Tests getOptions includes inactive when specified.
   *
   * @covers ::getOptions
   */
  public function testGetOptionsIncludesInactiveWhenSpecified(): void {
    $entry1 = $this->createMockEntry('SALE', TRUE, FALSE);
    $entry1->method('getLabel')->willReturn('For Sale');
    $entry1->method('label')->willReturn('For Sale');
    $entry1->method('getCode')->willReturn('SALE');

    $entry2 = $this->createMockEntry('INACTIVE', FALSE, FALSE);
    $entry2->method('getLabel')->willReturn('Inactive');
    $entry2->method('label')->willReturn('Inactive');
    $entry2->method('getCode')->willReturn('INACTIVE');

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry1,
      'property_type_inactive' => $entry2,
    ]);

    $options = $this->manager->getOptions('property_type', FALSE);

    $this->assertArrayHasKey('SALE', $options);
    $this->assertArrayHasKey('INACTIVE', $options);
  }

  /**
   * Tests getEntry with valid entry.
   *
   * @covers ::getEntry
   */
  public function testGetEntryWithValidCode(): void {
    $entry = $this->createMockEntry('SALE', TRUE, FALSE);

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry,
    ]);

    $result = $this->manager->getEntry('property_type', 'SALE');
    $this->assertSame($entry, $result);
  }

  /**
   * Tests getEntry returns NULL for non-existent code.
   *
   * @covers ::getEntry
   */
  public function testGetEntryWithInvalidCode(): void {
    $this->mockLoadEntries('property_type', []);

    $result = $this->manager->getEntry('property_type', 'NONEXISTENT');
    $this->assertNull($result);
  }

  /**
   * Tests getEntries returns all entries.
   *
   * @covers ::getEntries
   */
  public function testGetEntriesReturnsAllEntries(): void {
    $entry1 = $this->createMockEntry('SALE', TRUE, FALSE, 0);
    $entry1->method('getLabel')->willReturn('For Sale');
    $entry1->method('label')->willReturn('For Sale');

    $entry2 = $this->createMockEntry('RENT', TRUE, FALSE, 1);
    $entry2->method('getLabel')->willReturn('For Rent');
    $entry2->method('label')->willReturn('For Rent');

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry1,
      'property_type_rent' => $entry2,
    ]);

    $entries = $this->manager->getEntries('property_type');

    $this->assertCount(2, $entries);
  }

  /**
   * Tests isDeprecated returns TRUE for deprecated entry.
   *
   * @covers ::isDeprecated
   */
  public function testIsDeprecatedWithDeprecatedEntry(): void {
    $entry = $this->createMockEntry('OLD', TRUE, TRUE);

    $this->mockGetEntry('property_type', 'OLD', $entry);

    $result = $this->manager->isDeprecated('property_type', 'OLD');
    $this->assertTrue($result);
  }

  /**
   * Tests isDeprecated returns FALSE for non-deprecated entry.
   *
   * @covers ::isDeprecated
   */
  public function testIsDeprecatedWithNonDeprecatedEntry(): void {
    $entry = $this->createMockEntry('SALE', TRUE, FALSE);

    $this->mockGetEntry('property_type', 'SALE', $entry);

    $result = $this->manager->isDeprecated('property_type', 'SALE');
    $this->assertFalse($result);
  }

  /**
   * Tests isDeprecated returns FALSE for non-existent entry.
   *
   * @covers ::isDeprecated
   */
  public function testIsDeprecatedWithNonExistentEntry(): void {
    $this->mockGetEntry('property_type', 'NONEXISTENT', NULL);

    $result = $this->manager->isDeprecated('property_type', 'NONEXISTENT');
    $this->assertFalse($result);
  }

  /**
   * Tests getMetadata returns metadata array.
   *
   * @covers ::getMetadata
   */
  public function testGetMetadataReturnsMetadata(): void {
    $metadata = ['symbol' => '€', 'decimal_places' => 2];
    $entry = $this->createMockEntry('EUR', TRUE, FALSE);
    $entry->method('getMetadata')->willReturn($metadata);

    // Setup both storages with callback to avoid mock conflicts.
    $entries = ['currency_eur' => $entry];
    $this->setupStorageCallback($entries);

    $result = $this->manager->getMetadata('currency', 'EUR');
    $this->assertEquals($metadata, $result);
  }

  /**
   * Tests getMetadata returns empty array for non-existent entry.
   *
   * @covers ::getMetadata
   */
  public function testGetMetadataWithNonExistentEntry(): void {
    $this->setupStorageCallback([]);

    $result = $this->manager->getMetadata('currency', 'NONEXISTENT');
    $this->assertEmpty($result);
  }

  /**
   * Tests clearCache clears all caches.
   *
   * @covers ::clearCache
   */
  public function testClearCacheWithoutType(): void {
    $this->cache->expects($this->once())->method('deleteAll');
    $this->logger->expects($this->once())->method('info')
      ->with('Cleared all dictionary caches');

    $this->manager->clearCache();
  }

  /**
   * Tests clearCache clears specific type cache.
   *
   * @covers ::clearCache
   */
  public function testClearCacheWithType(): void {
    $this->cache->expects($this->once())->method('delete')
      ->with('ps_dictionary:entries:property_type');
    $this->logger->expects($this->once())->method('info');

    $this->manager->clearCache('property_type');
  }

  /**
   * Tests that caching works (runtime cache).
   *
   * @covers ::getEntry
   */
  public function testRuntimeCaching(): void {
    $entry = $this->createMockEntry('SALE', TRUE, FALSE);

    $this->mockLoadEntries('property_type', [
      'property_type_sale' => $entry,
    ]);

    // First call.
    $result1 = $this->manager->getEntry('property_type', 'SALE');
    // Second call should use runtime cache.
    $result2 = $this->manager->getEntry('property_type', 'SALE');

    $this->assertSame($result1, $result2);
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
   * Mocks loadEntries behavior.
   *
   * @param string $type
   *   Dictionary type ID.
   * @param array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface> $entries
   *   Entries to return.
   */
  private function mockLoadEntries(string $type, array $entries): void {
    $entryStorage = $this->createMock(ConfigEntityStorageInterface::class);
    $entryStorage->method('loadByProperties')
      ->with(['dictionary_type' => $type])
      ->willReturn($entries);

    // Use a callback to return correct storage based on storage type.
    $this->entityTypeManager->method('getStorage')
      ->willReturnCallback(function ($storageType) use ($entryStorage) {
        if ($storageType === 'ps_dictionary_entry') {
          return $entryStorage;
        }
        // For ps_dictionary_type, return a default mock.
        return $this->createMock(ConfigEntityStorageInterface::class);
      });

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

  /**
   * Setup unified storage callback for both entry and type storages.
   *
   * @param array<string, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface> $entries
   *   Entries to return.
   */
  private function setupStorageCallback(array $entries): void {
    $this->entityTypeManager->method('getStorage')
      ->willReturnCallback(function ($storageType) use ($entries) {
        $storage = $this->createMock(ConfigEntityStorageInterface::class);

        if ($storageType === 'ps_dictionary_entry') {
          $storage->method('loadByProperties')
            ->willReturnCallback(function ($properties) use ($entries) {
              if (isset($properties['dictionary_type'])) {
                return $entries;
              }
              return [];
            });
        }
        elseif ($storageType === 'ps_dictionary_type') {
          // Return NULL for dictionary type (skip schema parsing).
          $storage->method('load')->willReturn(NULL);
        }

        return $storage;
      });

    $this->cache->method('get')->willReturn(NULL);
  }

}
