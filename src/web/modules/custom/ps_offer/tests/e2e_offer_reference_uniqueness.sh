#!/usr/bin/env bash
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

OP_CODE="${1:-LOC}"
ASSET_CODE="${2:-BUR}"
MODE="${3:-basic}"

run_eval() {
  local php_code="$1"
  ps_e2e_drush php:eval "$php_code"
}

run_eval_b64() {
  local php_code="$1"
  local encoded
  encoded="$(printf '%s' "$php_code" | base64 -w0)"
  ps_e2e_drush php:eval "eval(base64_decode('${encoded}'));"
}

echo "== PS Offer reference uniqueness E2E =="
echo "Scenario: operation=${OP_CODE}, asset=${ASSET_CODE}, mode=${MODE}"

# Ensure a default reference pattern exists.
run_eval '$storage=\Drupal::entityTypeManager()->getStorage("ps_offer_reference_pattern"); $pattern=$storage->load("default"); if (!$pattern) { $pattern=$storage->create(["id"=>"default","label"=>"Default offer reference pattern","status"=>TRUE,"weight"=>0,"target_bundles"=>["offer"],"allow_manual_override"=>TRUE,"require_uniqueness"=>TRUE,"validate_manual_value_against_pattern"=>TRUE,"generate_on_create"=>TRUE,"regenerate_on_source_change"=>TRUE,"counter_scope_mode"=>"prefix","segments"=>[["uuid"=>"seg-literal-o","label"=>"Offer type","type"=>"literal","weight"=>0,"length"=>1,"fallback_value"=>"O"],["uuid"=>"seg-op","label"=>"Operation code","type"=>"field_map","weight"=>10,"length"=>1,"source_field"=>"field_operation_type","resolution_mode"=>"manual_then_alias","mapping"=>["LOC"=>"L","VEN"=>"V","CESSION"=>"C"],"fallback_value"=>"L"],["uuid"=>"seg-asset","label"=>"Asset code","type"=>"field_map","weight"=>20,"length"=>3,"source_field"=>"field_asset_type","resolution_mode"=>"manual_then_alias","mapping"=>["BUR"=>"BUR","ACT"=>"ACT","ENT"=>"LOG","COM"=>"COM","COW"=>"COW"],"fallback_value"=>"BUR"],["uuid"=>"seg-year","label"=>"Year","type"=>"year_2_digits","weight"=>30,"length"=>2,"fallback_value"=>"00"],["uuid"=>"seg-counter","label"=>"Counter","type"=>"counter","weight"=>40,"length"=>5,"fallback_value"=>"1"]]]); $pattern->save(); print "pattern_created\n"; } else { print "pattern_exists\n"; }'

if [[ "${MODE}" == "parallel-roundtrip-two" ]]; then
  UNIQUENESS_CHECK_PHP=$(cat <<'PHP'
$operation = strtoupper((string) getenv('PS_OFFER_OP_CODE'));
$asset = strtoupper((string) getenv('PS_OFFER_ASSET_CODE'));

$node_type_storage = \Drupal::entityTypeManager()->getStorage('node_type');
if (!$node_type_storage->load('offer')) {
  print 'FAIL: offer content type missing';
  exit(1);
}

$node_storage = \Drupal::entityTypeManager()->getStorage('node');
$ids = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', 'E2E Ref Parallel ', 'STARTS_WITH')
  ->execute();
if (!empty($ids)) {
  $stale = $node_storage->loadMultiple($ids);
  foreach ($stale as $node) {
    $node->delete();
  }
}

$create_auto = static function (int $index) use ($node_storage, $operation, $asset): int {
  $node = $node_storage->create([
    'type' => 'offer',
    'title' => 'E2E Ref Parallel ' . $index,
  ]);

  if ($node->hasField('field_reference_auto')) {
    $node->set('field_reference_auto', 1);
  }
  if ($node->hasField('field_reference')) {
    $node->set('field_reference', '');
  }
  if ($node->hasField('field_operation_type')) {
    $node->set('field_operation_type', $operation);
  }
  if ($node->hasField('field_asset_type')) {
    $node->set('field_asset_type', $asset);
  }

  $node->setUnpublished();
  $node->save();

  return (int) $node->id();
};

$save_auto = static function (int $nid) use ($node_storage): string {
  $node = $node_storage->load($nid);
  if (!$node) {
    print 'FAIL: unable to load node during roundtrip';
    exit(1);
  }

  if ($node->hasField('field_reference_auto')) {
    $node->set('field_reference_auto', 1);
  }
  if ($node->hasField('field_reference')) {
    $node->set('field_reference', '');
  }
  $node->setUnpublished();
  $node->save();

  return (string) ($node->get('field_reference')->value ?? '');
};

$nidA = $create_auto(1);
$nidB = $create_auto(2);

$phase1A = $save_auto($nidA);
$phase1B = $save_auto($nidB);
$phase2A = $save_auto($nidA);
$phase2B = $save_auto($nidB);

$all = [$phase1A, $phase1B, $phase2A, $phase2B];
$non_empty = array_filter($all, static fn ($ref): bool => trim((string) $ref) !== '');
if (count($non_empty) !== 4) {
  print 'FAIL: at least one roundtrip reference is empty';
  exit(1);
}

if ($phase1A === $phase1B || $phase2A === $phase2B) {
  print 'FAIL: duplicate reference detected between two existing offers during roundtrip';
  exit(1);
}
print "PASS: parallel logical roundtrip keeps references unique between existing offers\n";

$op_map = ['LOC' => 'L', 'VEN' => 'V', 'CESSION' => 'C'];
$asset_map = ['BUR' => 'BUR', 'ACT' => 'ACT', 'ENT' => 'LOG', 'COM' => 'COM', 'COW' => 'COW'];
$expected_prefix = 'O' . ($op_map[$operation] ?? 'L') . ($asset_map[$asset] ?? 'BUR');

foreach ($all as $ref) {
  $regex = '/^' . preg_quote($expected_prefix, '/') . '[0-9]{7}$/';
  if (preg_match($regex, $ref) !== 1) {
    print 'FAIL: roundtrip reference does not match expected prefix format';
    exit(1);
  }
}
print "PASS: parallel logical roundtrip references follow expected prefix format\n";

print 'RESULT:' . implode(',', $all);
PHP
)
else
  UNIQUENESS_CHECK_PHP=$(cat <<'PHP'
$operation = strtoupper((string) getenv('PS_OFFER_OP_CODE'));
$asset = strtoupper((string) getenv('PS_OFFER_ASSET_CODE'));

$node_type_storage = \Drupal::entityTypeManager()->getStorage('node_type');
if (!$node_type_storage->load('offer')) {
  print 'FAIL: offer content type missing';
  exit(1);
}

$node_storage = \Drupal::entityTypeManager()->getStorage('node');
$ids = \Drupal::entityQuery('node')
  ->accessCheck(FALSE)
  ->condition('type', 'offer')
  ->condition('title', 'E2E Ref Uniqueness ', 'STARTS_WITH')
  ->execute();
if (!empty($ids)) {
  $stale = $node_storage->loadMultiple($ids);
  foreach ($stale as $node) {
    $node->delete();
  }
}

$create_auto = static function (int $index) use ($node_storage, $operation, $asset): string {
  $node = $node_storage->create([
    'type' => 'offer',
    'title' => 'E2E Ref Uniqueness ' . $index,
  ]);

  if ($node->hasField('field_reference_auto')) {
    $node->set('field_reference_auto', 1);
  }
  if ($node->hasField('field_reference')) {
    $node->set('field_reference', '');
  }
  if ($node->hasField('field_operation_type')) {
    $node->set('field_operation_type', $operation);
  }
  if ($node->hasField('field_asset_type')) {
    $node->set('field_asset_type', $asset);
  }

  $node->setUnpublished();
  $node->save();

  return (string) ($node->get('field_reference')->value ?? '');
};

$refs = [
  $create_auto(1),
  $create_auto(2),
  $create_auto(3),
];

$non_empty = array_filter($refs, static fn ($ref): bool => trim((string) $ref) !== '');
if (count($non_empty) !== 3) {
  print 'FAIL: at least one generated reference is empty';
  exit(1);
}
if (count(array_unique($refs)) !== 3) {
  print 'FAIL: duplicate reference detected under logical concurrency';
  exit(1);
}
print "PASS: logical concurrency uniqueness across 3 auto-generated references\n";

$op_map = ['LOC' => 'L', 'VEN' => 'V', 'CESSION' => 'C'];
$asset_map = ['BUR' => 'BUR', 'ACT' => 'ACT', 'ENT' => 'LOG', 'COM' => 'COM', 'COW' => 'COW'];
$expected_prefix = 'O' . ($op_map[$operation] ?? 'L') . ($asset_map[$asset] ?? 'BUR');

foreach ($refs as $ref) {
  $regex = '/^' . preg_quote($expected_prefix, '/') . '[0-9]{7}$/';
  if (preg_match($regex, $ref) !== 1) {
    print 'FAIL: generated reference does not match expected prefix format';
    exit(1);
  }
}
print "PASS: logical concurrency references follow expected prefix format\n";

$counters = array_map(static fn (string $ref): int => (int) substr($ref, -5), $refs);
if (!($counters[0] < $counters[1] && $counters[1] < $counters[2])) {
  print 'FAIL: generated counters are not strictly increasing';
  exit(1);
}
print "PASS: logical concurrency counters are strictly increasing\n";
print 'RESULT:' . implode(',', $refs);
PHP
)
fi

PS_OFFER_OP_CODE="${OP_CODE}" PS_OFFER_ASSET_CODE="${ASSET_CODE}" run_eval_b64 "${UNIQUENESS_CHECK_PHP}"

echo "== Uniqueness E2E completed successfully =="