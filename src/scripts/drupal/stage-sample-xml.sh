#!/usr/bin/env bash
# shellcheck disable=SC1091
# Stage per-country sample CRM XML into web/sites/{country}/files/crm/offers.xml

source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Stage Property Search sample CRM XML for one multisite country.

Usage: scripts/main.sh drupal stage-sample-xml [country]

Default country: PS_COUNTRY_CODE env or com.

Examples:
  PS_COUNTRY_CODE=fr scripts/main.sh drupal stage-sample-xml
  scripts/main.sh drupal stage-sample-xml es
EOF
}

country="${1:-${PS_COUNTRY_CODE:-com}}"
if [[ "${country}" == "-h" || "${country}" == "--help" ]]; then
  show_help
  exit 0
fi

ps_is_country_code "${country}" || ps_die "Unknown country: ${country}"

ps_require_file "${PS_PROJECT_ROOT}/data/xml/bnppre_sample_50_per_type.xml"
ps_stage_country_sample_xml "${country}"
