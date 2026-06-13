<?php

/**
 * @file
 * CTX-ADM-04/05 — toggle operation_selected_show_budget and evaluate LOC price tab.
 */

declare(strict_types=1);

use Drupal\ps_context\Entity\PsContextRuleInterface;

/**
 * @param list<PsContextRuleInterface> $rules
 * @param array<string, string> $values
 */
function ps_ctx_tab_visible(array $rules, array $values, string $tab): bool {
  $tabs = [
    'group_budget' => TRUE,
    'group_surface' => TRUE,
    'group_capacity' => TRUE,
    'group_lots' => TRUE,
  ];
  $affected = [];
  foreach ($rules as $rule) {
    foreach ($rule->getActions() as $action) {
      if (in_array($action['action_type'] ?? '', ['show_tab', 'hide_tab'], TRUE)) {
        $affected[$action['target'] ?? ''] = TRUE;
      }
    }
  }
  foreach (array_keys($affected) as $key) {
    if (isset($tabs[$key])) {
      $tabs[$key] = TRUE;
    }
  }
  foreach ($rules as $rule) {
    $conditions = $rule->getConditions();
    $match = empty($conditions);
    if (!$match) {
      $results = [];
      foreach ($conditions as $condition) {
        $field = $condition['field_name'] ?? '';
        $operator = $condition['operator'] ?? 'equals';
        $expected = (string) ($condition['value'] ?? '');
        $actual = (string) ($values[$field] ?? '');
        $results[] = match ($operator) {
          'equals' => $actual === $expected,
          'not_equals' => $actual !== $expected,
          'empty' => $actual === '',
          'filled' => $actual !== '',
          default => FALSE,
        };
      }
      $match = $rule->getConditionsLogic() === 'OR'
        ? in_array(TRUE, $results, TRUE)
        : !in_array(FALSE, $results, TRUE);
    }
    if (!$match) {
      continue;
    }
    foreach ($rule->getActions() as $action) {
      if (($action['action_type'] ?? '') === 'show_tab' && isset($tabs[$action['target'] ?? ''])) {
        $tabs[$action['target']] = TRUE;
      }
      if (($action['action_type'] ?? '') === 'hide_tab' && isset($tabs[$action['target'] ?? ''])) {
        $tabs[$action['target']] = FALSE;
      }
    }
  }
  return $tabs[$tab] ?? FALSE;
}

$loadRules = static function (): array {
  $rules = \Drupal::entityTypeManager()
    ->getStorage('ps_context_rule')
    ->loadByProperties(['status' => TRUE]);
  uasort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());
  return array_values($rules);
};

$rule = \Drupal::entityTypeManager()->getStorage('ps_context_rule')->load('operation_selected_show_budget');
if ($rule === NULL) {
  print "CTX_ADM_04=skip\nCTX_ADM_05=skip\n";
  return;
}

$values = ['field_operation_type' => 'LOC'];
$before = ps_ctx_tab_visible($loadRules(), $values, 'group_budget');

$rule->set('status', FALSE)->save();
$afterOff = ps_ctx_tab_visible($loadRules(), $values, 'group_budget');

$rule->set('status', TRUE)->save();
$afterOn = ps_ctx_tab_visible($loadRules(), $values, 'group_budget');

print 'CTX_ADM_04=' . ($before && !$afterOff ? 'pass' : 'fail') . PHP_EOL;
print 'CTX_ADM_05=' . ($afterOn ? 'pass' : 'fail') . PHP_EOL;
