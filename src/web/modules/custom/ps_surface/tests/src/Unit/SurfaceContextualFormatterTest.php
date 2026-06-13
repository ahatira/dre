<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_surface\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemList;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\OfferContextResolverInterface;
use Drupal\ps_offer\Service\OfferSurfaceKpiBuilder;
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

    $translation = $this->createMock(TranslationInterface::class);
    $translation->method('translate')->willReturnCallback(
      static fn (string $string): string => $string,
    );
    $translation->method('translateString')->willReturnCallback(
      static fn (object $string): string => (string) $string,
    );

    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(static function (string $key): mixed {
      return match ($key) {
        'surface_divisible_template' => 'Divisible from @surface',
        'surface_capacity_unit' => 'seats',
        default => NULL,
      };
    });

    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config_factory->method('get')->with('ps_offer.settings')->willReturn($config);

    $contextResolver = $this->createMock(OfferContextResolverInterface::class);
    $contextResolver->method('isCapacityDriven')->willReturnCallback(
      static function (NodeInterface $node): bool {
        return $node->hasField('field_asset_type')
          && (string) $node->get('field_asset_type')->value === 'COW';
      },
    );
    $contextResolver->method('isTabVisible')->willReturn(TRUE);

    $renderer = $this->createMock(RendererInterface::class);
    $renderer->method('render')->willReturnCallback(function (array &$element) use ($renderer): string {
      if (($element['#type'] ?? '') === 'html_tag') {
        $value = (string) ($element['#value'] ?? '');
        $class = implode(' ', $element['#attributes']['class'] ?? []);
        return $class !== '' ? '<span class="' . $class . '">' . $value . '</span>' : $value;
      }

      if (($element['#type'] ?? '') === 'container') {
        $html = '';
        foreach ($element as $key => $child) {
          if (!is_array($child) || str_starts_with((string) $key, '#')) {
            continue;
          }
          $html .= $renderer->render($child);
        }
        return $html;
      }

      return '';
    });

    $container = new ContainerBuilder();
    $container->set('string_translation', $translation);
    $container->set('renderer', $renderer);
    $container->set('Drupal\ps_offer\OfferContextResolverInterface', $contextResolver);
    $container->set('ps_offer.surface_kpi_builder', new OfferSurfaceKpiBuilder($config_factory, $contextResolver));
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
   * Renders the first formatter element as HTML.
   *
   * @param array<int, array<string, mixed>> $result
   */
  private function renderFirst(array $result): string {
    $this->assertNotEmpty($result);
    return (string) \Drupal::service('renderer')->render($result[0]);
  }

  /**
   * Builds a mock FieldItemListInterface from an array of item data and a node.
   *
   * @param array<int, array<string, mixed>> $rows
   */
  private function buildItems(array $rows, NodeInterface $node): FieldItemListInterface {
    $item_objects = array_map(static fn(array $row): object => (object) $row, $rows);

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

    $html = $this->renderFirst($formatter->viewElements($items, 'fr'));
    $this->assertStringContainsString('1 200,5', $html);
    $this->assertStringContainsString('m²', $html);
    $this->assertStringNotContainsString('Divisible', $html);
    $this->assertStringContainsString('ps-surface-kpi__primary', $html);
  }

  /**
   * Divisible BUR with MINIM below TOTAL → divisible suffix in parentheses.
   */
  public function testBurDivisibleShowsTotalAndMinimumLot(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => TRUE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 2000.0, 'unit_code' => 'M2'],
      ['qualification' => 'MINIM', 'value' => 80.0, 'unit_code' => 'M2'],
      ['qualification' => 'DISPO', 'value' => 1500.0, 'unit_code' => 'M2'],
    ], $node);

    $html = $this->renderFirst($formatter->viewElements($items, 'fr'));
    $this->assertStringContainsString('2 000', $html);
    $this->assertStringContainsString('ps-surface-kpi__primary', $html);
    $this->assertStringContainsString('ps-surface-kpi__suffix', $html);
    $this->assertStringContainsString('(divisible from 80 m²)', $html);
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

    $html = $this->renderFirst($formatter->viewElements($items, 'fr'));
    $this->assertStringContainsString('5,5', $html);
    $this->assertStringContainsString('ha', $html);
    $this->assertStringNotContainsString('Divisible', $html);
  }

  /**
   * No TOTAL item → falls back to DISPO for non-divisible offers.
   */
  public function testMissingTotalFallsBackToDispo(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => FALSE]);
    $items = $this->buildItems([
      ['qualification' => 'DISPO', 'value' => 500.0, 'unit_code' => 'M2'],
    ], $node);

    $html = $this->renderFirst($formatter->viewElements($items, 'fr'));
    $this->assertStringContainsString('500', $html);
  }

  /**
   * Divisible but minimum lot equals total → no suffix.
   */
  public function testDivisibleWithEqualMinAndTotalHidesSuffix(): void {
    $formatter = $this->buildFormatter();
    $node = $this->buildOfferNode(['field_asset_type' => 'BUR', 'field_divisible' => TRUE]);
    $items = $this->buildItems([
      ['qualification' => 'TOTAL', 'value' => 1000.0, 'unit_code' => 'M2'],
      ['qualification' => 'DISPO', 'value' => 1000.0, 'unit_code' => 'M2'],
    ], $node);

    $html = $this->renderFirst($formatter->viewElements($items, 'fr'));
    $this->assertStringNotContainsString('Divisible', $html);
    $this->assertStringNotContainsString('ps-surface-kpi__suffix', $html);
  }

}
