<?php

namespace Drupal\Tests\ps_feature\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\ps_feature\Service\FeatureBuilderStateBuilder;
use Drupal\Tests\UnitTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * Tests FeatureBuilderStateBuilder::buildFromItems().
 */
#[CoversClass(FeatureBuilderStateBuilder::class)]
#[Group('ps_feature')]
class FeatureBuilderStateBuilderTest extends UnitTestCase {

  protected FeatureBuilderStateBuilder $service;

  protected EntityStorageInterface $definitionStorage;

  protected EntityTypeManagerInterface $entityTypeManager;

  protected function setUp(): void {
    parent::setUp();
    $this->definitionStorage = $this->createMock(EntityStorageInterface::class);
    $this->entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->entityTypeManager
      ->method('getStorage')
      ->with('fb_feature_definition')
      ->willReturn($this->definitionStorage);
    $this->service = new FeatureBuilderStateBuilder($this->entityTypeManager);
  }

  /**
   * Creates a feature definition double.
   */
  protected function makeDefinition(string $group, string $label, string $type): object {
    return new class($group, $label, $type) {
      public function __construct(
        private readonly string $group,
        private readonly string $label,
        private readonly string $type,
      ) {
      }

      public function getGroup(): string {
        return $this->group;
      }

      public function label(): string {
        return $this->label;
      }

      public function getTypeDriver(): string {
        return $this->type;
      }
    };
  }

  /**
   * Creates a mock FieldItemListInterface that iterates over the given items.
   *
   * PHPUnit makes Traversable interface mocks implement \Iterator internally,
   * so we configure the Iterator protocol methods (current, next, valid, etc.)
   * rather than getIterator().
   *
   * @param object[] $data
   *   The item objects to yield during iteration.
   *
   * @return \Drupal\Core\Field\FieldItemListInterface
   */
  protected function makeItemList(array $data): FieldItemListInterface {
    $pos = 0;
    $mock = $this->createMock(FieldItemListInterface::class);
    $mock->method('current')->willReturnCallback(
      static function () use (&$data, &$pos) { return $data[$pos] ?? FALSE; }
    );
    $mock->method('next')->willReturnCallback(
      static function () use (&$pos) { $pos++; }
    );
    $mock->method('valid')->willReturnCallback(
      static function () use (&$data, &$pos) { return isset($data[$pos]); }
    );
    $mock->method('rewind')->willReturnCallback(
      static function () use (&$pos) { $pos = 0; }
    );
    $mock->method('key')->willReturnCallback(
      static function () use (&$pos) { return $pos; }
    );
    return $mock;
  }

  public function testEmptyItemsReturnsEmptyFeaturesArray(): void {
    $this->definitionStorage->expects($this->never())->method('load');
    $items = $this->makeItemList([]);

    $result = $this->service->buildFromItems($items);

    $this->assertSame(['features' => []], $result);
  }

  public function testValidItemsAreConvertedToFeaturesArray(): void {
    $item = new \stdClass();
    $item->feature_definition_id = 'surface_totale';
    $item->payload = json_encode(['value' => 85.5, 'unit' => 'm²']);

    $this->definitionStorage
      ->expects($this->once())
      ->method('load')
      ->with('surface_totale')
      ->willReturn($this->makeDefinition('surfaces', 'Surface totale', 'numeric'));

    $result = $this->service->buildFromItems($this->makeItemList([$item]));

    $this->assertCount(1, $result['features']);
    $feature = $result['features'][0];
    $this->assertSame('surface_totale', $feature['id']);
    $this->assertSame(85.5, $feature['payload']['value']);
    $this->assertSame('m²', $feature['payload']['unit']);
    $this->assertSame(0, $feature['delta']);
    $this->assertSame('surfaces', $feature['group']);
    $this->assertSame('Surface totale', $feature['label']);
    $this->assertSame('numeric', $feature['type']);
  }

  public function testMultipleItemsPreserveDeltaOrder(): void {
    $item0 = new \stdClass();
    $item0->feature_definition_id = 'surface_totale';
    $item0->payload = json_encode(['value' => 85.5, 'unit' => 'm²']);

    $item1 = new \stdClass();
    $item1->feature_definition_id = 'parking_inclus';
    $item1->payload = json_encode(['present' => TRUE]);

    $this->definitionStorage
      ->expects($this->exactly(2))
      ->method('load')
      ->willReturnMap([
        ['surface_totale', $this->makeDefinition('surfaces', 'Surface totale', 'numeric')],
        ['parking_inclus', $this->makeDefinition('amenities', 'Parking inclus', 'flag')],
      ]);

    $result = $this->service->buildFromItems($this->makeItemList([$item0, $item1]));

    $this->assertCount(2, $result['features']);
    $this->assertSame(0, $result['features'][0]['delta']);
    $this->assertSame(1, $result['features'][1]['delta']);
    $this->assertSame('surface_totale', $result['features'][0]['id']);
    $this->assertSame('parking_inclus', $result['features'][1]['id']);
  }

  public function testItemWithEmptyFeatureDefinitionIdIsSkipped(): void {
    $this->definitionStorage->expects($this->never())->method('load');
    $item = new \stdClass();
    $item->feature_definition_id = '';
    $item->payload = json_encode(['value' => 100]);

    $result = $this->service->buildFromItems($this->makeItemList([$item]));

    $this->assertSame([], $result['features']);
  }

  public function testItemWithNullFeatureDefinitionIdIsSkipped(): void {
    $this->definitionStorage->expects($this->never())->method('load');
    $item = new \stdClass();
    $item->feature_definition_id = NULL;
    $item->payload = json_encode(['value' => 100]);

    $result = $this->service->buildFromItems($this->makeItemList([$item]));

    $this->assertSame([], $result['features']);
  }

  public function testInvalidJsonPayloadFallsBackToEmptyArray(): void {
    $item = new \stdClass();
    $item->feature_definition_id = 'surface_totale';
    $item->payload = 'not-valid-json{{{';

    $this->definitionStorage
      ->expects($this->once())
      ->method('load')
      ->with('surface_totale')
      ->willReturn(NULL);

    $result = $this->service->buildFromItems($this->makeItemList([$item]));

    $this->assertCount(1, $result['features']);
    $this->assertSame([], $result['features'][0]['payload']);
    $this->assertSame('', $result['features'][0]['group']);
    $this->assertSame('surface_totale', $result['features'][0]['label']);
    $this->assertSame('', $result['features'][0]['type']);
  }

  public function testNonArrayJsonPayloadFallsBackToEmptyArray(): void {
    $item = new \stdClass();
    $item->feature_definition_id = 'surface_totale';
    $item->payload = '"just a string"';

    $this->definitionStorage
      ->expects($this->once())
      ->method('load')
      ->with('surface_totale')
      ->willReturn(NULL);

    $result = $this->service->buildFromItems($this->makeItemList([$item]));

    $this->assertCount(1, $result['features']);
    $this->assertSame([], $result['features'][0]['payload']);
  }


  public function testEmptyPayloadFieldFallsBackToEmptyArray(): void {
    $item = new \stdClass();
    $item->feature_definition_id = 'parking_inclus';
    $item->payload = '';

    $this->definitionStorage
      ->expects($this->once())
      ->method('load')
      ->with('parking_inclus')
      ->willReturn(NULL);

    $result = $this->service->buildFromItems($this->makeItemList([$item]));

    $this->assertCount(1, $result['features']);
    $this->assertSame([], $result['features'][0]['payload']);
  }

  public function testMixedValidAndInvalidItemsOnlyKeepsValid(): void {
    $good = new \stdClass();
    $good->feature_definition_id = 'surface_totale';
    $good->payload = json_encode(['value' => 50]);

    $bad = new \stdClass();
    $bad->feature_definition_id = '';
    $bad->payload = json_encode(['value' => 999]);

    $this->definitionStorage
      ->expects($this->once())
      ->method('load')
      ->with('surface_totale')
      ->willReturn(NULL);

    $result = $this->service->buildFromItems($this->makeItemList([$good, $bad]));

    $this->assertCount(1, $result['features']);
    $this->assertSame('surface_totale', $result['features'][0]['id']);
  }

  public function testReturnStructureAlwaysHasFeaturesKey(): void {
    $this->definitionStorage->expects($this->never())->method('load');
    $result = $this->service->buildFromItems($this->makeItemList([]));

    $this->assertArrayHasKey('features', $result);
    $this->assertIsArray($result['features']);
  }

}
