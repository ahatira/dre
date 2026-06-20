#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Import — CRM XML via import pipeline + Solr index.

Usage: scripts/main.sh drupal import [country] [OPTIONS]

Default country: com

Options:
  --force    Run pipeline even if offers already exist
  --mode     full|delta (default: config)
  -h, --help Show this help

Prerequisites: make install [country]
Seeds sample XML into incoming/ when empty, then runs ps:import:run.
EOF
}

FORCE=0
MODE=""
COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force) FORCE=1; shift ;;
    --mode=*) MODE="${1#*=}"; shift ;;
    --mode) MODE="$2"; shift 2 ;;
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option or country: $1" ;;
  esac
done

ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"
ps_load_config
ps_drush_for_country "${COUNTRY}"

ps_header "Import CRM (${COUNTRY})"
ps_drush_bootstrapped || ps_die "Site not installed. Run: make install ${COUNTRY}"

count="$(ps_drush_published_offer_count)"
if [[ -n "${count}" && "${count}" -gt 0 && ${FORCE} -eq 0 ]]; then
  ps_info "Published offers present (${count}) — skip (use --force)"
  exit 0
fi

ps_retry 2 2 ps_drush en -y migrate migrate_plus migrate_tools ps_migrate file

IMPORT_OPTS=(--seed-sample=1)
if [[ -n "${MODE}" ]]; then
  IMPORT_OPTS+=(--mode="${MODE}")
fi

ps_info "Running CRM import pipeline..."
ps_retry 2 2 ps_drush ps:import:run "${IMPORT_OPTS[@]}" \
  || ps_die "Import pipeline failed"

count="$(ps_drush_published_offer_count)"
[[ -n "${count}" && "${count}" -gt 0 ]] || ps_die "No offers after import"

ps_info "Indexing Solr..."
ps_index_offers_solr
ps_drush_cr
ps_success "Import complete: ${count} offers (${PS_DRUSH_ALIAS})"
ps_info "Optional: make demo ${COUNTRY}"
