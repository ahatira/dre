<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_surface\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_surface\Plugin\Field\FieldFormatter\SurfaceContextualFormatter;
use Drupal\Tests\UnitTestCase;

/**
 * Unit tests for SurfaceContextualFormatter.
 *
 * @group ps_surface
 */
final class SurfaceContextualFormatterTest extends UnitTestCase {

  protected function setUp(): void {
    parent::setUp();

    // Minimal service container for string translation.
    $translation = $this->createMock(TranslationInterface::class);
    $translation->method('translate')->willReturnCallback(
      static fn (string $string): string => $string,
    );
    $translation->method('translateString')->willReturnCallback(
      static fn (object $string): string => (string) $string,
    );

    $container = new ContainerBuilder();
    $container->set('string_translation', $translation);
    \Drupal::setContainer($container);
  }

  /**
   * Builds a SurfaceContextualFormatter instance with a mocked field definition.
   */
  private function buildFormatter(): SurfaceContextualFormatter {
    $field_storage = $this->createMock(FieldStorageDefinitionInterface::class);
    $field_storage->method('getMainPropertyName')->willReturn('value');

    $field_definition = $this->createMock(FieldDefinitionInterface::class);
    $field_definition->method('getFieldStorageDefinition')->willReturn($field_storage);

    $formatter = new SurfaceContextualFormatter(
      'ps_surface_contextual',
      [],
      $field_definition,
      [],
      'above',
      'default',
      [],
    );
    $formatter->setStringTranslation($this->getStringTranslationStub());

    return $formatter;
  }

  /**
   * Builds a mock FieldItemListInterface from an array of item data and a node.
   *
   * @param array<int, array<string, mixed>> $rows
   */
  private function buildItems(array $rows, NodeInterface $node): FieldItemListInterface {
    $item_objects = array_map(static fn(array $row): object => (object) $row, $rows);

    // FieldItemList (concrete class) has getIterator() as a real method — mockable via onlyMethods().
    $items = $this->getMockBuilder(FieldItemList::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['getEntity', 'getIterator'])
      ->getMock();

    $items->method('getEntity')->willReturn($node);
    $items->method('getIterator')->willReturn(new \ArrayIterator($item_objects));

    return $items;
  }

  /**
   * Builds a mock offer node with given field values.
   *
   * @param array<string, mixed> $field_values
   */
  private function buildOfferNode(array $field_values): NodeInterface {
    $node = $this->createMock(NodeInterface::class);
    $node->method('bundle')->willReturn('offer');

    $node->method('hasField')->willReturnCallback(
      static fn (string $name): bool => array_key_exists($name, $field_values),
    );

    $node->method('get')->willReturnCallback(function (string $name) use ($field_values): object {
      $value = $field_values[$name] ?? NULL;
      return new class($value) {
        public function __construct(public readonly mixed $value) {}
      };
    });

    return $node;
  }

  /**
   * COW asset_type → empty output (capacity-driven).
   */
  public function testCowReturnsEmpty(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'COW', 'field_divisible' => FALSE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 800.0, 'unit_code' => 'M2'],
    ], $node);

    $result = $formatter->viewElements($items, 'fr');
    $this->assertEmpty($result);
  }

  /**
   * Non-divisible BUR → "{TOTAL} m²".
   */
  public function testBurNotDivisibleShowsTotal(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => FALSE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 1200.5, 'unit_code' => 'M2'],
      ['qualification' => 'DISPO', 'value' => 800.0, 'unit_code' => 'M2'],
    ], $node);

    $result = $formatter->viewElements($items, 'fr');
    $this->assertNotEmpty($result);
    // formatValue() uses narrow no-break space (U+202F) as thousands separator.
    $this->assertStringContainsString("1\u{202F}200.50", $result[0]['#markup'] ?? '');
    $this->assertStringContainsString('m²', $result[0]['#markup'] ?? '');
    $this->assertStringNotContainsString('Available', $result[0]['#markup'] ?? '');
  }

  /**
   * Divisible BUR with DISPO → "{TOTAL} m² · Available: {DISPO} m²".
   */
  public function testBurDivisibleShowsTotalAndDispo(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => TRUE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 2000.0, 'unit_code' => 'M2'],
      ['qualification' => 'DISPO', 'value' => 1500.0, 'unit_code' => 'M2'],
    ], $node);

    $result = $formatter->viewElements($items, 'fr');
    $this->assertNotEmpty($result);
    $markup = $result[0]['#markup'] ?? '';
    // formatValue() uses narrow no-break space (U+202F) as thousands separator.
    $this->assertStringContainsString("2\u{202F}000", $markup);
    $this->assertStringContainsString('Available', $markup);
    $this->assertStringContainsString("1\u{202F}500", $markup);
  }

  /**
   * TER asset_type (land) → always shows TOTAL only, even if divisible.
   */
  public function testTerLandShowsTotalOnly(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'TER', 'field_divisible' => TRUE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 5.5, 'unit_code' => 'HA'],
      ['qualification' => 'DISPO', 'value' => 3.0, 'unit_code' => 'HA'],
    ], $node);

    $result = $formatter->viewElements($items, 'fr');
    $this->assertNotEmpty($result);
    $markup = $result[0]['#markup'] ?? '';
    $this->assertStringContainsString('5.50', $markup);
    $this->assertStringContainsString('ha', $markup);
    $this->assertStringNotContainsString('Available', $markup);
  }

  /**
   * No TOTAL item → empty output.
   */
  public function testMissingTotalReturnsEmpty(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => FALSE]);
    $items = $this->buildItems([
      ['qualification' => 'DISPO', 'value' => 500.0, 'unit_code' => 'M2'],
    ], $node);

    $result = $formatter->viewElements($items, 'fr');
    $this->assertEmpty($result);
  }

  /**
   * Divisible but DISPO is zero → no "Available" suffix.
   */
  public function testDivisibleWithZeroDispoHidesAvailableSuffix(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => TRUE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 1000.0, 'unit_code' => 'M2'],
      ['qualification' => 'DISPO', 'value' => 0.0, 'unit_code' => 'M2'],
    ], $node);

    $result = $formatter->viewElements($items, 'fr');
    $this->assertNotEmpty($result);
    $this->assertStringNotContainsString('Available', $result[0]['#markup'] ?? '');
  }

}
