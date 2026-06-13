<?php

declare(strict_types=1);

namespace Drupal\ps_context\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\ps_context\Entity\PsContextRuleInterface;
use Drupal\ps_context\Value\OfferContextState;

/**
 * Canonical PHP evaluator for ps_context rules (mirrors ps-context-offer-form.js).
 */
final class ContextRuleEvaluator implements ContextRuleEvaluatorInterface {

  /**
   * Dynamic tabs controlled by matrix show_tab / hide_tab actions.
   */
  private const DYNAMIC_TABS = [
    'group_budget',
    'group_surface',
    'group_capacity',
    'group_lots',
  ];

  /**
   * Dynamic fields controlled by matrix show_field / hide_field actions.
   */
  private const DYNAMIC_FIELDS = [
    'field_divisible',
  ];

  public function __construct(
    private readonly EntityTypeManagerInterface $entityTypeManager,
  ) {}

  public function resolveFromNode(NodeInterface $node): OfferContextState {
    return $this->resolveFromValues($this->fieldValuesFromNode($node));
  }

  public function resolveFromValues(array $fieldValues): OfferContextState {
    $rules = $this->loadActiveRules();

    $affectedTabs = [];
    $affectedFields = [];
    foreach ($rules as $rule) {
      foreach ($rule->getActions() as $action) {
        $type = $action['action_type'] ?? '';
        $target = $action['target'] ?? '';
        if (in_array($type, ['show_tab', 'hide_tab'], TRUE) && in_array($target, self::DYNAMIC_TABS, TRUE)) {
          $affectedTabs[$target] = TRUE;
        }
        if (in_array($type, ['show_field', 'hide_field'], TRUE) && in_array($target, self::DYNAMIC_FIELDS, TRUE)) {
          $affectedFields[$target] = TRUE;
        }
      }
    }

    $tabs = [];
    foreach (array_keys($affectedTabs) as $tab) {
      $tabs[$tab] = TRUE;
    }
    $fields = [];
    foreach (array_keys($affectedFields) as $field) {
      $fields[$field] = TRUE;
    }
    $defaults = [];

    foreach ($rules as $rule) {
      if (!$this->evaluateConditions($rule, $fieldValues)) {
        continue;
      }

      foreach ($rule->getActions() as $action) {
        $type = $action['action_type'] ?? '';
        $target = $action['target'] ?? '';
        $value = (string) ($action['value'] ?? '');

        switch ($type) {
          case 'show_tab':
            if (isset($tabs[$target])) {
              $tabs[$target] = TRUE;
            }
            break;

          case 'hide_tab':
            if (isset($tabs[$target])) {
              $tabs[$target] = FALSE;
            }
            break;

          case 'show_field':
            if (isset($fields[$target])) {
              $fields[$target] = TRUE;
            }
            break;

          case 'hide_field':
            if (isset($fields[$target])) {
              $fields[$target] = FALSE;
            }
            break;

          case 'set_default':
            if ($target !== '' && $value !== '') {
              $defaults[$target] = $value;
            }
            break;
        }
      }
    }

    return new OfferContextState($tabs, $fields, $defaults);
  }

  /**
   * @return array<string, string>
   */
  private function fieldValuesFromNode(NodeInterface $node): array {
    $values = [
      'field_asset_type' => '',
      'field_operation_type' => '',
      'field_divisible' => '',
    ];

    if ($node->hasField('field_asset_type') && !$node->get('field_asset_type')->isEmpty()) {
      $values['field_asset_type'] = strtoupper((string) $node->get('field_asset_type')->value);
    }
    if ($node->hasField('field_operation_type') && !$node->get('field_operation_type')->isEmpty()) {
      $values['field_operation_type'] = strtoupper((string) $node->get('field_operation_type')->value);
    }
    if ($node->hasField('field_divisible') && !$node->get('field_divisible')->isEmpty()) {
      $values['field_divisible'] = (string) (int) $node->get('field_divisible')->value;
    }

    return $values;
  }

  /**
   * @return list<\Drupal\ps_context\Entity\PsContextRuleInterface>
   */
  private function loadActiveRules(): array {
    /** @var \Drupal\ps_context\Entity\PsContextRuleInterface[] $rules */
    $rules = $this->entityTypeManager
      ->getStorage('ps_context_rule')
      ->loadByProperties(['status' => TRUE]);

    uasort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());

    return array_values($rules);
  }

  /**
   * @param array<string, string> $fieldValues
   */
  private function evaluateConditions(PsContextRuleInterface $rule, array $fieldValues): bool {
    $conditions = $rule->getConditions();

    if ($conditions === []) {
      return TRUE;
    }

    $results = array_map(
      fn(array $condition): bool => $this->evaluateCondition($condition, $fieldValues),
      $conditions,
    );

    return $rule->getConditionsLogic() === 'OR'
      ? in_array(TRUE, $results, TRUE)
      : !in_array(FALSE, $results, TRUE);
  }

  /**
   * @param array<string, string> $fieldValues
   */
  private function evaluateCondition(array $condition, array $fieldValues): bool {
    $field_name = $condition['field_name'] ?? '';
    $operator = $condition['operator'] ?? 'equals';
    $expected = (string) ($condition['value'] ?? '');
    $actual = $fieldValues[$field_name] ?? '';

    return match ($operator) {
      'equals' => $actual === $expected,
      'not_equals' => $actual !== $expected,
      'empty' => $actual === '',
      'filled' => $actual !== '',
      'contains' => str_contains($actual, $expected),
      default => FALSE,
    };
  }

}
