<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_search\Unit\Service;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\ps_search\Service\LocationSearchFilter;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_search\Service\LocationSearchFilter
 * @group ps_search
 */
final class LocationSearchFilterTest extends UnitTestCase {

  /**
   * @covers ::extractTokens
   */
  public function testExtractTokensDeduplicatesAndLimits(): void {
    $filter = new LocationSearchFilter(
      $this->createMock(Connection::class),
      $this->createDictionaryResolver(),
    );

    $tokens = $filter->extractTokens('Paris, Lyon, Paris, Nancy');

    $this->assertSame(['Paris', 'Lyon', 'Nancy'], $tokens);
  }

  /**
   * @covers ::extractTokens
   */
  public function testExtractTokensFromArray(): void {
    $filter = new LocationSearchFilter(
      $this->createMock(Connection::class),
      $this->createDictionaryResolver(),
    );

    $tokens = $filter->extractTokens(['75015', 'Nancy']);

    $this->assertSame(['75015', 'Nancy'], $tokens);
  }

  /**
   * @covers ::isDepartmentCode
   */
  public function testIsDepartmentCode(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getCode')->willReturn('75');
    $entry->method('label')->willReturn('Paris');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->willReturn([$entry]);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('ps_dictionary_entry')->willReturn($storage);

    $filter = new LocationSearchFilter(
      $this->createMock(Connection::class),
      new DictionaryResolver($entityTypeManager),
    );

    $this->assertTrue($filter->isDepartmentCode('75'));
    $this->assertFalse($filter->isDepartmentCode('750'));
    $this->assertFalse($filter->isDepartmentCode('Paris'));
  }

  /**
   * Builds a dictionary resolver backed by an empty entry storage.
   */
  private function createDictionaryResolver(): DictionaryResolver {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->willReturn([]);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('ps_dictionary_entry')->willReturn($storage);

    return new DictionaryResolver($entityTypeManager);
  }

}
