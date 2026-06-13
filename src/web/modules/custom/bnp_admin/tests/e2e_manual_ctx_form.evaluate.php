<?php

/**
 * @file
 * Evaluates ps_context tab visibility (mirrors ps-context-offer-form.js).
 */

declare(strict_types=1);

use Drupal\ps_context\Entity\PsContextRuleInterface;

/**
 * Evaluates tab visibility for given field values.
 *
 * @param list<\Drupal\ps_context\Entity\PsContextRuleInterface> $rules
 * @param array<string, string> $values
 *
 * @return array<string, bool>
 *   Tab machine names => visible.
 */
function ps_ctx_eval_tabs(array $rules, array $values): array {
  $tabs = [
    'group_budget' => TRUE,
    'group_surface' => TRUE,
    'group_capacity' => TRUE,
    'group_lots' => TRUE,
  ];
  $fields = [
    'field_divisible' => TRUE,
  ];

  $affectedTabs = [];
  $affectedFields = [];
  foreach ($rules as $rule) {
    foreach ($rule->getActions() as $action) {
      $type = $action['action_type'] ?? '';
      $target = $action['target'] ?? '';
      if (in_array($type, ['show_tab', 'hide_tab'], TRUE)) {
        $affectedTabs[$target] = TRUE;
      }
      if (in_array($type, ['show_field', 'hide_field'], TRUE)) {
        $affectedFields[$target] = TRUE;
      }
    }
  }

  foreach (array_keys($affectedTabs) as $tab) {
    if (isset($tabs[$tab])) {
      $tabs[$tab] = TRUE;
    }
  }
  foreach (array_keys($affectedFields) as $field) {
    if (isset($fields[$field])) {
      $fields[$field] = TRUE;
    }
  }

  foreach ($rules as $rule) {
    if (!ps_ctx_rule_matches($rule, $values)) {
      continue;
    }
    foreach ($rule->getActions() as $action) {
      $type = $action['action_type'] ?? '';
      $target = $action['target'] ?? '';
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
      }
    }
  }

  return [
    'tabs' => $tabs,
    'fields' => $fields,
  ];
}

/**
 * Checks whether a rule's conditions match field values.
 */
function ps_ctx_rule_matches(PsContextRuleInterface $rule, array $values): bool {
  $conditions = $rule->getConditions();
  if ($conditions === []) {
    return TRUE;
  }
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
      'contains' => str_contains($actual, $expected),
      default => FALSE,
    };
  }
  return $rule->getConditionsLogic() === 'OR'
    ? in_array(TRUE, $results, TRUE)
    : !in_array(FALSE, $results, TRUE);
}

/** @var \Drupal\ps_context\Entity\PsContextRuleInterface[] $rules */
$rules = \Drupal::entityTypeManager()
  ->getStorage('ps_context_rule')
  ->loadByProperties(['status' => TRUE]);
uasort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());
$rules = array_values($rules);

$empty = [];
$initial = ps_ctx_eval_tabs($rules, $empty);
$ent = ps_ctx_eval_tabs($rules, ['field_asset_type' => 'ENT']);
$cow = ps_ctx_eval_tabs($rules, ['field_asset_type' => 'COW']);
$loc = ps_ctx_eval_tabs($rules, ['field_operation_type' => 'LOC']);
$burLocDiv = ps_ctx_eval_tabs($rules, [
  'field_asset_type' => 'BUR',
  'field_operation_type' => 'LOC',
  'field_divisible' => '1',
]);
$burToCow = ps_ctx_eval_tabs($rules, ['field_asset_type' => 'COW']);

$out = [
  'rules_count' => (string) count($rules),
  'initial_price' => ($initial['tabs']['group_budget'] ?? TRUE) ? 'visible' : 'hidden',
  'initial_surface' => ($initial['tabs']['group_surface'] ?? TRUE) ? 'visible' : 'hidden',
  'initial_capacity' => ($initial['tabs']['group_capacity'] ?? TRUE) ? 'visible' : 'hidden',
  'initial_lots' => ($initial['tabs']['group_lots'] ?? TRUE) ? 'visible' : 'hidden',
  'ent_surface' => ($ent['tabs']['group_surface'] ?? FALSE) ? 'visible' : 'hidden',
  'cow_capacity' => ($cow['tabs']['group_capacity'] ?? FALSE) ? 'visible' : 'hidden',
  'cow_surface' => ($cow['tabs']['group_surface'] ?? TRUE) ? 'visible' : 'hidden',
  'cow_divisible' => ($cow['fields']['field_divisible'] ?? TRUE) ? 'visible' : 'hidden',
  'loc_price' => ($loc['tabs']['group_budget'] ?? FALSE) ? 'visible' : 'hidden',
  'bur_loc_div_lots' => ($burLocDiv['tabs']['group_lots'] ?? FALSE) ? 'visible' : 'hidden',
  'bur_to_cow_surface' => ($burToCow['tabs']['group_surface'] ?? TRUE) ? 'visible' : 'hidden',
];

// Budget defaults from set_default actions.
$defLocBur = ['field_operation_type' => 'LOC', 'field_asset_type' => 'BUR'];
$defLocCow = ['field_operation_type' => 'LOC', 'field_asset_type' => 'COW'];
$defVenBur = ['field_operation_type' => 'VEN', 'field_asset_type' => 'BUR'];
$defaults = [
  'field_budget_period' => NULL,
  'field_budget_unit' => NULL,
  'field_budget_currency' => NULL,
];
foreach ($rules as $rule) {
  if (!ps_ctx_rule_matches($rule, $defLocBur)) {
    continue;
  }
  foreach ($rule->getActions() as $action) {
    if (($action['action_type'] ?? '') === 'set_default' && array_key_exists($action['target'] ?? '', $defaults)) {
      $defaults[$action['target']] = $action['value'] ?? '';
    }
  }
}
$out['def_loc_bur_period'] = $defaults['field_budget_period'] ?? '';
$out['def_loc_bur_unit'] = $defaults['field_budget_unit'] ?? '';
foreach ($rules as $rule) {
  if (!ps_ctx_rule_matches($rule, $defLocCow)) {
    continue;
  }
  foreach ($rule->getActions() as $action) {
    if (($action['action_type'] ?? '') === 'set_default' && ($action['target'] ?? '') === 'field_budget_unit') {
      $out['def_loc_cow_unit'] = $action['value'] ?? '';
    }
  }
}
foreach ($rules as $rule) {
  if (!ps_ctx_rule_matches($rule, $defVenBur)) {
    continue;
  }
  foreach ($rule->getActions() as $action) {
    if (($action['action_type'] ?? '') === 'set_default' && ($action['target'] ?? '') === 'field_budget_unit') {
      $out['def_ven_bur_unit'] = $action['value'] ?? '';
    }
  }
}
$out['def_currency'] = $defaults['field_budget_currency'] ?? 'EUR';

// Detect content.editor admin form rendering (Gin horizontal-tabs).
$account = reset(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => 'content.editor']));
if ($account) {
  $role = \Drupal\user\Entity\Role::load('content_editor');
  $out['editor_admin_theme_perm'] = ($role && $role->hasPermission('view the administration theme')) ? 'yes' : 'no';

  \Drupal::service('account_switcher')->switchTo($account);
  $admin_theme = \Drupal::config('system.theme')->get('admin') ?: 'gin';
  $theme_manager = \Drupal::service('theme.manager');
  $previous_theme = $theme_manager->getActiveTheme()->getName();
  $theme_manager->setActiveTheme(\Drupal::service('theme.initialization')->getActiveThemeByName($admin_theme));

  $node = \Drupal::entityTypeManager()->getStorage('node')->create(['type' => 'offer']);
  $form = \Drupal::entityTypeManager()->getFormObject('node', 'default')->setEntity($node);
  $form_state = new \Drupal\Core\Form\FormState();
  $build = \Drupal::formBuilder()->getForm($form, $form_state);
  $html = (string) \Drupal::service('renderer')->renderRoot($build);

  $theme_manager->setActiveTheme(\Drupal::service('theme.initialization')->getActiveThemeByName($previous_theme));
  \Drupal::service('account_switcher')->switchBack();

  if (str_contains($html, 'horizontal-tabs')) {
    $out['editor_form_mode'] = 'horizontal-tabs';
  }
  elseif (str_contains($html, 'accordion')) {
    $out['editor_form_mode'] = 'accordion';
  }
  else {
    $out['editor_form_mode'] = 'unknown';
  }
  $out['editor_has_horizontal_price'] = str_contains($html, 'href="#edit-group-budget"') ? 'yes' : 'no';
  $out['editor_has_accordion_price'] = str_contains($html, 'edit-group-budget') ? 'yes' : 'no';
}

foreach ($out as $key => $value) {
  print $key . '=' . $value . PHP_EOL;
}
