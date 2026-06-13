<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_context\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_context\Entity\PsContextRuleInterface;
use Drupal\ps_context\Service\ContextRuleEvaluator;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_context\Service\ContextRuleEvaluator
 * @group ps_context
 */
final class ContextRuleEvaluatorTest extends UnitTestCase {

  /**
   * @covers ::resolveFromValues
   */
  public function testEmptyStateHidesDynamicTabs(): void {
    $evaluator = new ContextRuleEvaluator($this->createEntityTypeManager([
      $this->createRule('default_hide_surface', -11, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_surface', 'value' => ''],
      ]),
      $this->createRule('default_hide_capacity', -10, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_capacity', 'value' => ''],
      ]),
      $this->createRule('default_hide_budget', -9, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_budget', 'value' => ''],
      ]),
      $this->createRule('default_hide_lots', -8, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_lots', 'value' => ''],
      ]),
    ]));

    $state = $evaluator->resolveFromValues([]);

    $this->assertFalse($state->isTabVisible('group_surface'));
    $this->assertFalse($state->isTabVisible('group_capacity'));
    $this->assertFalse($state->isTabVisible('group_budget'));
    $this->assertFalse($state->isTabVisible('group_lots'));
  }

  /**
   * @covers ::resolveFromValues
   */
  public function testCowIsCapacityDrivenFromMatrix(): void {
    $evaluator = new ContextRuleEvaluator($this->createEntityTypeManager([
      $this->createRule('default_hide_surface', -11, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_surface', 'value' => ''],
      ]),
      $this->createRule('default_hide_capacity', -10, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_capacity', 'value' => ''],
      ]),
      $this->createRule('asset_type_cow', 0, [
        ['field_name' => 'field_asset_type', 'operator' => 'equals', 'value' => 'COW'],
      ], [
        ['action_type' => 'hide_tab', 'target' => 'group_surface', 'value' => ''],
        ['action_type' => 'show_tab', 'target' => 'group_capacity', 'value' => ''],
        ['action_type' => 'hide_field', 'target' => 'field_divisible', 'value' => ''],
      ]),
    ]));

    $state = $evaluator->resolveFromValues(['field_asset_type' => 'COW']);

    $this->assertTrue($state->isCapacityDriven());
    $this->assertFalse($state->isTabVisible('group_surface'));
    $this->assertTrue($state->isTabVisible('group_capacity'));
    $this->assertFalse($state->isFieldVisible('field_divisible'));
  }

  /**
   * @covers ::resolveFromValues
   */
  public function testBurLocShowsSurfaceAndPrice(): void {
    $evaluator = new ContextRuleEvaluator($this->createEntityTypeManager([
      $this->createRule('default_hide_surface', -11, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_surface', 'value' => ''],
      ]),
      $this->createRule('default_hide_capacity', -10, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_capacity', 'value' => ''],
      ]),
      $this->createRule('default_hide_budget', -9, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_budget', 'value' => ''],
      ]),
      $this->createRule('asset_selected_show_surface', -1, [
        ['field_name' => 'field_asset_type', 'operator' => 'equals', 'value' => 'BUR'],
      ], [
        ['action_type' => 'show_tab', 'target' => 'group_surface', 'value' => ''],
      ]),
      $this->createRule('operation_selected_show_budget', 0, [
        ['field_name' => 'field_operation_type', 'operator' => 'filled', 'value' => ''],
      ], [
        ['action_type' => 'show_tab', 'target' => 'group_budget', 'value' => ''],
      ]),
    ]));

    $state = $evaluator->resolveFromValues([
      'field_asset_type' => 'BUR',
      'field_operation_type' => 'LOC',
    ]);

    $this->assertFalse($state->isCapacityDriven());
    $this->assertTrue($state->isTabVisible('group_surface'));
    $this->assertTrue($state->isTabVisible('group_budget'));
    $this->assertFalse($state->isTabVisible('group_capacity'));
  }

  /**
   * @param list<\Drupal\ps_context\Entity\PsContextRuleInterface> $rules
   */
  private function createEntityTypeManager(array $rules): EntityTypeManagerInterface {
    $storage = $this->createMock(EntityStorageInterface::class);
    $storage->method('loadByProperties')->willReturn($rules);

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $entityTypeManager->method('getStorage')->with('ps_context_rule')->willReturn($storage);

    return $entityTypeManager;
  }

  /**
   * @param list<array<string, string>> $conditions
   * @param list<array<string, string>> $actions
   */
  private function createRule(string $id, int $weight, array $conditions, array $actions): PsContextRuleInterface {
    $rule = $this->createMock(PsContextRuleInterface::class);
    $rule->method('getWeight')->willReturn($weight);
    $rule->method('getConditionsLogic')->willReturn('AND');
    $rule->method('getConditions')->willReturn($conditions);
    $rule->method('getActions')->willReturn($actions);
    $rule->method('id')->willReturn($id);

    return $rule;
  }

}
