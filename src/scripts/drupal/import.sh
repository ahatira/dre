#!/usr/bin/env bash
# shellcheck disable=SC1091
# CRM sample XML migrate import — independent step after make install.
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Import Script - Sample CRM XML (offers + dependencies)

Usage: scripts/main.sh drupal import [country] [OPTIONS]

Imports staged sample XML via ps_migrate (offers, media, agents, features…).
Does not install demo content — use make demo separately.

Prerequisites:
  - make install (or make reinstall) for the target country
  - Docker running

Options:
  --force           Re-import even if published offers already exist
  -h, --help        Show this help

Examples:
  make import es
  scripts/main.sh drupal import fr --force
  PS_COUNTRY_CODE=be scripts/main.sh drupal import
EOF
}

FORCE_IMPORT=0
COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force)
      FORCE_IMPORT=1
      shift
      ;;
    -h|--help)
      show_help
      exit 0
      ;;
    com|be|es|fr|ie|it|lu|nl|pl)
      COUNTRY="$1"
      shift
      ;;
    *)
      ps_die "Unknown option or country: $1"
      ;;
  esac
done

ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"

PS_COUNTRY_CODE="${COUNTRY}"
export PS_COUNTRY_CODE
PS_DRUSH_URI="$(ps_site_uri "${COUNTRY}")"
export PS_DRUSH_URI

ps_header "Drupal: CRM XML import (${COUNTRY})"

ps_info "Checking prerequisites..."
ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"
ps_in_docker || ps_die "Docker containers not running. Start them first: make up"
ps_drush_bootstrapped || ps_die "Drupal is not installed. Run: make install ${COUNTRY}"
ps_success "Prerequisites OK"

EXISTING_OFFERS="$(ps_drush_published_offer_count)"
if [[ -n "${EXISTING_OFFERS}" && "${EXISTING_OFFERS}" -gt 0 && ${FORCE_IMPORT} -eq 0 ]]; then
  ps_info "Published offers already present (${EXISTING_OFFERS}) — skipping (use --force to re-import)"
  exit 0
fi

ps_info "Staging sample XML for ${COUNTRY}..."
ps_require_file "${PS_PROJECT_ROOT}/data/xml/bnppre_sample_50_per_type.xml" \
  "Master sample XML not found: ${PS_PROJECT_ROOT}/data/xml/bnppre_sample_50_per_type.xml"
ps_stage_country_sample_xml "${COUNTRY}"
XML_TARGET="$(ps_crm_xml_target "${COUNTRY}")"

ps_info "Importing dictionary (ensure referentials are up to date)..."
ps_retry 2 2 ps_drush ps:dictionary:import -y || ps_warn "Dictionary import warnings"

ps_info "Ensuring migrate stack is enabled..."
ps_retry 2 2 ps_drush en -y migrate migrate_plus migrate_tools ps_migrate

ps_info "Removing stale migrate configs (imported before ps_migrate)..."
ps_drush ev '
if (!\Drupal::moduleHandler()->moduleExists("ps_migrate")) {
  $connection = \Drupal::database();
  $names = $connection->select("config", "c")
    ->fields("c", ["name"])
    ->condition("name", "migrate_plus.%", "LIKE")
    ->execute()
    ->fetchCol();
  foreach ($names as $name) {
    if (preg_match("/^migrate_plus\.(migration|migration_group)\.ps_/", $name)) {
      $connection->delete("config")->condition("name", $name)->execute();
    }
  }
}
' || true

ps_info "Running CRM migrate pipeline (${XML_TARGET})..."
if ! ps_retry 2 2 ps_drush migrate:import ps_offer_from_xml --update --execute-dependencies -y; then
  ps_die "Offer migrate failed (check ${XML_TARGET})"
fi

ps_info "Importing offer translations from XML..."
ps_retry 2 2 ps_drush migrate:import ps_offer_translations_from_xml --update -y \
  || ps_warn "Offer translation migrate had warnings"

OFFER_COUNT="$(ps_drush_published_offer_count)"
[[ -n "${OFFER_COUNT}" && "${OFFER_COUNT}" -gt 0 ]] \
  || ps_die "No offers found after migrate import"

ps_info "Indexing offers in Solr..."
ps_solr_init_cores || ps_warn "Solr core init had warnings"
bash "${PS_SCRIPTS_DIR}/drupal/index-solr.sh"

ps_info "Syncing feature filters into Solr..."
ps_retry 2 2 ps_drush ps:search:features:sync-index --rebuild-tracker=1 -y \
  || ps_warn "Feature filter sync failed"

ps_retry 2 2 ps_drush_cr
ps_success "CRM import complete: ${OFFER_COUNT} published offers (${PS_DRUSH_URI})"
ps_info "Optional next step: make demo ${COUNTRY}"
