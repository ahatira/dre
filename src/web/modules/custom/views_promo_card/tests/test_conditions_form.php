<?php

/**
 * @file
 * CLI test: configure, modify, clear each placement condition via form API.
 */

declare(strict_types=1);

use Drupal\Core\Form\FormState;
use Drupal\views_promo_card\Entity\PromoCardPlacement;

$placement_id = 'b2b_search_calc_row2';
$entity = PromoCardPlacement::load($placement_id);
if ($entity === NULL) {
  throw new RuntimeException("Placement {$placement_id} not found.");
}

$original_conditions = $entity->getConditions();
$original_logic = $entity->getConditionsLogic();

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = \Drupal::requestStack()->getCurrentRequest();
$request->setMethod('POST');

$form_object = \Drupal::entityTypeManager()->getFormObject('promo_card_placement', 'edit');

$condition_configs = [
  'request_path' => [
    'pages' => '/test-path',
    'negate' => 0,
  ],
  'user_role' => [
    'roles' => ['authenticated' => 'authenticated'],
  ],
  'language' => [
    'langcodes' => ['fr' => 'fr'],
  ],
  'promo_card_min_results' => [
    'minimum' => 3,
    'negate' => 0,
  ],
  'promo_card_pager_page' => [
    'max_page' => 1,
    'negate' => 0,
  ],
  'promo_card_route_name' => [
    'routes' => 'view.ps_search_offers.page_list',
    'negate' => 0,
  ],
  'promo_card_request_parameter' => [
    'parameter' => 'utm_source',
    'value' => 'newsletter',
    'negate' => 0,
  ],
  'promo_card_views_exposed_filter' => [
    'filter_id' => 'asset_type',
    'value' => 'BUR',
    'negate' => 0,
  ],
];

/**
 * Submits the placement form with the given conditions section values.
 */
function submit_conditions_form(object $form_object, array $section_values): void {
  $form_state = new FormState();
  $form_state->setMethod('POST');
  $form = \Drupal::formBuilder()->buildForm($form_object, $form_state);

  $submit_values = $form_state->getValues();
  $submit_values['layout']['editor']['conditions_section'] = $section_values + [
    'conditions_logic' => 'and',
  ];
  $form_state->setValues($submit_values);
  $form_state->setUserInput($submit_values);
  $form_state->setSubmitted();
  $form_state->setTriggeringElement($form['actions']['submit']);
  \Drupal::formBuilder()->submitForm($form_object, $form_state);
  $form_object->save($form, $form_state);

  if ($form_state->hasAnyErrors()) {
    throw new RuntimeException('Validation errors: ' . implode('; ', array_keys($form_state->getErrors())));
  }
}

/**
 * Builds empty submitted values for a condition type.
 */
function build_empty_condition_values(string $condition_id): array {
  return match ($condition_id) {
    'request_path' => ['pages' => '', 'negate' => 0],
    'user_role' => ['roles' => []],
    'language' => ['langcodes' => []],
    'promo_card_min_results' => ['minimum' => ''],
    'promo_card_pager_page' => ['max_page' => ''],
    'promo_card_route_name' => ['routes' => '', 'negate' => 0],
    'promo_card_request_parameter' => ['parameter' => '', 'value' => '', 'negate' => 0],
    'promo_card_views_exposed_filter' => ['filter_id' => '', 'value' => '', 'negate' => 0],
    default => [],
  };
}

/**
 * Builds conditions section values with one active condition.
 */
function build_section_for_condition(string $active_id, array $configs, array $values): array {
  $section = ['conditions_logic' => 'and'];
  foreach ($configs as $condition_id => $defaults) {
    if ($condition_id === $active_id) {
      $section[$condition_id] = $values;
    }
  }
  return $section;
}

$errors = [];

foreach ($condition_configs as $condition_id => $values) {
  if ($condition_id === 'language' && !\Drupal::languageManager()->isMultilingual()) {
    continue;
  }

  $test_entity = PromoCardPlacement::load($placement_id);
  $test_entity->set('conditions', []);
  $test_entity->save();

  $form_object->setEntity($test_entity);
  $form_state = new FormState();
  $form_state->setMethod('POST');
  $form = \Drupal::formBuilder()->buildForm($form_object, $form_state);
  if (!isset($form['layout']['editor']['conditions_section'][$condition_id])) {
    $errors[] = "{$condition_id}: tab missing on form";
    continue;
  }

  try {
    submit_conditions_form(
      $form_object,
      build_section_for_condition($condition_id, $condition_configs, $values),
    );
  }
  catch (RuntimeException $e) {
    $errors[] = "{$condition_id}: configure failed — " . $e->getMessage();
    continue;
  }

  $saved = PromoCardPlacement::load($placement_id);
  $conditions = $saved->getConditions();
  if (count($conditions) !== 1 || ($conditions[0]['id'] ?? '') !== $condition_id) {
    $errors[] = "{$condition_id}: not persisted after configure (got " . json_encode($conditions) . ')';
    continue;
  }

  // Modify.
  $modify_values = $values;
  if ($condition_id === 'request_path') {
    $modify_values['pages'] = '/modified-path';
    $modify_values['negate'] = 1;
  }
  elseif ($condition_id === 'user_role') {
    $modify_values['roles'] = [
      'authenticated' => 'authenticated',
      'administrator' => 'administrator',
    ];
  }
  elseif ($condition_id === 'language') {
    $modify_values['langcodes'] = ['en' => 'en'];
  }
  elseif ($condition_id === 'promo_card_min_results') {
    $modify_values['minimum'] = 10;
    $modify_values['negate'] = 1;
  }
  elseif ($condition_id === 'promo_card_pager_page') {
    $modify_values['max_page'] = 2;
  }
  elseif ($condition_id === 'promo_card_route_name') {
    $modify_values['routes'] = 'entity.node.canonical';
  }
  elseif ($condition_id === 'promo_card_request_parameter') {
    $modify_values['parameter'] = 'ref';
    $modify_values['value'] = '';
  }
  elseif ($condition_id === 'promo_card_views_exposed_filter') {
    $modify_values['filter_id'] = 'field_city';
    $modify_values['value'] = 'paris';
  }

  $form_object->setEntity($saved);
  try {
    submit_conditions_form(
      $form_object,
      build_section_for_condition($condition_id, $condition_configs, $modify_values),
    );
  }
  catch (RuntimeException $e) {
    $errors[] = "{$condition_id}: modify failed — " . $e->getMessage();
    continue;
  }

  // Clear configured fields (block-style: empty fields are not persisted).
  $form_object->setEntity(PromoCardPlacement::load($placement_id));
  try {
    submit_conditions_form(
      $form_object,
      build_section_for_condition(
        $condition_id,
        $condition_configs,
        build_empty_condition_values($condition_id),
      ),
    );
  }
  catch (RuntimeException $e) {
    $errors[] = "{$condition_id}: clear failed — " . $e->getMessage();
    continue;
  }

  $after_clear = PromoCardPlacement::load($placement_id)->getConditions();
  if ($after_clear !== []) {
    $errors[] = "{$condition_id}: still present after clear (got " . json_encode($after_clear) . ')';
    continue;
  }

  echo "OK {$condition_id} (configure/modify/clear)\n";
}

$restore = PromoCardPlacement::load($placement_id);
$restore->set('conditions', $original_conditions);
$restore->set('conditions_logic', $original_logic);
$restore->save();

if ($errors !== []) {
  foreach ($errors as $error) {
    echo "FAIL {$error}\n";
  }
  throw new RuntimeException('Condition form tests failed.');
}

echo "All condition form configure/modify/clear tests passed.\n";
