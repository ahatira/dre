#!/usr/bin/env bash
# shellcheck disable=SC1091
# Generates per-country CRM sample XML (50 offers, multilang) into data/xml/samples/.

source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

show_help() {
  cat <<'EOF'
Generate Property Search per-country sample CRM XML.

Usage: scripts/main.sh tools generate-sample-xml [country|all]

Creates data/xml/samples/{country}/offers.xml (~10 offers per asset type, multilingual).

Examples:
  scripts/main.sh tools generate-sample-xml fr
  scripts/main.sh tools generate-sample-xml all
EOF
}

country="${1:-all}"
if [[ "${country}" == "-h" || "${country}" == "--help" ]]; then
  show_help
  exit 0
fi

generator="${PS_SRC_DIR}/scripts/tools/generate_country_sample_xml.php"
ps_require_file "${generator}"
ps_require_file "${PS_PROJECT_ROOT}/data/xml/bnppre_sample_50_per_type.xml"

php "${generator}" "${country}"
