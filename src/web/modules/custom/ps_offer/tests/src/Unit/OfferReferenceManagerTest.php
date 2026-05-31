<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Entity\OfferReferencePatternInterface;
use Drupal\ps_offer\Service\OfferReferenceGeneratorInterface;
use Drupal\ps_offer\Service\OfferReferenceManager;
use Drupal\ps_offer\Service\OfferReferencePatternResolver;
use Drupal\Tests\UnitTestCase;

final class OfferReferenceManagerTest extends UnitTestCase {

  public function testApplyReferenceModeKeepsManualValue(): void {
    $resolver = new OfferReferencePatternResolver($this->entityTypeManagerWithPatterns([]));
    $generator = $this->createMock(OfferReferenceGeneratorInterface::class);

    $generator->expects($this->never())->method('generate');

    $manager = new OfferReferenceManager($resolver, $generator, $this->createMock(EntityTypeManagerInterface::class));
    $node = $this->createMock(NodeInterface::class);

    $node->method('bundle')->willReturn('offer');
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_reference', 'field_reference_auto', 'field_operation_type', 'field_asset_type'], TRUE));

    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_reference_auto' => $this->fieldListWithValue('0'),
        'field_reference' => $this->fieldListWithValue('MANUAL-001'),
        'field_operation_type' => $this->fieldListWithValue('RENT'),
        'field_asset_type' => $this->fieldListWithValue('BUR'),
        default => $this->emptyFieldList(),
      };
    });

    $node->expects($this->never())->method('set');

    $manager->applyReferenceMode($node);
  }

  public function testApplyReferenceModeGeneratesWhenAuto(): void {
    $pattern = $this->createMock(OfferReferencePatternInterface::class);
    $pattern->method('status')->willReturn(TRUE);
    $pattern->method('getTargetBundles')->willReturn(['offer']);
    $pattern->method('getWeight')->willReturn(0);
    $pattern->method('requiresUniqueness')->willReturn(FALSE);

    $resolver = new OfferReferencePatternResolver($this->entityTypeManagerWithPatterns(['default' => $pattern]));

    $generator = $this->createMock(OfferReferenceGeneratorInterface::class);
    $generator->expects($this->once())->method('generate')->willReturn('OLBUR2600001');

    $manager = new OfferReferenceManager($resolver, $generator, $this->createMock(EntityTypeManagerInterface::class));
    $node = $this->createMock(NodeInterface::class);

    $node->method('bundle')->willReturn('offer');
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_reference', 'field_reference_auto', 'field_operation_type', 'field_asset_type'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_reference_auto' => $this->fieldListWithValue('1'),
        'field_reference' => $this->emptyFieldList(),
        'field_operation_type' => $this->fieldListWithValue('RENT'),
        'field_asset_type' => $this->fieldListWithValue('BUR'),
        default => $this->emptyFieldList(),
      };
    });

    $node->expects($this->once())->method('set')->with('field_reference', 'OLBUR2600001');

    $manager->applyReferenceMode($node);
  }

  public function testApplyReferenceModeSkipsCollidingReferenceWhenUniquenessIsRequired(): void {
    $pattern = $this->createMock(OfferReferencePatternInterface::class);
    $pattern->method('status')->willReturn(TRUE);
    $pattern->method('getTargetBundles')->willReturn(['offer']);
    $pattern->method('getWeight')->willReturn(0);
    $pattern->method('requiresUniqueness')->willReturn(TRUE);

    $resolver = new OfferReferencePatternResolver($this->entityTypeManagerWithPatterns(['default' => $pattern]));

    $generator = $this->createMock(OfferReferenceGeneratorInterface::class);
    $generator
      ->expects($this->exactly(2))
      ->method('generate')
      ->willReturnOnConsecutiveCalls('OLBUR2600001', 'OLBUR2600002');

    $query = $this->createMock(QueryInterface::class);
    $query->method('accessCheck')->willReturnSelf();
    $query->method('condition')->willReturnSelf();
    $query->method('range')->willReturnSelf();
    $query->method('count')->willReturnSelf();
    $query->method('execute')->willReturnOnConsecutiveCalls(1, 0);

    $node_storage = $this->createMock(ContentEntityStorageInterface::class);
    $node_storage->method('getQuery')->willReturn($query);

    $pattern_storage = $this->createMock(EntityStorageInterface::class);
    $pattern_storage->method('loadMultiple')->willReturn(['default' => $pattern]);

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager
      ->method('getStorage')
      ->willReturnCallback(static function (string $entity_type_id) use ($pattern_storage, $node_storage): EntityStorageInterface {
        return match ($entity_type_id) {
          'ps_offer_reference_pattern' => $pattern_storage,
          'node' => $node_storage,
          default => throw new \InvalidArgumentException('Unexpected entity type id: ' . $entity_type_id),
        };
      });

    $manager = new OfferReferenceManager($resolver, $generator, $entity_type_manager);

    $node = $this->createMock(NodeInterface::class);
    $node->method('bundle')->willReturn('offer');
    $node->method('id')->willReturn(NULL);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_reference', 'field_reference_auto', 'field_operation_type', 'field_asset_type'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_reference_auto' => $this->fieldListWithValue('1'),
        'field_reference' => $this->emptyFieldList(),
        'field_operation_type' => $this->fieldListWithValue('LOC'),
        'field_asset_type' => $this->fieldListWithValue('BUR'),
        default => $this->emptyFieldList(),
      };
    });

    $node->expects($this->once())->method('set')->with('field_reference', 'OLBUR2600002');

    $manager->applyReferenceMode($node);
  }

  private function fieldListWithValue(string $value): FieldItemListInterface {
    $item = $this->createMock(FieldItemInterface::class);
    $item->method('getValue')->willReturn(['value' => $value]);

    $list = $this->createMock(FieldItemListInterface::class);
    $list->method('isEmpty')->willReturn(FALSE);
    $list->method('first')->willReturn($item);

    return $list;
  }

  private function emptyFieldList(): FieldItemListInterface {
    $list = $this->createMock(FieldItemListInterface::class);
    $list->method('isEmpty')->willReturn(TRUE);
    $list->method('first')->willReturn(NULL);

    return $list;
  }

  private function entityTypeManagerWithPatterns(array $patterns): EntityTypeManagerInterface {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadMultiple')->willReturn($patterns);

    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $entity_type_manager
      ->method('getStorage')
      ->with('ps_offer_reference_pattern')
      ->willReturn($storage);

    return $entity_type_manager;
  }

}