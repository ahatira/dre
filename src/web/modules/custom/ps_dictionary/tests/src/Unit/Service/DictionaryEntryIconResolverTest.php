<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\ps_dictionary\Service\DictionaryEntryIconResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_dictionary\Service\DictionaryEntryIconResolver
 * @group ps_dictionary
 */
final class DictionaryEntryIconResolverTest extends UnitTestCase {

  /**
   * @covers ::normalizeStoredIcon
   */
  public function testNormalizeStoredIconFromLegacyCssClass(): void {
    $resolver = new DictionaryEntryIconResolver($this->createMock(EntityTypeManagerInterface::class));

    $this->assertSame(
      'bnp_custom:offices',
      $resolver->normalizeStoredIcon('ps-asset-icon--bur', ''),
    );
    $this->assertSame(
      'bnp_custom:offices',
      $resolver->normalizeStoredIcon('bnp_custom:offices', ''),
    );
  }

  /**
   * @covers ::getDefaultIconId
   */
  public function testGetDefaultIconIdForAssetType(): void {
    $resolver = new DictionaryEntryIconResolver($this->createMock(EntityTypeManagerInterface::class));

    $this->assertSame('bnp_custom:offices', $resolver->getDefaultIconId('asset_type', 'BUR'));
    $this->assertSame('bnp_custom:logistic-warehouses', $resolver->getDefaultIconId('asset_type', 'LOG'));
    $this->assertSame('', $resolver->getDefaultIconId('operation_type', 'LOC'));
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsUsesStoredIcon(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getIcon')->willReturn('bnp_custom:shops');
    $entry->method('getType')->willReturn('asset_type');
    $entry->method('getCode')->willReturn('COM');

    $resolver = new DictionaryEntryIconResolver($this->createMock(EntityTypeManagerInterface::class));
    $parts = $resolver->resolveParts($entry);

    $this->assertSame('bnp_custom:shops', $parts['full_id']);
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsFallsBackToAssetTypeDefault(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getIcon')->willReturn('');
    $entry->method('getType')->willReturn('asset_type');
    $entry->method('getCode')->willReturn('COW');

    $resolver = new DictionaryEntryIconResolver($this->createMock(EntityTypeManagerInterface::class));
    $parts = $resolver->resolveParts($entry);

    $this->assertSame('bnp_custom:coworking', $parts['full_id']);
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsUsesContextWhenEntryMissing(): void {
    $resolver = new DictionaryEntryIconResolver($this->createMock(EntityTypeManagerInterface::class));
    $parts = $resolver->resolveParts(NULL, [
      'type' => 'asset_type',
      'code' => 'TER',
    ]);

    $this->assertSame('bnp_custom:terrain', $parts['full_id']);

    $logParts = $resolver->resolveParts(NULL, [
      'type' => 'asset_type',
      'code' => 'LOG',
    ]);
    $this->assertSame('bnp_custom:logistic-warehouses', $logParts['full_id']);
  }

  /**
   * @covers ::resolveParts
   */
  public function testResolvePartsLoadsEntryById(): void {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getIcon')->willReturn('bnp_custom:offices');
    $entry->method('getType')->willReturn('asset_type');
    $entry->method('getCode')->willReturn('BUR');

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('load')->with('asset_type.bur')->willReturn($entry);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('ps_dictionary_entry')->willReturn($storage);

    $resolver = new DictionaryEntryIconResolver($entityTypeManager);
    $parts = $resolver->resolveParts('asset_type.bur');

    $this->assertSame('bnp_custom:offices', $parts['full_id']);
  }

}
