<?php

/**
 * @file
 * CLI test: save/reload each placement condition type.
 *
 * Usage (inside ps_php):
 *   vendor/bin/drush php:script web/modules/custom/views_promo_card/tests/test_conditions_save.php
 */

declare(strict_types=1);

use Drupal\Core\Form\FormState;
use Drupal\views_promo_card\Entity\PromoCardPlacement;

$placement_id = 'b2b_search_calc_row2';
$backup = PromoCardPlacement::load($placement_id);
if ($backup === NULL) {
  throw new RuntimeException("Placement {$placement_id} not found.");
}
$original_conditions = $backup->getConditions();
$original_logic = $backup->getConditionsLogic();

$cases = [
  'request_path' => [
    'id' => 'request_path',
    'negate' => FALSE,
    'pages' => "/find-property\n/admin",
  ],
  'user_role' => [
    'id' => 'user_role',
    'negate' => FALSE,
    'roles' => ['authenticated' => 'authenticated'],
  ],
  'promo_card_min_results' => [
    'id' => 'promo_card_min_results',
    'negate' => FALSE,
    'minimum' => 5,
  ],
  'promo_card_pager_page' => [
    'id' => 'promo_card_pager_page',
    'negate' => FALSE,
    'max_page' => 0,
  ],
  'promo_card_route_name' => [
    'id' => 'promo_card_route_name',
    'negate' => FALSE,
    'routes' => "view.ps_search_offers.page_list",
  ],
  'promo_card_request_parameter' => [
    'id' => 'promo_card_request_parameter',
    'negate' => FALSE,
    'parameter' => 'foo',
    'value' => 'bar',
  ],
  'promo_card_views_exposed_filter' => [
    'id' => 'promo_card_views_exposed_filter',
    'negate' => FALSE,
    'filter_id' => 'field_asset_type',
    'value' => 'office',
  ],
];

$errors = [];
foreach ($cases as $label => $config) {
  $entity = PromoCardPlacement::load($placement_id);
  $entity->set('conditions', [$config]);
  $entity->set('conditions_logic', 'and');
  $entity->save();

  $reloaded = PromoCardPlacement::load($placement_id);
  $saved = $reloaded->getConditions()[0] ?? [];
  if (($saved['id'] ?? '') !== $config['id']) {
    $errors[] = "{$label}: id mismatch after entity save";
    continue;
  }

  $form_object = \Drupal::entityTypeManager()->getFormObject('promo_card_placement', 'edit');
  $form_object->setEntity($reloaded);
  $form_state = new FormState();
  $form = \Drupal::formBuilder()->buildForm($form_object, $form_state);
  $section = $form['layout']['editor']['conditions_section'] ?? [];
  if (!isset($section[$config['id']])) {
    $errors[] = "{$label}: condition tab missing in form (keys: " . implode(', ', array_keys($section)) . ')';
    continue;
  }
  if (($section[$config['id']]['#group'] ?? '') !== 'conditions_tabs') {
    $errors[] = "{$label}: condition not grouped in vertical tabs";
    continue;
  }
  if (!isset($section['conditions_tabs'])) {
    $errors[] = "{$label}: vertical tabs element missing";
    continue;
  }

  echo "OK {$label}\n";
}

// Restore original.
$restore = PromoCardPlacement::load($placement_id);
$restore->set('conditions', $original_conditions);
$restore->set('conditions_logic', $original_logic);
$restore->save();

if ($errors !== []) {
  foreach ($errors as $error) {
    echo "FAIL {$error}\n";
  }
  throw new RuntimeException('Condition save tests failed.');
}

echo "All condition save/reload tests passed.\n";
