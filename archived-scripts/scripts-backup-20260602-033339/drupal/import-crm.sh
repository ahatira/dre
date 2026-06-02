#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

SKIP_DICTIONARY_CSV_IMPORT="${SKIP_DICTIONARY_CSV_IMPORT:-0}"
PUBLISH_VALID_OFFERS_AFTER_IMPORT="${PUBLISH_VALID_OFFERS_AFTER_IMPORT:-1}"

ps_header "CRM Import"
ps_require_cmd docker
ps_require_container_running "${PS_PHP_CONTAINER}"

ps_timed_run "Enable migrate modules" ps_retry 2 2 ps_drush en migrate migrate_plus migrate_tools ps_migrate ps_dictionary -y

if ! ps_timed_run "Ensure migration config" ps_retry 2 2 ps_drush scr scripts/tools/migrate-config.php; then
  ps_warn "Unable to ensure migration config"
fi
if ! ps_timed_run "Import dictionary asset_type from XML" ps_retry 2 2 ps_drush migrate:import ps_dictionary_asset_type_from_xml -y; then
  ps_warn "asset_type XML migration failed"
fi
if ! ps_timed_run "Import dictionary operation_type from XML" ps_retry 2 2 ps_drush migrate:import ps_dictionary_operation_type_from_xml -y; then
  ps_warn "operation_type XML migration failed"
fi
if ! ps_timed_run "Cleanup operation_type legacy codes" ps_retry 2 2 ps_drush scr scripts/tools/cleanup-legacy.php; then
  ps_warn "operation_type legacy cleanup failed"
fi

if [[ "${SKIP_DICTIONARY_CSV_IMPORT}" == "1" ]]; then
  ps_warn "CSV dictionary sync skipped"
else
  if ! ps_timed_run "Import dictionary CSV" ps_retry 2 2 ps_drush ps:dictionary:import -y; then
    ps_warn "Dictionary CSV import failed"
  fi
fi

ps_timed_run "Set projection bypass state" ps_drush state:set ps_offer.skip_projection 1 --input-format=integer
if ! ps_timed_run "Import offers and dependencies" ps_retry 2 2 ps_drush migrate:import ps_offer_from_xml --execute-dependencies -y; then
  ps_drush state:delete ps_offer.skip_projection || true
  ps_die "Offer migration failed"
fi
ps_timed_run "Unset projection bypass state" ps_drush state:delete ps_offer.skip_projection

if [[ "${PUBLISH_VALID_OFFERS_AFTER_IMPORT}" == "1" ]]; then
  if ! ps_timed_run "Publish valid imported offers" ps_retry 2 2 ps_drush scr scripts/tools/publish-valid-offers.php; then
    ps_warn "Post-import publication step failed"
  fi
else
  ps_warn "Post-import publication skipped (PUBLISH_VALID_OFFERS_AFTER_IMPORT=0)"
fi

if ! ps_timed_run "Sync and index search features" ps_retry 2 2 ps_drush ps:search:features:sync-index --rebuild-tracker=1; then
  ps_warn "Search feature sync/index step failed (search stack may be disabled)"
fi

ps_success "CRM import completed"
ps_drush migrate:status --group=ps_project
