<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_surface\Unit;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_surface\Service\SurfaceProjectionManager;
use Drupal\Tests\UnitTestCase;

final class SurfaceProjectionManagerTest extends UnitTestCase {

  public function testRebuildForOfferProjectsQualifiedTotals(): void {
    $entity_type_manager = $this->createMock(EntityTypeManagerInterface::class);
    $node_storage = $this->createMock(EntityStorageInterface::class);
    $division_storage = $this->createMock(EntityStorageInterface::class);
      $query = $this->createMock(QueryInterface::class);
    $offer = $this->createMock(NodeInterface::class);

    $offer->method('bundle')->willReturn('offer');
    $offer->method('hasField')->willReturnCallback(static fn (string $field_name): bool => $field_name === 'field_surfaces');

    $offer->expects($this->once())
      ->method('set')
      ->with(
        'field_surfaces',
        [
          ['qualification' => 'TOTAL', 'value' => 130.5, 'unit_code' => 'M2'],
          ['qualification' => 'DISPO', 'value' => 80.25, 'unit_code' => 'M2'],
          ['qualification' => 'ETREF', 'value' => 15.75, 'unit_code' => 'M2'],
        ],
      );

    $offer->expects($this->once())
      ->method('save');

      // Simuler le champ field_divisions sur l'offre.
      $division1 = $this->buildDivisionEntity([
        ['qualification' => 'TOTAL', 'value' => '120.50'],
        ['qualification' => 'DISPO', 'value' => '80.25'],
      ]);
      $division2 = $this->buildDivisionEntity([
        ['qualification' => 'TOTAL', 'value' => '10.00'],
        ['qualification' => 'ETREF', 'value' => '15.75'],
      ]);
      $offer->method('get')->willReturnCallback(function (string $field_name) use ($division1, $division2) {
        $field = $this->createMock(FieldItemListInterface::class);
        if ($field_name === 'field_divisions') {
          $field->method('referencedEntities')->willReturn([$division1, $division2]);
        } else {
          $field->method('getValue')->willReturn([]);
        }
        return $field;
      });

    $node_storage->method('load')->with(101)->willReturn($offer);

    $query->method('accessCheck')->with(FALSE)->willReturnSelf();
      $query->method('condition')->with('field_divisions', 101)->willReturnSelf();
    $query->method('execute')->willReturn([11, 12]);

    $division_storage->method('getQuery')->willReturn($query);
    $division_storage->method('loadMultiple')->with([11, 12])->willReturn([
      $this->buildDivisionEntity([
        ['qualification' => 'TOTAL', 'value' => '120.50'],
        ['qualification' => 'DISPO', 'value' => '80.25'],
      ]),
      $this->buildDivisionEntity([
        ['qualification' => 'TOTAL', 'value' => '10.00'],
        ['qualification' => 'ETREF', 'value' => '15.75'],
      ]),
    ]);

    $entity_type_manager->method('getStorage')->willReturnCallback(static function (string $entity_type) use ($node_storage, $division_storage): EntityStorageInterface {
      return match ($entity_type) {
        'node' => $node_storage,
        'ps_surface_division' => $division_storage,
        default => throw new \InvalidArgumentException('Unexpected storage type.'),
      };
    });

    $manager = new SurfaceProjectionManager($entity_type_manager);
    $manager->rebuildForOffer(101);
  }

  private function buildDivisionEntity(array $surface_rows): object {
    return new class($surface_rows) {

      public function __construct(
        private readonly array $surfaceRows,
      ) {}

      public function hasField(string $field_name): bool {
        return $field_name === 'surfaces';
      }

      public function get(string $field_name): object {
        if ($field_name !== 'surfaces') {
          throw new \InvalidArgumentException('Unexpected field request');
        }

        return new class($this->surfaceRows) {

          public function __construct(
            private readonly array $rows,
          ) {}

          public function getValue(): array {
            return $this->rows;
          }

        };
      }

    };
  }

}
