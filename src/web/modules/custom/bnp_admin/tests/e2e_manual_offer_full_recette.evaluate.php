<?php

/**
 * @file
 * Manual recette §5.3 — OFF-01→12 données minimales + tentative publication.
 */

declare(strict_types=1);

use Drupal\Core\Entity\EntityStorageException;
use Drupal\node\Entity\Node;
use Drupal\ps_surface\Entity\SurfaceDivision;

/**
 * @return array<string, array{op: string, asset: string, div: string, surface: bool, capacity: bool, lots: bool}>
 */
function full_recette_offer_matrix(): array {
  return [
    'OFF-01' => ['op' => 'LOC', 'asset' => 'BUR', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-02' => ['op' => 'LOC', 'asset' => 'BUR', 'div' => '1', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => TRUE],
    'OFF-03' => ['op' => 'LOC', 'asset' => 'COW', 'div' => '0', 'surface' => FALSE, 'capacity' => TRUE, 'lots' => FALSE],
    'OFF-04' => ['op' => 'LOC', 'asset' => 'ENT', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-05' => ['op' => 'LOC', 'asset' => 'ACT', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-06' => ['op' => 'LOC', 'asset' => 'COM', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-07' => ['op' => 'LOC', 'asset' => 'TER', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-08' => ['op' => 'VEN', 'asset' => 'BUR', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-09' => ['op' => 'VEN', 'asset' => 'BUR', 'div' => '1', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => TRUE],
    'OFF-10' => ['op' => 'VEN', 'asset' => 'COW', 'div' => '0', 'surface' => FALSE, 'capacity' => TRUE, 'lots' => FALSE],
    'OFF-11' => ['op' => 'VEN', 'asset' => 'COM', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
    'OFF-12' => ['op' => 'VEN', 'asset' => 'TER', 'div' => '0', 'surface' => TRUE, 'capacity' => FALSE, 'lots' => FALSE],
  ];
}

/**
 * @return array{media: int, agent: int, cert: int}
 */
function full_recette_fixture_ids(): array {
  $media = (int) (reset(\Drupal::entityQuery('media')->accessCheck(FALSE)->range(0, 1)->execute()) ?: 0);
  $agent = (int) (reset(\Drupal::entityQuery('ps_agent')->accessCheck(FALSE)->range(0, 1)->execute()) ?: 0);
  $cert = (int) (reset(\Drupal::entityQuery('taxonomy_term')->accessCheck(FALSE)->condition('vid', 'certification_label')->range(0, 1)->execute()) ?: 0);
  return ['media' => $media, 'agent' => $agent, 'cert' => $cert];
}

/**
 * @param array{op: string, asset: string, div: string, surface: bool, capacity: bool, lots: bool} $case
 */
function full_recette_apply_minimal_data(Node $node, array $case, array $fixtures): void {
  $node->set('field_client_type', 'B2B');
  $node->set('field_operation_type', $case['op']);
  $node->set('field_asset_type', $case['asset']);
  $node->set('field_divisible', (int) $case['div']);
  $node->set('field_mandate_type', 'SIM');
  $node->set('field_reference_auto', 1);
  $node->set('field_budget_value', 150000);

  if ($case['op'] === 'LOC' && $case['asset'] === 'COW') {
    $node->set('field_budget_unit', 'PER_POSTE');
    $node->set('field_budget_period', 'YEAR');
  }
  elseif ($case['op'] === 'LOC') {
    $node->set('field_budget_unit', 'PER_M2');
    $node->set('field_budget_period', 'YEAR');
  }
  else {
    $node->set('field_budget_unit', 'GLOBAL');
    $node->set('field_budget_period', NULL);
  }

  if ($case['surface']) {
    $node->set('field_surfaces', [
      ['qualification' => 'TOTAL', 'value' => 500, 'unit' => 'M2'],
    ]);
  }

  if ($case['capacity']) {
    $node->set('field_capacity_mode', 'SEAT_BASED');
    $node->set('field_capacity_total', 20);
    $node->set('field_capacity_available', 15);
  }

  if ($case['lots']) {
    $division = SurfaceDivision::create([
      'division_reference' => 'LOT-' . $node->getTitle(),
      'division_label' => 'Lot 01',
      'surfaces' => [
        ['qualification' => 'TOTAL', 'value' => 200, 'unit' => 'M2'],
      ],
    ]);
    $division->save();
    $node->set('field_divisions', [['target_id' => $division->id()]]);
  }

  $node->set('field_address', [
    'country_code' => 'FR',
    'locality' => 'Paris',
    'postal_code' => '75001',
    'address_line1' => '1 Rue de Rivoli',
  ]);
  $node->set('field_geo', 'POINT(2.3522 48.8566)');
  $node->set('field_diagnostics', [
    [
      'diagnostic_type' => 'dpe',
      'diagnostic_class' => 'C',
      'diagnostic_value' => 120,
    ],
  ]);

  if ($fixtures['cert'] > 0) {
    $node->set('field_certification_labels', [['target_id' => $fixtures['cert']]]);
  }
  if ($fixtures['media'] > 0) {
    $node->set('field_media_gallery', [['target_id' => $fixtures['media']]]);
  }
  if ($fixtures['agent'] > 0) {
    $node->set('field_primary_agent', [['target_id' => $fixtures['agent']]]);
  }
}

$editor = reset(\Drupal::entityTypeManager()->getStorage('user')->loadByProperties(['name' => 'content.editor']));
if (!$editor) {
  print "FULL=fail_user\n";
  return;
}

$fixtures = full_recette_fixture_ids();
if ($fixtures['media'] === 0 || $fixtures['agent'] === 0) {
  print 'FULL=fail_fixtures:media=' . $fixtures['media'] . ',agent=' . $fixtures['agent'] . PHP_EOL;
  return;
}

\Drupal::service('account_switcher')->switchTo($editor);
\Drupal::messenger()->deleteAll();

try {
  foreach (full_recette_offer_matrix() as $case_id => $case) {
    $node = Node::create([
      'type' => 'offer',
      'title' => $case_id . '-FULL-' . time(),
      'langcode' => 'en',
      'status' => 1,
      'uid' => $editor->id(),
    ]);
    full_recette_apply_minimal_data($node, $case, $fixtures);

    try {
      $node->save();
      if ($node->isPublished()) {
        print $case_id . '_FULL=pass_pub,nid:' . $node->id() . PHP_EOL;
      }
      else {
        print $case_id . '_FULL=fail_draft,nid:' . $node->id() . PHP_EOL;
      }
    }
    catch (EntityStorageException $e) {
      if ($case['asset'] === 'COW' && str_contains($e->getMessage(), 'TOTAL surface')) {
        print $case_id . '_FULL=block_cow_surface,' . $e->getMessage() . PHP_EOL;
      }
      else {
        print $case_id . '_FULL=fail_pub,' . $e->getMessage() . PHP_EOL;
      }
    }
    catch (\Throwable $e) {
      print $case_id . '_FULL=fail_exception,' . $e->getMessage() . PHP_EOL;
    }
    \Drupal::messenger()->deleteAll();
  }
}
finally {
  \Drupal::service('account_switcher')->switchBack();
  \Drupal::messenger()->deleteAll();
}
