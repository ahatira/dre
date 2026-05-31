<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_dictionary\Unit;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_dictionary\Entity\DictionaryEntryInterface;
use Drupal\ps_dictionary\Service\DictionaryResolver;
use Drupal\Tests\UnitTestCase;

final class DictionaryResolverTest extends UnitTestCase {

  public function testResolveLabelIsCaseInsensitive(): void {
    $entries = [
      $this->createEntry('BUR', 'Bureau', 10),
      $this->createEntry('COW', 'Coworking', 20),
    ];

    $resolver = $this->createResolverWithEntries($entries);

    self::assertSame('Bureau', $resolver->resolveLabel('asset_type', 'bur'));
    self::assertNull($resolver->resolveLabel('asset_type', 'UNKNOWN'));
  }

  public function testResolveCodeIsCaseInsensitive(): void {
    $entries = [
      $this->createEntry('VEN', 'Vente', 10),
      $this->createEntry('LOC', 'Location', 20),
    ];

    $resolver = $this->createResolverWithEntries($entries);

    self::assertSame('VEN', $resolver->resolveCode('operation_type', 'vente'));
    self::assertNull($resolver->resolveCode('operation_type', 'Achat'));
  }

  public function testAllReturnsSortedEntriesByWeight(): void {
    $entries = [
      $this->createEntry('USD', 'Dollar', 20),
      $this->createEntry('EUR', 'Euro', 5),
    ];

    $resolver = $this->createResolverWithEntries($entries);

    self::assertSame([
      [
        'code' => 'EUR',
        'label' => 'Euro',
        'weight' => 5,
      ],
      [
        'code' => 'USD',
        'label' => 'Dollar',
        'weight' => 20,
      ],
    ], $resolver->all('currency'));
  }

  public function testIsValidUsesResolvedLabel(): void {
    $entries = [
      $this->createEntry('EUR', 'Euro', 1),
    ];

    $resolver = $this->createResolverWithEntries($entries);

    self::assertTrue($resolver->isValid('currency', 'eur'));
    self::assertFalse($resolver->isValid('currency', 'usd'));
  }

  /**
   * @param array<int, \Drupal\ps_dictionary\Entity\DictionaryEntryInterface> $entries
   */
  private function createResolverWithEntries(array $entries): DictionaryResolver {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage
      ->method('loadByProperties')
      ->willReturnCallback(static function (array $properties) use ($entries): array {
        return isset($properties['type']) ? $entries : [];
      });

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager
      ->method('getStorage')
      ->with('ps_dictionary_entry')
      ->willReturn($storage);

    return new DictionaryResolver($entity_type_manager);
  }

  private function createEntry(string $code, string $label, int $weight): DictionaryEntryInterface {
    $entry = $this->createMock(DictionaryEntryInterface::class);
    $entry->method('getCode')->willReturn($code);
    $entry->method('label')->willReturn($label);
    $entry->method('getWeight')->willReturn($weight);
    return $entry;
  }

}
