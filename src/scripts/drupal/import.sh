#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Import — CRM sample XML via migrate + Solr index.

Usage: scripts/main.sh drupal import [country] [OPTIONS]

Default country: com

Options:
  --force    Re-import even if offers already exist
  -h, --help Show this help

Prerequisites: make install [country]
Does not import dictionary (done at install) or demo content.
EOF
}

FORCE=0
COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force) FORCE=1; shift ;;
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option or country: $1" ;;
  esac
done

ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"
ps_load_config
ps_drush_for_country "${COUNTRY}"

ps_header "Import CRM sample (${COUNTRY})"
ps_drush_bootstrapped || ps_die "Site not installed. Run: make install ${COUNTRY}"

count="$(ps_drush_published_offer_count)"
if [[ -n "${count}" && "${count}" -gt 0 && ${FORCE} -eq 0 ]]; then
  ps_info "Published offers present (${count}) — skip (use --force)"
  exit 0
fi

ps_stage_sample_xml "${COUNTRY}"
ps_retry 2 2 ps_drush en -y migrate migrate_plus migrate_tools ps_migrate

ps_info "Running migrate pipeline..."
ps_retry 2 2 ps_drush migrate:import ps_offer_from_xml --update --execute-dependencies -y \
  || ps_die "Offer migrate failed"
ps_retry 2 2 ps_drush migrate:import ps_offer_translations_from_xml --update -y \
  || ps_warn "Offer translation migrate warnings"

count="$(ps_drush_published_offer_count)"
[[ -n "${count}" && "${count}" -gt 0 ]] || ps_die "No offers after migrate"

ps_info "Indexing Solr..."
ps_index_offers_solr
ps_drush_cr
ps_success "Import complete: ${count} offers (${PS_DRUSH_ALIAS})"
ps_info "Optional: make demo ${COUNTRY}"
