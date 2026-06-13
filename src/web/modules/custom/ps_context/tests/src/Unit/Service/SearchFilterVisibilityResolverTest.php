<?php

declare(strict_types=1);

namespace Drupal\Tests\ps_context\Unit\Service;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\ps_context\Entity\PsContextRuleInterface;
use Drupal\ps_context\Service\ContextRuleEvaluator;
use Drupal\ps_context\Service\SearchFilterVisibilityResolver;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\ps_context\Service\SearchFilterVisibilityResolver
 * @group ps_context
 */
final class SearchFilterVisibilityResolverTest extends UnitTestCase {

  /**
   * @covers ::resolve
   */
  public function testResolveWithoutAssetShowsSurface(): void {
    $resolver = new SearchFilterVisibilityResolver(new ContextRuleEvaluator($this->createEntityTypeManager([])));

    $result = $resolver->resolve(NULL);

    $this->assertTrue($result['show_surface']);
    $this->assertFalse($result['show_capacity']);
    $this->assertSame('surface', $result['primary_filter']);
  }

  /**
   * @covers ::resolve
   */
  public function testResolveCowShowsCapacityFromMatrix(): void {
    $rules = [
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
      ]),
    ];

    $resolver = new SearchFilterVisibilityResolver(new ContextRuleEvaluator($this->createEntityTypeManager($rules)));
    $result = $resolver->resolve('COW');

    $this->assertFalse($result['show_surface']);
    $this->assertTrue($result['show_capacity']);
    $this->assertSame('capacity', $result['primary_filter']);
  }

  /**
   * @covers ::resolve
   */
  public function testResolveBurShowsSurfaceFromMatrix(): void {
    $rules = [
      $this->createRule('default_hide_surface', -11, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_surface', 'value' => ''],
      ]),
      $this->createRule('default_hide_capacity', -10, [], [
        ['action_type' => 'hide_tab', 'target' => 'group_capacity', 'value' => ''],
      ]),
      $this->createRule('asset_selected_show_surface', -1, [
        ['field_name' => 'field_asset_type', 'operator' => 'equals', 'value' => 'BUR'],
      ], [
        ['action_type' => 'show_tab', 'target' => 'group_surface', 'value' => ''],
      ]),
    ];

    $resolver = new SearchFilterVisibilityResolver(new ContextRuleEvaluator($this->createEntityTypeManager($rules)));
    $result = $resolver->resolve('BUR');

    $this->assertTrue($result['show_surface']);
    $this->assertFalse($result['show_capacity']);
    $this->assertSame('surface', $result['primary_filter']);
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
