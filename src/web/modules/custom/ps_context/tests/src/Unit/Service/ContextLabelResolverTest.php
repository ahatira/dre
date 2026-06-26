<?php

declare(strict_types=1);

namespace Drupal\ps_context\Tests\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_context\Entity\PsContextLabelProfileInterface;
use Drupal\ps_context\Service\ContextLabelResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_context\Service\ContextLabelResolver
 * @group ps_context
 */
final class ContextLabelResolverTest extends UnitTestCase {

  /**
   * @covers ::resolve
   * @dataProvider resolveProvider
   */
  public function testResolve(?string $asset, ?string $op, ?string $expectedUnit, string $expectedFieldLabel): void {
    $resolver = $this->createResolver();
    $config = $resolver->resolve($asset, $op);

    self::assertSame($expectedUnit, $config['budget_unit']);
    self::assertSame($expectedFieldLabel, $config['field_label']);
  }

  /**
   * @return array<string, array{0: ?string, 1: ?string, 2: ?string, 3: string}>
   */
  public static function resolveProvider(): array {
    return [
      'flexible' => [NULL, NULL, NULL, 'Budget'],
      'rent bur' => ['BUR', 'LOC', 'PER_M2', 'Rent'],
      'rent cow' => ['COW', 'LOC', 'PER_POSTE', 'Rent'],
      'sale any asset' => ['ENT', 'VEN', 'GLOBAL', 'Price'],
      'flexible with asset only' => ['BUR', NULL, NULL, 'Budget'],
    ];
  }

  /**
   * @covers ::resolveHomepageEntry
   */
  public function testResolveHomepageEntryDefaultFlexible(): void {
    $resolver = $this->createResolver();
    $config = $resolver->resolveHomepageEntry(NULL, NULL);

    self::assertSame('Max. budget', $config['max_label']);
    self::assertSame('Max. budget', $config['max_placeholder']);
  }

  /**
   * @covers ::resolveHomepageEntry
   */
  public function testResolveHomepageEntryCowRent(): void {
    $resolver = $this->createResolver();
    $config = $resolver->resolveHomepageEntry('COW', 'LOC');

    self::assertSame('Max. rent (per desk/day)', $config['max_label']);
    self::assertSame($config['max_label'], $config['max_placeholder']);
    self::assertSame(1, $config['step']);
  }

  /**
   * @covers ::resolveCapacity
   */
  public function testResolveCapacityCowRent(): void {
    $resolver = $this->createResolver();
    $config = $resolver->resolveCapacity('COW', 'LOC');

    self::assertSame('Desk capacity', $config['field_label']);
    self::assertSame('Min desk capacity', $config['min_label']);
    self::assertSame('Max desk capacity', $config['max_label']);
  }

  /**
   * @covers ::resolveHomepageCapacity
   */
  public function testResolveHomepageCapacityCowRent(): void {
    $resolver = $this->createResolver();
    $config = $resolver->resolveHomepageCapacity('COW', 'LOC');

    self::assertSame('Desk capacity', $config['field_label']);
    self::assertTrue($config['hide_operation']);
  }

  private function createResolver(): ContextLabelResolver {
    $profiles = [
      $this->profile('default', '*', '*', 0, [
        'search_budget_field_label' => 'Budget',
        'search_budget_min_label' => 'Min budget (€)',
        'search_budget_max_label' => 'Max budget (€)',
        'search_budget_input_unit' => '€',
        'search_budget_value_suffix' => ' €',
        'search_budget_step' => '10',
        'hero_budget_max_label' => 'Max. budget',
        'hero_budget_max_placeholder' => 'Max. budget',
      ]),
      $this->profile('loc_rent', '*', 'LOC', 10, [
        'search_budget_field_label' => 'Rent',
        'search_budget_min_label' => 'Min rent (€/m²/year)',
        'search_budget_max_label' => 'Max rent (€/m²/year)',
        'search_budget_input_unit' => '€/m²/yr',
        'search_budget_value_suffix' => ' €/m²/yr',
        'search_budget_step' => '10',
        'search_budget_budget_unit' => 'PER_M2',
        'search_budget_budget_period' => 'YEAR',
        'hero_budget_max_label' => 'Max. rent',
        'hero_budget_max_placeholder' => 'Max. rent',
      ]),
      $this->profile('loc_cow', 'COW', 'LOC', 20, [
        'hero_budget_max_label' => 'Max. rent (per desk/day)',
        'hero_budget_max_placeholder' => 'Max. rent (per desk/day)',
        'search_budget_field_label' => 'Rent',
        'search_budget_min_label' => 'Min rent (€/desk/day)',
        'search_budget_max_label' => 'Max rent (€/desk/day)',
        'search_budget_input_unit' => '€/desk/day',
        'search_budget_value_suffix' => ' €/desk/day',
        'search_budget_step' => '1',
        'search_budget_budget_unit' => 'PER_POSTE',
        'search_budget_budget_period' => 'DAY',
        'search_capacity_field_label' => 'Desk capacity',
        'search_capacity_min_label' => 'Min desk capacity',
        'search_capacity_max_label' => 'Max desk capacity',
        'hero_capacity_field_label' => 'Desk capacity',
        'hero_hide_operation_toggle' => '1',
      ]),
      $this->profile('ven_sale', '*', 'VEN', 10, [
        'search_budget_field_label' => 'Price',
        'search_budget_min_label' => 'Min price (€)',
        'search_budget_max_label' => 'Max price (€)',
        'search_budget_input_unit' => '€',
        'search_budget_value_suffix' => ' €',
        'search_budget_step' => '1000',
        'search_budget_budget_unit' => 'GLOBAL',
        'hero_budget_max_label' => 'Max. price',
        'hero_budget_max_placeholder' => 'Max. price',
      ]),
    ];

    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadMultiple')->willReturn($profiles);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')
      ->with('ps_context_label_profile')
      ->willReturn($storage);

    return new ContextLabelResolver($entityTypeManager);
  }

  /**
   * @param array<string, string> $labels
   */
  private function profile(string $id, string $asset, string $operation, int $weight, array $labels): PsContextLabelProfileInterface {
    $profile = $this->createMock(PsContextLabelProfileInterface::class);
    $profile->method('status')->willReturn(TRUE);
    $profile->method('id')->willReturn($id);
    $profile->method('getAssetType')->willReturn($asset);
    $profile->method('getOperationType')->willReturn($operation);
    $profile->method('getWeight')->willReturn($weight);
    $profile->method('getLabels')->willReturn($labels);
    return $profile;
  }

}
