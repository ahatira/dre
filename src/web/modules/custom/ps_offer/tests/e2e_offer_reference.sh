#!/usr/bin/env bash
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

NID="${1:-1}"
MANUAL_REF="REF-E2E-MANUAL-001"
OP_CODE="${2:-LOC}"
ASSET_CODE="${3:-BUR}"

run_eval() {
  local php_code="$1"
  ps_e2e_drush php:eval "$php_code"
}

assert_eq() {
  local expected="$1"
  local actual="$2"
  local message="$3"
  if [[ "$expected" != "$actual" ]]; then
    echo "FAIL: ${message} (expected='${expected}', actual='${actual}')"
    exit 1
  fi
  echo "PASS: ${message}"
}

assert_regex() {
  local regex="$1"
  local value="$2"
  local message="$3"
  if [[ ! "$value" =~ $regex ]]; then
    echo "FAIL: ${message} (value='${value}', regex='${regex}')"
    exit 1
  fi
  echo "PASS: ${message}"
}

echo "== PS Offer reference E2E (node ${NID}) =="
echo "Scenario: operation=${OP_CODE}, asset=${ASSET_CODE}"

# Ensure a default reference pattern exists.
run_eval '$storage=\Drupal::entityTypeManager()->getStorage("ps_offer_reference_pattern"); $pattern=$storage->load("default"); if (!$pattern) { $pattern=$storage->create(["id"=>"default","label"=>"Default offer reference pattern","status"=>TRUE,"weight"=>0,"target_bundles"=>["offer"],"allow_manual_override"=>TRUE,"require_uniqueness"=>TRUE,"validate_manual_value_against_pattern"=>TRUE,"generate_on_create"=>TRUE,"regenerate_on_source_change"=>TRUE,"counter_scope_mode"=>"prefix","segments"=>[["uuid"=>"seg-literal-o","label"=>"Offer type","type"=>"literal","weight"=>0,"length"=>1,"fallback_value"=>"O"],["uuid"=>"seg-op","label"=>"Operation code","type"=>"field_map","weight"=>10,"length"=>1,"source_field"=>"field_operation_type","resolution_mode"=>"manual_then_alias","mapping"=>["LOC"=>"L","VEN"=>"V","CESSION"=>"C"],"fallback_value"=>"L"],["uuid"=>"seg-asset","label"=>"Asset code","type"=>"field_map","weight"=>20,"length"=>3,"source_field"=>"field_asset_type","resolution_mode"=>"manual_then_alias","mapping"=>["BUR"=>"BUR","ACT"=>"ACT","ENT"=>"LOG","COM"=>"COM","COW"=>"COW"],"fallback_value"=>"BUR"],["uuid"=>"seg-year","label"=>"Year","type"=>"year_2_digits","weight"=>30,"length"=>2,"fallback_value"=>"00"],["uuid"=>"seg-counter","label"=>"Counter","type"=>"counter","weight"=>40,"length"=>5,"fallback_value"=>"1"]]]); $pattern->save(); print "pattern_created\n"; } else { print "pattern_exists\n"; }'

# Ensure a media item exists for required gallery validation.
MEDIA_ID="$(ps_e2e_drush php:eval '$storage=\Drupal::entityTypeManager()->getStorage("media"); $ids=\Drupal::entityQuery("media")->accessCheck(FALSE)->range(0,1)->execute(); if ($ids) { print (string) reset($ids); return; } $m=$storage->create(["bundle"=>"mediahub_video","name"=>"E2E Auto Generated Media","field_media_video_url"=>"https://example.com/video.mp4","status"=>1]); $m->save(); print (string) $m->id();')"

echo "Using media id: ${MEDIA_ID}"

# Manual mode scenario.
run_eval '$nid=(int) "'"${NID}"'"; $node=\Drupal\node\Entity\Node::load($nid); if (!$node) { throw new \RuntimeException("Node not found: " . $nid); } $node->set("field_reference_auto", 0); $node->set("field_reference", "'"${MANUAL_REF}"'"); if ($node->hasField("field_operation_type")) { $node->set("field_operation_type", "'"${OP_CODE}"'"); } if ($node->hasField("field_asset_type")) { $node->set("field_asset_type", "'"${ASSET_CODE}"'"); } if ($node->hasField("field_media_gallery")) { $node->set("field_media_gallery", [["target_id" => (int) "'"${MEDIA_ID}"'"]]); } $node->save(); print "manual_saved\n";'

MANUAL_DATA="$(ps_e2e_drush php:eval '$nid=(int) "'"${NID}"'"; $node=\Drupal\node\Entity\Node::load($nid); if (!$node) { throw new \RuntimeException("Node not found: " . $nid); } print "ref=".($node->get("field_reference")->value ?? "")."\n"; print "auto=".(string) ((int) $node->get("field_reference_auto")->value)."\n"; print "gallery_count=".(string) count($node->get("field_media_gallery"))."\n";')"

MANUAL_REF_DB="$(echo "$MANUAL_DATA" | sed -n 's/^ref=//p' | head -n1)"
MANUAL_AUTO_DB="$(echo "$MANUAL_DATA" | sed -n 's/^auto=//p' | head -n1)"
MANUAL_GALLERY_DB="$(echo "$MANUAL_DATA" | sed -n 's/^gallery_count=//p' | head -n1)"

assert_eq "${MANUAL_REF}" "${MANUAL_REF_DB}" "manual mode persisted reference"
assert_eq "0" "${MANUAL_AUTO_DB}" "manual mode persisted auto=0"
assert_regex '^[1-9][0-9]*$' "${MANUAL_GALLERY_DB}" "gallery has at least one media after manual save"

# Auto mode scenario.
run_eval '$nid=(int) "'"${NID}"'"; $node=\Drupal\node\Entity\Node::load($nid); if (!$node) { throw new \RuntimeException("Node not found: " . $nid); } $node->set("field_reference_auto", 1); $node->set("field_reference", ""); if ($node->hasField("field_operation_type")) { $node->set("field_operation_type", "'"${OP_CODE}"'"); } if ($node->hasField("field_asset_type")) { $node->set("field_asset_type", "'"${ASSET_CODE}"'"); } if ($node->hasField("field_media_gallery")) { $node->set("field_media_gallery", [["target_id" => (int) "'"${MEDIA_ID}"'"]]); } $node->save(); print "auto_saved\n";'

AUTO_DATA="$(ps_e2e_drush php:eval '$nid=(int) "'"${NID}"'"; $node=\Drupal\node\Entity\Node::load($nid); if (!$node) { throw new \RuntimeException("Node not found: " . $nid); } print "ref=".($node->get("field_reference")->value ?? "")."\n"; print "auto=".(string) ((int) $node->get("field_reference_auto")->value)."\n";')"

AUTO_REF_DB="$(echo "$AUTO_DATA" | sed -n 's/^ref=//p' | head -n1)"
AUTO_AUTO_DB="$(echo "$AUTO_DATA" | sed -n 's/^auto=//p' | head -n1)"

assert_eq "1" "${AUTO_AUTO_DB}" "auto mode persisted auto=1"
assert_regex '^[A-Z0-9]{8,20}$' "${AUTO_REF_DB}" "auto mode generated a structured reference"

EXPECTED_OP_ALIAS=""
case "${OP_CODE}" in
  LOC) EXPECTED_OP_ALIAS="L" ;;
  VEN) EXPECTED_OP_ALIAS="V" ;;
  CESSION) EXPECTED_OP_ALIAS="C" ;;
  *) EXPECTED_OP_ALIAS="L" ;;
esac

EXPECTED_ASSET_ALIAS=""
case "${ASSET_CODE}" in
  BUR) EXPECTED_ASSET_ALIAS="BUR" ;;
  ACT) EXPECTED_ASSET_ALIAS="ACT" ;;
  ENT) EXPECTED_ASSET_ALIAS="LOG" ;;
  COM) EXPECTED_ASSET_ALIAS="COM" ;;
  COW) EXPECTED_ASSET_ALIAS="COW" ;;
  *) EXPECTED_ASSET_ALIAS="BUR" ;;
esac

EXPECTED_PREFIX="O${EXPECTED_OP_ALIAS}${EXPECTED_ASSET_ALIAS}"
assert_regex "^${EXPECTED_PREFIX}[0-9]{7}$" "${AUTO_REF_DB}" "auto mode generated prefix matches operation/asset mapping"

if [[ "${AUTO_REF_DB}" == "${MANUAL_REF}" ]]; then
  echo "FAIL: auto mode did not regenerate reference"
  exit 1
fi

echo "PASS: auto mode regenerated reference (${AUTO_REF_DB})"
echo "== E2E completed successfully =="
