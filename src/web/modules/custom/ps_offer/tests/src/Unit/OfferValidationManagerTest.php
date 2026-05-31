<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Core\Entity\ContentEntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferValidationManager;
use Drupal\Tests\UnitTestCase;

final class OfferValidationManagerTest extends UnitTestCase {

  public function testInvalidBudgetThrowsExceptionWhenPublished(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addError')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Price value must be greater than 0 when a price period is set.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(TRUE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->fieldListWithValue('MONTH'),
        'field_budget_value' => $this->fieldListWithValue('0'),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));

    $this->expectException(EntityStorageException::class);
    $this->expectExceptionMessage('Price value must be greater than 0 when a price period is set.');

    $manager->apply($node);
  }

  public function testInvalidBudgetAddsWarningWhenDraft(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addWarning')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Price value must be greater than 0 when a price period is set.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(FALSE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->fieldListWithValue('MONTH'),
        'field_budget_value' => $this->fieldListWithValue('0'),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));
    $manager->apply($node);
  }

  public function testPublishedWithoutPrimaryAgentIsUnpublishedWithWarning(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addWarning')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'The offer has been saved as a draft because no primary agent is set.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(TRUE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_primary_agent'], TRUE));
    $node->expects($this->once())->method('setUnpublished');
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_primary_agent' => $this->emptyFieldList(),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));
    $manager->apply($node);
  }

  public function testSeatBasedCapacityThrowsExceptionWhenPublishedWithoutTotal(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addError')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Capacity total must be greater than 0 for seat-based offers.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(TRUE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_primary_agent', 'field_capacity_mode', 'field_capacity_total', 'field_capacity_available', 'field_budget_unit'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_primary_agent' => $this->fieldListWithValue('42'),
        'field_capacity_mode' => $this->fieldListWithValue('SEAT_BASED'),
        'field_capacity_total' => $this->emptyFieldList(),
        'field_capacity_available' => $this->emptyFieldList(),
        'field_budget_unit' => $this->emptyFieldList(),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));

    $this->expectException(EntityStorageException::class);
    $this->expectExceptionMessage('Capacity total must be greater than 0 for seat-based offers.');

    $manager->apply($node);
  }

  public function testSeatBasedCapacityAddsWarningWhenDraftWithoutTotal(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addWarning')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Capacity total must be greater than 0 for seat-based offers.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(FALSE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_capacity_mode', 'field_capacity_total', 'field_capacity_available', 'field_budget_unit'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_capacity_mode' => $this->fieldListWithValue('SEAT_BASED'),
        'field_capacity_total' => $this->emptyFieldList(),
        'field_capacity_available' => $this->emptyFieldList(),
        'field_budget_unit' => $this->emptyFieldList(),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));
    $manager->apply($node);
  }

  public function testCapacityAvailableGreaterThanTotalThrowsException(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addError')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Capacity available must be lower than or equal to capacity total.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(TRUE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_primary_agent', 'field_capacity_mode', 'field_capacity_total', 'field_capacity_available', 'field_budget_unit'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_primary_agent' => $this->fieldListWithValue('42'),
        'field_capacity_mode' => $this->fieldListWithValue('HYBRID'),
        'field_capacity_total' => $this->fieldListWithValue('10'),
        'field_capacity_available' => $this->fieldListWithValue('12'),
        'field_budget_unit' => $this->emptyFieldList(),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));

    $this->expectException(EntityStorageException::class);
    $this->expectExceptionMessage('Capacity available must be lower than or equal to capacity total.');

    $manager->apply($node);
  }

  public function testPerPosteBudgetRequiresCapacityTotalWhenPublished(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addError')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Capacity total must be greater than 0 when budget unit is PER_POSTE.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(TRUE);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_primary_agent', 'field_capacity_mode', 'field_capacity_total', 'field_capacity_available', 'field_budget_unit'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_primary_agent' => $this->fieldListWithValue('42'),
        'field_capacity_mode' => $this->fieldListWithValue('SURFACE_BASED'),
        'field_capacity_total' => $this->emptyFieldList(),
        'field_capacity_available' => $this->emptyFieldList(),
        'field_budget_unit' => $this->fieldListWithValue('PER_POSTE'),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));

    $this->expectException(EntityStorageException::class);
    $this->expectExceptionMessage('Capacity total must be greater than 0 when budget unit is PER_POSTE.');

    $manager->apply($node);
  }

  public function testManualDuplicateReferenceThrowsException(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger
      ->expects($this->once())
      ->method('addError')
      ->with($this->callback(static fn ($message): bool => $message instanceof TranslatableMarkup && $message->getUntranslatedString() === 'Manual reference value is already used by another offer.'));

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(FALSE);
    $node->method('id')->willReturn(1234);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_capacity_mode', 'field_capacity_total', 'field_capacity_available', 'field_budget_unit', 'field_primary_agent', 'field_reference', 'field_reference_auto'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_capacity_mode' => $this->emptyFieldList(),
        'field_capacity_total' => $this->emptyFieldList(),
        'field_capacity_available' => $this->emptyFieldList(),
        'field_budget_unit' => $this->emptyFieldList(),
        'field_primary_agent' => $this->emptyFieldList(),
        'field_reference_auto' => $this->fieldListWithValue('0'),
        'field_reference' => $this->fieldListWithValue('REF-MANUAL-DUP-001'),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(1));

    $this->expectException(EntityStorageException::class);
    $this->expectExceptionMessage('Manual reference value is already used by another offer.');

    $manager->apply($node);
  }

  public function testManualSelfReferenceOnSameNodeIsAllowed(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger->expects($this->never())->method('addError');

    $node->method('bundle')->willReturn('offer');
    $node->method('isPublished')->willReturn(FALSE);
    $node->method('id')->willReturn(1234);
    $node->method('hasField')->willReturnCallback(static fn (string $field): bool => in_array($field, ['field_budget_period', 'field_budget_value', 'field_capacity_mode', 'field_capacity_total', 'field_capacity_available', 'field_budget_unit', 'field_primary_agent', 'field_reference', 'field_reference_auto'], TRUE));
    $node->method('get')->willReturnCallback(function (string $field): FieldItemListInterface {
      return match ($field) {
        'field_budget_period' => $this->emptyFieldList(),
        'field_budget_value' => $this->emptyFieldList(),
        'field_capacity_mode' => $this->emptyFieldList(),
        'field_capacity_total' => $this->emptyFieldList(),
        'field_capacity_available' => $this->emptyFieldList(),
        'field_budget_unit' => $this->emptyFieldList(),
        'field_primary_agent' => $this->emptyFieldList(),
        'field_reference_auto' => $this->fieldListWithValue('0'),
        'field_reference' => $this->fieldListWithValue('REF-MANUAL-SELF-001'),
        default => throw new \InvalidArgumentException('Unexpected field requested in test.'),
      };
    });

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));
    $manager->apply($node);
  }

  public function testNonOfferNodeIsIgnored(): void {
    $messenger = $this->createMock(MessengerInterface::class);
    $node = $this->createMock(NodeInterface::class);

    $messenger->expects($this->never())->method('addError');
    $messenger->expects($this->never())->method('addWarning');
    $node->method('bundle')->willReturn('article');
    $node->expects($this->never())->method('hasField');
    $node->expects($this->never())->method('get');

    $manager = new OfferValidationManager($messenger, $this->entityTypeManagerWithManualReferenceDuplicateCount(0));
    $manager->apply($node);
  }

  private function entityTypeManagerWithManualReferenceDuplicateCount(int $duplicateCount): EntityTypeManagerInterface {
    $query = $this->createMock(QueryInterface::class);
    $query->method('accessCheck')->willReturnSelf();
    $query->method('condition')->willReturnSelf();
    $query->method('range')->willReturnSelf();
    $query->method('count')->willReturnSelf();
    $query->method('execute')->willReturn($duplicateCount);

    $storage = $this->createMock(ContentEntityStorageInterface::class);
    $storage->method('getQuery')->willReturn($query);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('node')->willReturn($storage);

    return $entityTypeManager;
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

}
