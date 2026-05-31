#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

BATCH_SIZE="${BATCH_SIZE:-200}"
MONITOR_INTERVAL="${MONITOR_INTERVAL:-20}"
XML_SOURCE_PATH="${XML_SOURCE_PATH:-${PS_PROJECT_ROOT}/data/xml/bnppre_all_fr.xml}"
CRM_TARGET_PATH="${CRM_TARGET_PATH:-${PS_DRUPAL_ROOT}/web/sites/default/files/crm/offers.xml}"
IMPORT_XML_DICTIONARIES="${IMPORT_XML_DICTIONARIES:-0}"
SKIP_DICTIONARY_CSV_IMPORT="${SKIP_DICTIONARY_CSV_IMPORT:-0}"
PUBLISH_VALID_OFFERS_AFTER_IMPORT="${PUBLISH_VALID_OFFERS_AFTER_IMPORT:-1}"
KILL_EXISTING_IMPORTS="${KILL_EXISTING_IMPORTS:-0}"
SKIP_STATIC_PREIMPORTS="${SKIP_STATIC_PREIMPORTS:-0}"
SKIP_AGENT_IMPORTS="${SKIP_AGENT_IMPORTS:-0}"
SKIP_MEDIA_IMPORTS="${SKIP_MEDIA_IMPORTS:-0}"
SKIP_DIVISION_IMPORTS="${SKIP_DIVISION_IMPORTS:-0}"
MAX_BATCHES="${MAX_BATCHES:-0}"

usage() {
  cat <<'EOF'
Usage: src/scripts/main.sh drupal import-crm-bulk

Environment variables:
  BATCH_SIZE                    Number of BUSINESS_ID per offer batch (default: 200)
  MONITOR_INTERVAL              Seconds between monitor snapshots (default: 20)
  XML_SOURCE_PATH               Host path to source XML (default: data/xml/bnppre_all_fr.xml)
  CRM_TARGET_PATH               Container target XML path (default: /var/www/html/web/sites/default/files/crm/offers.xml)
  IMPORT_XML_DICTIONARIES       1=run XML dictionary migrations, 0=skip (default: 0)
  SKIP_DICTIONARY_CSV_IMPORT    1=skip CSV dictionary import (default: 0)
  PUBLISH_VALID_OFFERS_AFTER_IMPORT 1=publish valid offers (default: 1)
  KILL_EXISTING_IMPORTS         1=terminate existing migrate:import drush processes before start (default: 0)
  SKIP_STATIC_PREIMPORTS        1=skip groups/definitions/agents/media/divisions pre-import phase (default: 0)
  SKIP_AGENT_IMPORTS            1=skip avatar+agent migrations in pre-import phase (default: 0)
  SKIP_MEDIA_IMPORTS            1=skip file+media migrations in pre-import phase (default: 0)
  SKIP_DIVISION_IMPORTS         1=skip division migration in pre-import phase (default: 0)
  MAX_BATCHES                   0=all batches, >0=stop after N offer batches (benchmark mode)
EOF
}

if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
  usage
  exit 0
fi

ensure_no_concurrent_import() {
  local running_pids
  running_pids="$(docker top "${PS_PHP_CONTAINER}" 2>/dev/null | awk '/drush\.php migrate:import/ {print $2}')"

  if [[ -z "${running_pids}" ]]; then
    return 0
  fi

  if [[ "${KILL_EXISTING_IMPORTS}" == "1" ]]; then
    ps_warn "Terminating existing migrate:import processes: ${running_pids}"
    # PIDs from `docker top` are host PIDs.
    kill ${running_pids} >/dev/null 2>&1 || true
    sleep 2
    return 0
  fi

  ps_die "Concurrent migrate:import process detected (PIDs: ${running_pids}). Re-run with KILL_EXISTING_IMPORTS=1 to stop them first."
}

run_drush_with_monitoring() {
  local label="$1"
  shift

  local start_ts now last_tick=0
  start_ts="$(ps_now_epoch)"
  ps_info "START: ${label}"

  ps_drush "$@" &
  local cmd_pid=$!

  while kill -0 "${cmd_pid}" >/dev/null 2>&1; do
    now="$(ps_now_epoch)"
    if (( now - last_tick >= MONITOR_INTERVAL )); then
      local stats_line drush_line
      stats_line="$(docker stats --no-stream --format '{{.Name}} cpu={{.CPUPerc}} mem={{.MemUsage}} pids={{.PIDs}}' "${PS_PHP_CONTAINER}" 2>/dev/null || true)"
      drush_line="$(docker top "${PS_PHP_CONTAINER}" 2>/dev/null | awk '/drush\.php migrate:import/ {print "C=" $4 " TIME=" $7 " CMD=" $8; exit}' || true)"
      ps_info "[monitor] ${label} elapsed=$((now - start_ts))s ${stats_line} ${drush_line}"
      last_tick="${now}"
    fi
    sleep 5
  done

  set +e
  wait "${cmd_pid}"
  local rc=$?
  set -e

  if (( rc != 0 )); then
    ps_error "${label} failed with exit code ${rc}"
    return "${rc}"
  fi

  ps_info "END: ${label} (elapsed: $(ps_elapsed_seconds "${start_ts}")s)"
}

collect_business_ids() {
  local xml_path="$1"
  local ids_file="$2"

  grep -o '<BUSINESS_ID>[^<]*</BUSINESS_ID>' "${xml_path}" \
    | sed -e 's#<BUSINESS_ID>##' -e 's#</BUSINESS_ID>##' \
    | awk 'NF > 0' > "${ids_file}"
}

iter_id_batches() {
  local ids_file="$1"
  local batch_size="$2"
  awk -v size="${batch_size}" '
    {
      idx = (NR - 1) % size
      if (idx == 0) {
        if (NR > 1) print line
        line = $0
      }
      else {
        line = line "," $0
      }
    }
    END {
      if (NR > 0) print line
    }
  ' "${ids_file}"
}

ps_header "CRM Import (Bulk Mode)"
ps_require_cmd docker
ps_require_container_running "${PS_PHP_CONTAINER}"
ps_require_file "${XML_SOURCE_PATH}"
ensure_no_concurrent_import

if ! [[ "${BATCH_SIZE}" =~ ^[0-9]+$ ]] || (( BATCH_SIZE <= 0 )); then
  ps_die "BATCH_SIZE must be a positive integer (current: ${BATCH_SIZE})"
fi

if ! [[ "${MONITOR_INTERVAL}" =~ ^[0-9]+$ ]] || (( MONITOR_INTERVAL <= 0 )); then
  ps_die "MONITOR_INTERVAL must be a positive integer (current: ${MONITOR_INTERVAL})"
fi

if ! [[ "${MAX_BATCHES}" =~ ^[0-9]+$ ]] || (( MAX_BATCHES < 0 )); then
  ps_die "MAX_BATCHES must be an integer >= 0 (current: ${MAX_BATCHES})"
fi

TMP_DIR="$(mktemp -d)"
trap 'rm -rf "${TMP_DIR}"' EXIT
IDS_FILE="${TMP_DIR}/business_ids.txt"

ps_info "Bulk source XML: ${XML_SOURCE_PATH}"
ps_info "Batch size: ${BATCH_SIZE}"
ps_info "Monitor interval: ${MONITOR_INTERVAL}s"

host_offer_count="$(grep -c '<OFFER>' "${XML_SOURCE_PATH}" || true)"
ps_info "Source offer count (host): ${host_offer_count}"

ps_timed_run "Copy XML to Drupal public://crm/offers.xml" ps_retry 2 2 docker exec -i "${PS_PHP_CONTAINER}" sh -lc "mkdir -p \"$(dirname "${CRM_TARGET_PATH}")\""
ps_timed_run "Sync XML into container" ps_retry 2 2 docker cp "${XML_SOURCE_PATH}" "${PS_PHP_CONTAINER}:${CRM_TARGET_PATH}"

container_offer_count="$(docker exec -i "${PS_PHP_CONTAINER}" sh -lc "grep -c '<OFFER>' '${CRM_TARGET_PATH}'" || true)"
ps_info "Source offer count (container): ${container_offer_count}"

ps_timed_run "Enable migrate modules" ps_retry 2 2 ps_drush en migrate migrate_plus migrate_tools ps_migrate ps_dictionary -y

ps_timed_run "Ensure migration config" ps_retry 2 2 ps_drush scr scripts/tools/migrate-config.php

for migration_id in \
  ps_dictionary_asset_type_from_xml \
  ps_dictionary_operation_type_from_xml \
  ps_feature_groups_from_xml \
  ps_feature_definitions_from_xml \
  ps_agent_avatar_file_from_xml \
  ps_agent_from_xml \
  ps_file_from_xml \
  ps_media_from_xml \
  ps_surface_division_from_xml \
  ps_offer_from_xml
do
  ps_drush migrate:reset-status "${migration_id}" -y >/dev/null 2>&1 || true
done

if [[ "${IMPORT_XML_DICTIONARIES}" == "1" ]]; then
  run_drush_with_monitoring "Import dictionary asset_type from XML" migrate:import ps_dictionary_asset_type_from_xml -y
  run_drush_with_monitoring "Import dictionary operation_type from XML" migrate:import ps_dictionary_operation_type_from_xml -y
  ps_timed_run "Cleanup operation_type legacy codes" ps_retry 2 2 ps_drush scr scripts/tools/cleanup-legacy.php
else
  ps_warn "XML dictionary migrations skipped (IMPORT_XML_DICTIONARIES=0)"
fi

if [[ "${SKIP_DICTIONARY_CSV_IMPORT}" == "1" ]]; then
  ps_warn "CSV dictionary sync skipped"
else
  ps_timed_run "Import dictionary CSV" ps_retry 2 2 ps_drush ps:dictionary:import -y
fi

if [[ "${SKIP_STATIC_PREIMPORTS}" == "1" ]]; then
  ps_warn "Static pre-import phase skipped (SKIP_STATIC_PREIMPORTS=1)"
else
  run_drush_with_monitoring "Import feature groups" migrate:import ps_feature_groups_from_xml -y
  run_drush_with_monitoring "Import feature definitions" migrate:import ps_feature_definitions_from_xml -y

  if [[ "${SKIP_AGENT_IMPORTS}" == "1" ]]; then
    ps_warn "Agent pre-imports skipped (SKIP_AGENT_IMPORTS=1)"
  else
    run_drush_with_monitoring "Import agent avatars" migrate:import ps_agent_avatar_file_from_xml -y
    run_drush_with_monitoring "Import agents" migrate:import ps_agent_from_xml -y
  fi

  if [[ "${SKIP_MEDIA_IMPORTS}" == "1" ]]; then
    ps_warn "Media pre-imports skipped (SKIP_MEDIA_IMPORTS=1)"
  else
    run_drush_with_monitoring "Import files" migrate:import ps_file_from_xml -y
    run_drush_with_monitoring "Import media" migrate:import ps_media_from_xml -y
  fi

  if [[ "${SKIP_DIVISION_IMPORTS}" == "1" ]]; then
    ps_warn "Division pre-import skipped (SKIP_DIVISION_IMPORTS=1)"
  else
    run_drush_with_monitoring "Import divisions" migrate:import ps_surface_division_from_xml -y
  fi
fi

collect_business_ids "${XML_SOURCE_PATH}" "${IDS_FILE}"
total_ids="$(wc -l < "${IDS_FILE}" | tr -d ' ')"
if [[ "${total_ids}" == "0" ]]; then
  ps_die "No BUSINESS_ID found in ${XML_SOURCE_PATH}"
fi

total_batches="$(( (total_ids + BATCH_SIZE - 1) / BATCH_SIZE ))"
ps_info "Total BUSINESS_ID extracted: ${total_ids}"
ps_info "Total batches to import: ${total_batches}"

ps_timed_run "Set projection bypass state" ps_drush state:set ps_offer.skip_projection 1 --input-format=integer

batch_index=0
while IFS= read -r idlist; do
  batch_index=$((batch_index + 1))

  if (( MAX_BATCHES > 0 && batch_index > MAX_BATCHES )); then
    ps_warn "Stopping early after ${MAX_BATCHES} batches (benchmark mode)"
    break
  fi

  batch_label="Import offers batch ${batch_index}/${total_batches}"
  run_drush_with_monitoring "${batch_label}" migrate:import ps_offer_from_xml --idlist="${idlist}" -y
done < <(iter_id_batches "${IDS_FILE}" "${BATCH_SIZE}")

ps_timed_run "Unset projection bypass state" ps_drush state:delete ps_offer.skip_projection

if [[ "${PUBLISH_VALID_OFFERS_AFTER_IMPORT}" == "1" ]]; then
  ps_timed_run "Publish valid imported offers" ps_retry 2 2 ps_drush scr scripts/tools/publish-valid-offers.php
else
  ps_warn "Post-import publication skipped (PUBLISH_VALID_OFFERS_AFTER_IMPORT=0)"
fi

run_drush_with_monitoring "Sync and index search features" ps:search:features:sync-index --rebuild-tracker=1

ps_success "Bulk CRM import completed"
ps_drush migrate:status --group=ps_crm_import