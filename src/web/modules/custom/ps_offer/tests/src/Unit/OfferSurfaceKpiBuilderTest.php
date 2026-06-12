<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_offer\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\node\NodeInterface;
use Drupal\ps_offer\Service\OfferSurfaceKpiBuilder;
use Drupal\Tests\UnitTestCase;

/**
 * Unit tests for OfferSurfaceKpiBuilder.
 *
 * @group ps_offer
 */
final class OfferSurfaceKpiBuilderTest extends UnitTestCase {

  /**
   * Builds a builder with default test settings.
   */
  private function buildBuilder(array $settings = []): OfferSurfaceKpiBuilder {
    $defaults = [
      'surface_divisible_template' => 'Divisible from @surface',
      'surface_capacity_unit' => 'seats',
    ];
    $merged = array_merge($defaults, $settings);

    $config = $this->createMock(ImmutableConfig::class);
    $config->method('get')->willReturnCallback(
      static fn (string $key): mixed => $merged[$key] ?? NULL,
    );

    $config_factory = $this->createMock(ConfigFactoryInterface::class);
    $config_factory->method('get')->with('ps_offer.settings')->willReturn($config);

    return new OfferSurfaceKpiBuilder($config_factory);
  }

  /**
   * Builds a mock offer node.
   *
   * @param array<string, mixed> $field_values
   */
  private function buildOfferNode(array $field_values): NodeInterface {
    $node = $this->createMock(NodeInterface::class);

    $node->method('hasField')->willReturnCallback(
      static fn (string $name): bool => array_key_exists($name, $field_values),
    );

    $node->method('get')->willReturnCallback(function (string $name) use ($field_values): object {
      $value = $field_values[$name] ?? NULL;

      if ($name === 'field_surfaces') {
        return new class($value ?? []) {
          public function __construct(private readonly array $items) {}
          public function isEmpty(): bool {
            return $this->items === [];
          }
          public function getIterator(): \ArrayIterator {
            return new \ArrayIterator(array_map(
              static fn (array $row): object => (object) $row,
              $this->items,
            ));
          }
        };
      }

      return new class($value) {
        public function __construct(public readonly mixed $value) {}
        public function isEmpty(): bool {
          return $this->value === NULL || $this->value === '' || $this->value === [];
        }
      };
    });

    return $node;
  }

  public function testCowShowsCapacity(): void {
    $builder = $this->buildBuilder(['surface_capacity_unit' => 'postes']);
    $node = $this->buildOfferNode([
      'field_asset_type' => 'COW',
      'field_capacity_total' => 12,
    ]);

    $this->assertSame('12 postes', $builder->buildKpiSummary($node));
  }

  public function testTerShowsTotalOnly(): void {
    $builder = $this->buildBuilder();
    $node = $this->buildOfferNode([
      'field_asset_type' => 'TER',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 5.5, 'unit_code' => 'HA'],
        ['qualification' => 'DISPO', 'value' => 3.0, 'unit_code' => 'HA'],
      ],
    ]);

    $this->assertSame('5,5 ha', $builder->buildKpiSummary($node));
  }

  public function testDivisibleWithMinimBelowTotal(): void {
    $builder = $this->buildBuilder();
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 1890.0, 'unit_code' => 'M2'],
        ['qualification' => 'MINIM', 'value' => 68.0, 'unit_code' => 'M2'],
        ['qualification' => 'DISPO', 'value' => 1500.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame(
      '1 890 m² (divisible from 68 m²)',
      $builder->buildKpiSummary($node),
    );
  }

  public function testDivisibleWhenMinEqualsTotalHidesSuffix(): void {
    $builder = $this->buildBuilder();
    $node = $this->buildOfferNode([
      'field_asset_type' => 'ACT',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 1000.0, 'unit_code' => 'M2'],
        ['qualification' => 'DISPO', 'value' => 1000.0, 'unit_code' => 'M2'],
        ['qualification' => 'MINIM', 'value' => 1000.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame('1 000 m²', $builder->buildKpiSummary($node));
  }

  public function testNonDivisibleShowsTotalOnly(): void {
    $builder = $this->buildBuilder();
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => FALSE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 1200.0, 'unit_code' => 'M2'],
        ['qualification' => 'DISPO', 'value' => 800.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame('1 200 m²', $builder->buildKpiSummary($node));
  }

  public function testDivisibleUsesParentheses(): void {
    $builder = $this->buildBuilder([
      'surface_divisible_template' => 'Divisible dès @surface',
    ]);
    $node = $this->buildOfferNode([
      'field_asset_type' => 'ACT',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 2000.0, 'unit_code' => 'M2'],
        ['qualification' => 'MINIM', 'value' => 300.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame(
      '2 000 m² (divisible dès 300 m²)',
      $builder->buildKpiSummary($node),
    );
  }

  public function testDivisibleUsesEtrefWhenMinimMissing(): void {
    $builder = $this->buildBuilder([
      'surface_divisible_template' => 'Divisible dès @surface',
    ]);
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 2000.0, 'unit_code' => 'M2'],
        ['qualification' => 'ETREF', 'value' => 80.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame(
      '2 000 m² (divisible dès 80 m²)',
      $builder->buildKpiSummary($node),
    );
  }

  public function testDivisiblePrefersMinimOverEtref(): void {
    $builder = $this->buildBuilder([
      'surface_divisible_template' => 'Divisible dès @surface',
    ]);
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 2000.0, 'unit_code' => 'M2'],
        ['qualification' => 'MINIM', 'value' => 80.0, 'unit_code' => 'M2'],
        ['qualification' => 'ETREF', 'value' => 120.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame(
      '2 000 m² (divisible dès 80 m²)',
      $builder->buildKpiSummary($node),
    );
  }

  public function testNonDivisibleUsesDispoWhenTotalMissing(): void {
    $builder = $this->buildBuilder();
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => FALSE,
      'field_surfaces' => [
        ['qualification' => 'DISPO', 'value' => 800.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame('800 m²', $builder->buildKpiSummary($node));
  }

  public function testCompareKpiSummaryMatchesKpiSummary(): void {
    $builder = $this->buildBuilder([
      'surface_divisible_template' => 'Divisible dès @surface',
    ]);
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 2000.0, 'unit_code' => 'M2'],
        ['qualification' => 'MINIM', 'value' => 80.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame(
      $builder->buildKpiSummary($node),
      $builder->buildCompareKpiSummary($node),
    );
  }

  public function testBuildKpiPartsSplitsPrimaryAndSuffix(): void {
    $builder = $this->buildBuilder();
    $node = $this->buildOfferNode([
      'field_asset_type' => 'BUR',
      'field_divisible' => TRUE,
      'field_surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 838.0, 'unit_code' => 'M2'],
        ['qualification' => 'MINIM', 'value' => 38.0, 'unit_code' => 'M2'],
      ],
    ]);

    $this->assertSame(
      [
        'primary' => '838 m²',
        'suffix' => '(divisible from 38 m²)',
      ],
      $builder->buildKpiParts($node),
    );
  }

}
