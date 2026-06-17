#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Index Solr — Search API offers index for one country.

Usage: scripts/main.sh drupal index-solr [country]

Default country: com

Prerequisites: offers imported (make import [country]), Solr up.
EOF
}

COUNTRY="${PS_COUNTRY_CODE:-com}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help) show_help; exit 0 ;;
    com|be|es|fr|ie|it|lu|nl|pl) COUNTRY="$1"; shift ;;
    *) ps_die "Unknown option or country: $1" ;;
  esac
done

ps_is_country_code "${COUNTRY}" || ps_die "Unknown country: ${COUNTRY}"
ps_load_config
ps_drush_for_country "${COUNTRY}"

ps_header "Solr index (${COUNTRY})"
ps_drush_bootstrapped || ps_die "Site not installed. Run: make install ${COUNTRY}"

ps_index_offers_solr
ps_drush_cr
ps_success "Solr index complete (${PS_DRUSH_ALIAS})"
