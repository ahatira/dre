<?php

/**
 * @file
 * Manual recette — OFF-01→12 (tabs + brouillon) et VAL-01→10.
 */

declare(strict_types=1);

use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormState;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\Entity\Node;
use Drupal\ps_context\Entity\PsContextRuleInterface;
use Drupal\ps_offer\Hook\OfferHooks;

/**
 * Loads enabled context rules sorted by weight.
 *
 * @return list<\Drupal\ps_context\Entity\PsContextRuleInterface>
 */
function recette_context_rules(): array {
  /** @var \Drupal\ps_context\Entity\PsContextRuleInterface[] $rules */
  $rules = \Drupal::entityTypeManager()
    ->getStorage('ps_context_rule')
    ->loadByProperties(['status' => TRUE]);
  uasort($rules, static fn(PsContextRuleInterface $a, PsContextRuleInterface $b): int => $a->getWeight() <=> $b->getWeight());
  return array_values($rules);
}

/**
 * @param list<\Drupal\ps_context\Entity\PsContextRuleInterface> $rules
 * @param array<string, string> $values
 *
 * @return array{tabs: array<string, bool>, fields: array<string, bool>}
 */
function recette_eval_tabs(array $rules, array $values): array {
  $tabs = [
    'group_budget' => TRUE,
    'group_surface' => TRUE,
    'group_capacity' => TRUE,
    'group_lots' => TRUE,
  ];
  $fields = [
    'field_divisible' => TRUE,
  ];

  foreach ($rules as $rule) {
    if (!recette_rule_matches($rule, $values)) {
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

  return ['tabs' => $tabs, 'fields' => $fields];
}

function recette_rule_matches(PsContextRuleInterface $rule, array $values): bool {
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

function recette_messenger_text(string $type): string {
  $parts = [];
  foreach (\Drupal::messenger()->messagesByType($type) as $message) {
    $parts[] = (string) $message;
  }
  return implode(' | ', $parts);
}

function recette_clear_messenger(): void {
  \Drupal::messenger()->deleteAll();
}

/**
 * @return array<string, mixed>
 */
function recette_offer_matrix(): array {
  return [
    'OFF-01' => ['op' => 'LOC', 'asset' => 'BUR', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-02' => ['op' => 'LOC', 'asset' => 'BUR', 'div' => '1', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => TRUE],
    'OFF-03' => ['op' => 'LOC', 'asset' => 'COW', 'div' => '0', 'surface' => FALSE, 'capacity' => TRUE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-04' => ['op' => 'LOC', 'asset' => 'ENT', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-05' => ['op' => 'LOC', 'asset' => 'ACT', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-06' => ['op' => 'LOC', 'asset' => 'COM', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-07' => ['op' => 'LOC', 'asset' => 'TER', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-08' => ['op' => 'VEN', 'asset' => 'BUR', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-09' => ['op' => 'VEN', 'asset' => 'BUR', 'div' => '1', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => TRUE],
    'OFF-10' => ['op' => 'VEN', 'asset' => 'COW', 'div' => '0', 'surface' => FALSE, 'capacity' => TRUE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-11' => ['op' => 'VEN', 'asset' => 'COM', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
    'OFF-12' => ['op' => 'VEN', 'asset' => 'TER', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'price' => TRUE, 'lots' => FALSE],
  ];
}

function recette_tab_ok(array $tabs, string $group, bool $expected): bool {
  return (bool) ($tabs[$group] ?? FALSE) === $expected;
}

function recette_save_draft_offer(string $case_id, array $case): string {
  $editor = reset(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => 'content.editor']));
  if (!$editor) {
    return 'fail_user';
  }

  \Drupal::service('account_switcher')->switchTo($editor);
  recette_clear_messenger();

  try {
    $node = Node::create([
      'type' => 'offer',
      'title' => $case_id . ' Recette ' . time(),
      'langcode' => 'en',
      'status' => 0,
      'uid' => $editor->id(),
    ]);
    $node->set('field_client_type', 'B2B');
    $node->set('field_operation_type', $case['op']);
    $node->set('field_asset_type', $case['asset']);
    $node->set('field_divisible', (int) $case['div']);
    if ($node->hasField('field_mandate_type')) {
      $node->set('field_mandate_type', 'SIM');
    }
    if ($node->hasField('field_reference_auto')) {
      $node->set('field_reference_auto', 1);
    }
    if ($node->hasField('field_reference')) {
      $node->set('field_reference', 'REC-' . str_replace('-', '', $case_id) . '-' . time());
    }
    if ($case['asset'] === 'COW') {
      $node->set('field_capacity_mode', 'SEAT_BASED');
      $node->set('field_capacity_total', 20);
      $node->set('field_capacity_available', 15);
    }
    if ($case['lots']) {
      // Lots tab visibility only — inline divisions are form/UI tested separately.
    }
    elseif ($case['surface']) {
      $node->set('field_surfaces', [
        ['qualification' => 'TOTAL', 'value' => 500, 'unit' => 'M2'],
      ]);
    }
    $node->set('field_budget_value', 150000);
    $violations = $node->validate();
    if ($violations->count() > 0) {
      return 'fail_entity_validation:' . $violations->get(0)->getMessage();
    }
    $node->save();
    return 'pass_nid:' . $node->id();
  }
  catch (\Throwable $e) {
    return 'fail_exception:' . $e->getMessage();
  }
  finally {
    \Drupal::service('account_switcher')->switchBack();
    recette_clear_messenger();
  }
}

/**
 * @param callable(): void $callback
 */
function recette_try_save(callable $callback): string {
  recette_clear_messenger();
  try {
    $callback();
    return 'saved';
  }
  catch (EntityStorageException $e) {
    return 'blocked:' . $e->getMessage();
  }
  catch (\Throwable $e) {
    return 'error:' . $e->getMessage();
  }
}

function recette_base_offer(array $overrides = []): Node {
  $defaults = [
    'type' => 'offer',
    'title' => 'VAL Recette ' . microtime(TRUE),
    'langcode' => 'en',
    'status' => 0,
  ];
  $node = Node::create($defaults + $overrides);
  $node->set('field_client_type', 'B2B');
  $node->set('field_operation_type', 'LOC');
  $node->set('field_asset_type', 'BUR');
  $node->set('field_divisible', 0);
  return $node;
}

// --- OFF-01 → OFF-12 ---
$rules = recette_context_rules();
foreach (recette_offer_matrix() as $case_id => $case) {
  $values = [
    'field_operation_type' => $case['op'],
    'field_asset_type' => $case['asset'],
    'field_divisible' => $case['div'],
  ];
  $eval = recette_eval_tabs($rules, $values);
  $tabs = $eval['tabs'];
  $tabs_ok = recette_tab_ok($tabs, 'group_surface', $case['surface'])
    && recette_tab_ok($tabs, 'group_capacity', $case['capacity'])
    && recette_tab_ok($tabs, 'group_budget', $case['price'])
    && recette_tab_ok($tabs, 'group_lots', $case['lots']);

  if (!$tabs_ok) {
    print $case_id . '=fail_tabs' . PHP_EOL;
    continue;
  }

  $save = recette_save_draft_offer($case_id, $case);
  if (str_starts_with($save, 'pass_nid:')) {
    print $case_id . '=pass,' . $save . PHP_EOL;
  }
  else {
    print $case_id . '=fail_save,' . $save . PHP_EOL;
  }
}

// --- OFF-13 → OFF-18 (cas complémentaires) ---
$off01 = recette_offer_matrix()['OFF-01'];

// OFF-13 : B2C + LOC + BUR — mêmes onglets que B2B.
$values13 = [
  'field_operation_type' => 'LOC',
  'field_asset_type' => 'BUR',
  'field_divisible' => '0',
];
$eval13 = recette_eval_tabs($rules, $values13);
$tabs13 = $eval13['tabs'];
$tabs13_ok = recette_tab_ok($tabs13, 'group_surface', $off01['surface'])
  && recette_tab_ok($tabs13, 'group_capacity', $off01['capacity'])
  && recette_tab_ok($tabs13, 'group_budget', $off01['price'])
  && recette_tab_ok($tabs13, 'group_lots', $off01['lots']);
if (!$tabs13_ok) {
  print 'OFF-13=fail_tabs' . PHP_EOL;
}
else {
  $save13 = recette_save_draft_offer('OFF-13', $off01);
  if (str_starts_with($save13, 'pass_nid:')) {
    $nid13 = (int) substr($save13, strlen('pass_nid:'));
    $node13 = Node::load($nid13);
    if ($node13) {
      $node13->set('field_client_type', 'B2C');
      $node13->save();
    }
    print 'OFF-13=pass,' . $save13 . ',client:B2C' . PHP_EOL;
  }
  else {
    print 'OFF-13=fail_save,' . $save13 . PHP_EOL;
  }
}

// OFF-14 : état initial vide — Price, Surface, Capacity, Lots masqués.
$eval14 = recette_eval_tabs($rules, []);
$tabs14 = $eval14['tabs'];
$off14_ok = !($tabs14['group_budget'] ?? TRUE)
  && !($tabs14['group_surface'] ?? TRUE)
  && !($tabs14['group_capacity'] ?? TRUE)
  && !($tabs14['group_lots'] ?? TRUE);
print 'OFF-14=' . ($off14_ok ? 'pass,all_hidden' : 'fail_tabs') . PHP_EOL;

// OFF-15 : BUR → COW — Surface disparaît, Capacity apparaît, Divisible masqué.
$bur15 = recette_eval_tabs($rules, ['field_operation_type' => 'LOC', 'field_asset_type' => 'BUR']);
$cow15 = recette_eval_tabs($rules, ['field_operation_type' => 'LOC', 'field_asset_type' => 'COW']);
$off15_ok = ($bur15['tabs']['group_surface'] ?? FALSE)
  && !($bur15['tabs']['group_capacity'] ?? TRUE)
  && !($cow15['tabs']['group_surface'] ?? TRUE)
  && ($cow15['tabs']['group_capacity'] ?? FALSE)
  && !($cow15['fields']['field_divisible'] ?? TRUE);
print 'OFF-15=' . ($off15_ok ? 'pass,bur_to_cow' : 'fail_transition') . PHP_EOL;

// OFF-16 : COW → BUR — inverse OFF-15.
$cow16 = recette_eval_tabs($rules, ['field_operation_type' => 'LOC', 'field_asset_type' => 'COW']);
$bur16 = recette_eval_tabs($rules, ['field_operation_type' => 'LOC', 'field_asset_type' => 'BUR']);
$off16_ok = !($cow16['tabs']['group_surface'] ?? TRUE)
  && ($cow16['tabs']['group_capacity'] ?? FALSE)
  && ($bur16['tabs']['group_surface'] ?? FALSE)
  && !($bur16['tabs']['group_capacity'] ?? TRUE)
  && ($bur16['fields']['field_divisible'] ?? FALSE);
print 'OFF-16=' . ($off16_ok ? 'pass,cow_to_bur' : 'fail_transition') . PHP_EOL;

// OFF-17 : Divisible coché sans opération/actif — Lots masqué.
$eval17 = recette_eval_tabs($rules, ['field_divisible' => '1']);
$off17_ok = !($eval17['tabs']['group_lots'] ?? TRUE);
print 'OFF-17=' . ($off17_ok ? 'pass,lots_hidden' : 'fail_lots_visible') . PHP_EOL;

// OFF-18 : édition — onglets identiques au create pour une offre existante.
$save18 = recette_save_draft_offer('OFF-18', $off01);
if (!str_starts_with($save18, 'pass_nid:')) {
  print 'OFF-18=fail_seed,' . $save18 . PHP_EOL;
}
else {
  $nid18 = (int) substr($save18, strlen('pass_nid:'));
  $node18 = Node::load($nid18);
  $edit_values = [
    'field_operation_type' => (string) $node18->get('field_operation_type')->value,
    'field_asset_type' => (string) $node18->get('field_asset_type')->value,
    'field_divisible' => (string) (int) $node18->get('field_divisible')->value,
  ];
  $create_values = [
    'field_operation_type' => $off01['op'],
    'field_asset_type' => $off01['asset'],
    'field_divisible' => $off01['div'],
  ];
  $eval_create = recette_eval_tabs($rules, $create_values);
  $eval_edit = recette_eval_tabs($rules, $edit_values);
  $tabs_match = $eval_create['tabs'] === $eval_edit['tabs']
    && $eval_create['fields'] === $eval_edit['fields'];

  $form_match = 'skip';
  $account = reset(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => 'content.editor']));
  if ($account && $node18) {
    \Drupal::service('account_switcher')->switchTo($account);
    $admin_theme = \Drupal::config('system.theme')->get('admin') ?: 'gin';
    $theme_manager = \Drupal::service('theme.manager');
    $previous_theme = $theme_manager->getActiveTheme()->getName();
    $theme_manager->setActiveTheme(\Drupal::service('theme.initialization')->getActiveThemeByName($admin_theme));

    $form_object = \Drupal::entityTypeManager()->getFormObject('node', 'default');
    $form_object->setEntity($node18);
    $form_state = new FormState();
    $build = \Drupal::formBuilder()->getForm($form_object, $form_state);
    $html = (string) \Drupal::service('renderer')->renderRoot($build);

    $theme_manager->setActiveTheme(\Drupal::service('theme.initialization')->getActiveThemeByName($previous_theme));
    \Drupal::service('account_switcher')->switchBack();

    $form_match = str_contains($html, 'horizontal-tabs') ? 'horizontal-tabs' : 'other';
  }

  if ($tabs_match && $form_match === 'horizontal-tabs') {
    print 'OFF-18=pass,nid:' . $nid18 . ',form:' . $form_match . PHP_EOL;
  }
  else {
    print 'OFF-18=fail,tabs_match:' . ($tabs_match ? 'yes' : 'no') . ',form:' . $form_match . PHP_EOL;
  }
}

// --- VAL-01 : sans surface TOTAL ---
$val01_draft = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->setUnpublished();
  $node->save();
});
$val01_warn = str_contains(recette_messenger_text(MessengerInterface::TYPE_WARNING), 'TOTAL surface');
recette_clear_messenger();
$val01_pub = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->setPublished();
  $node->set('field_primary_agent', 1);
  $node->save();
});
print 'VAL-01_draft=' . ($val01_draft === 'saved' && $val01_warn ? 'warn_ok' : 'fail') . PHP_EOL;
print 'VAL-01_pub=' . (str_starts_with($val01_pub, 'blocked:') ? 'block_ok' : 'fail:' . $val01_pub) . PHP_EOL;
recette_clear_messenger();

// --- VAL-02 : sans galerie ---
$val02_draft = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->setUnpublished();
  $node->set('field_surfaces', [['qualification' => 'TOTAL', 'value' => 100, 'unit' => 'M2']]);
  $node->save();
});
$val02_form_object = \Drupal::entityTypeManager()->getFormObject('node', 'default');
$val02_node = recette_base_offer();
$val02_node->setPublished();
$val02_form_object->setEntity($val02_node);
$form = [];
$form_state = new FormState();
$form_state->setFormObject($val02_form_object);
$form_state->setValues([
  'status' => ['value' => 1],
  'field_media_gallery' => ['target_id' => ''],
]);
OfferHooks::validateGallery($form, $form_state);
$val02_pub_form = $form_state->hasAnyErrors();
print 'VAL-02_draft=' . ($val02_draft === 'saved' ? 'pass_ok' : 'fail:' . $val02_draft) . PHP_EOL;
print 'VAL-02_pub=' . ($val02_pub_form ? 'form_block_ok' : 'fail') . PHP_EOL;
recette_clear_messenger();

// --- VAL-03 : publication sans agent ---
recette_clear_messenger();
$val03_node = recette_base_offer();
$val03_node->setPublished();
$val03_node->set('field_surfaces', [['qualification' => 'TOTAL', 'value' => 100, 'unit' => 'M2']]);
$val03_result = recette_try_save(static function () use ($val03_node): void {
  $val03_node->save();
});
$val03_warn = str_contains(recette_messenger_text(MessengerInterface::TYPE_WARNING), 'primary agent');
$val03_draft = !$val03_node->isPublished();
print 'VAL-03=' . ($val03_result === 'saved' && $val03_warn && $val03_draft ? 'draft_warn_ok' : 'fail') . PHP_EOL;
recette_clear_messenger();

// --- VAL-04 : capacité SEAT_BASED total vide ---
$val04_draft = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_asset_type', 'COW');
  $node->set('field_capacity_mode', 'SEAT_BASED');
  $node->setUnpublished();
  $node->save();
});
$val04_warn = str_contains(recette_messenger_text(MessengerInterface::TYPE_WARNING), 'Capacity total');
recette_clear_messenger();
$val04_pub = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_asset_type', 'COW');
  $node->set('field_capacity_mode', 'SEAT_BASED');
  $node->setPublished();
  $node->set('field_primary_agent', 1);
  $node->set('field_surfaces', [['qualification' => 'TOTAL', 'value' => 100, 'unit' => 'M2']]);
  $node->save();
});
print 'VAL-04_draft=' . ($val04_draft === 'saved' && $val04_warn ? 'warn_ok' : 'fail') . PHP_EOL;
print 'VAL-04_pub=' . (str_starts_with($val04_pub, 'blocked:') ? 'block_ok' : 'fail:' . $val04_pub) . PHP_EOL;
recette_clear_messenger();

// --- VAL-05 : dispo > total ---
$val05_draft = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_asset_type', 'COW');
  $node->set('field_capacity_mode', 'SEAT_BASED');
  $node->set('field_capacity_total', 20);
  $node->set('field_capacity_available', 25);
  $node->setUnpublished();
  $node->save();
});
$val05_warn = str_contains(recette_messenger_text(MessengerInterface::TYPE_WARNING), 'Capacity available');
recette_clear_messenger();
$val05_pub = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_asset_type', 'COW');
  $node->set('field_capacity_mode', 'SEAT_BASED');
  $node->set('field_capacity_total', 20);
  $node->set('field_capacity_available', 25);
  $node->setPublished();
  $node->set('field_primary_agent', 1);
  $node->set('field_surfaces', [['qualification' => 'TOTAL', 'value' => 100, 'unit' => 'M2']]);
  $node->save();
});
print 'VAL-05_draft=' . ($val05_draft === 'saved' && $val05_warn ? 'warn_ok' : 'fail') . PHP_EOL;
print 'VAL-05_pub=' . (str_starts_with($val05_pub, 'blocked:') ? 'block_ok' : 'fail:' . $val05_pub) . PHP_EOL;
recette_clear_messenger();

// --- VAL-06 : PER_POSTE + capacité 0 ---
$val06_draft = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_asset_type', 'COW');
  $node->set('field_operation_type', 'LOC');
  $node->set('field_budget_unit', 'PER_POSTE');
  $node->set('field_capacity_mode', 'SEAT_BASED');
  $node->setUnpublished();
  $node->save();
});
$val06_warn = str_contains(recette_messenger_text(MessengerInterface::TYPE_WARNING), 'Capacity total');
recette_clear_messenger();
$val06_pub = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_asset_type', 'COW');
  $node->set('field_operation_type', 'LOC');
  $node->set('field_budget_unit', 'PER_POSTE');
  $node->set('field_capacity_mode', 'SEAT_BASED');
  $node->setPublished();
  $node->set('field_primary_agent', 1);
  $node->save();
});
print 'VAL-06_draft=' . ($val06_draft === 'saved' && $val06_warn ? 'warn_ok' : 'fail') . PHP_EOL;
print 'VAL-06_pub=' . (str_starts_with($val06_pub, 'blocked:') ? 'block_ok' : 'fail:' . $val06_pub) . PHP_EOL;
recette_clear_messenger();

// --- VAL-07 : budget 0 normalisé ---
$val07_node = recette_base_offer();
$val07_node->set('field_budget_value', 0);
$val07_node->set('field_budget_period', 'YEAR');
$val07_node->set('field_budget_unit', 'PER_M2');
$val07_result = recette_try_save(static function () use ($val07_node): void {
  $val07_node->save();
});
$val07_null = $val07_node->get('field_budget_value')->isEmpty();
print 'VAL-07=' . ($val07_result === 'saved' && $val07_null ? 'normalize_ok' : 'fail') . PHP_EOL;
recette_clear_messenger();

// --- VAL-08 : non divisible DISPO < TOTAL ---
recette_clear_messenger();
$val08_result = recette_try_save(static function (): void {
  $node = recette_base_offer();
  $node->set('field_divisible', 0);
  $node->set('field_surfaces', [
    ['qualification' => 'TOTAL', 'value' => 500, 'unit' => 'M2'],
    ['qualification' => 'DISPO', 'value' => 200, 'unit' => 'M2'],
  ]);
  $node->setUnpublished();
  $node->save();
});
$val08_warn = str_contains(recette_messenger_text(MessengerInterface::TYPE_WARNING), 'non-divisible');
print 'VAL-08=' . ($val08_result === 'saved' && $val08_warn ? 'warn_ok' : 'fail') . PHP_EOL;
recette_clear_messenger();

// --- VAL-09 : référence manuelle dupliquée ---
$storage = \Drupal::entityTypeManager()->getStorage('node');
$prefix = 'VAL-09-DUP-';
$query = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', $prefix, 'STARTS_WITH');
foreach ($storage->loadMultiple($query->execute()) as $old) {
  $old->delete();
}
$manual = 'REF-VAL09-DUP-' . time();
$first = Node::create(['type' => 'offer', 'title' => $prefix . 'A']);
$first->set('field_reference_auto', 0);
$first->set('field_reference', $manual);
$first->setUnpublished();
$first->save();
$second = Node::create(['type' => 'offer', 'title' => $prefix . 'B']);
$second->set('field_reference_auto', 0);
$second->set('field_reference', $manual);
$second->setUnpublished();
$val09 = recette_try_save(static function () use ($second): void {
  $second->save();
});
print 'VAL-09=' . (str_starts_with($val09, 'blocked:') ? 'block_ok' : 'fail:' . $val09) . PHP_EOL;
recette_clear_messenger();

// --- VAL-10 : même ref manuelle sur édition ---
$prefix10 = 'VAL-10-SELF-';
$query10 = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', $prefix10, 'STARTS_WITH');
foreach ($storage->loadMultiple($query10->execute()) as $old) {
  $old->delete();
}
$manual10 = 'REF-VAL10-SELF-' . time();
$self = Node::create(['type' => 'offer', 'title' => $prefix10 . 'A']);
$self->set('field_reference_auto', 0);
$self->set('field_reference', $manual10);
$self->setUnpublished();
$self->save();
$edit = $storage->load($self->id());
$edit->setTitle($prefix10 . 'A-edited');
$val10 = recette_try_save(static function () use ($edit, $manual10): void {
  $edit->set('field_reference_auto', 0);
  $edit->set('field_reference', $manual10);
  $edit->setUnpublished();
  $edit->save();
});
print 'VAL-10=' . ($val10 === 'saved' ? 'pass_ok' : 'fail:' . $val10) . PHP_EOL;
