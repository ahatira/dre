#!/usr/bin/env bash
set -Eeuo pipefail

SCRIPTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

usage() {
  cat <<'EOF'
Usage: src/scripts/main.sh <domain> <command> [args...]

Domains:
  drupal     install|deploy|verify|import-crm|cache|cron|migrate|config|users|permissions|translations|queue|search-api|solr|qa|cleanup
  tools      build|git|quality|lint|test|debug|profile|docs
  composer   update|audit|validate|autoload|cleanup
  generate   generate-xml|bnppre-offers

Examples:
  src/scripts/main.sh drupal install --force
  src/scripts/main.sh drupal import-crm
  src/scripts/main.sh tools quality
  src/scripts/main.sh generate bnppre-offers
EOF
}

if [[ $# -lt 2 ]]; then
  usage
  exit 1
fi

DOMAIN="$1"
COMMAND="$2"
shift 2

case "${DOMAIN}" in
  drupal|tools|composer)
    SCRIPT_PATH="${SCRIPTS_DIR}/${DOMAIN}/${COMMAND}.sh"
    ;;
  generate)
    SCRIPT_PATH="${SCRIPTS_DIR}/generate/${COMMAND}.sh"
    ;;
  *)
    echo "Unknown domain: ${DOMAIN}" >&2
    usage
    exit 1
    ;;
esac

if [[ ! -f "${SCRIPT_PATH}" ]]; then
  echo "Unknown command: ${DOMAIN} ${COMMAND}" >&2
  usage
  exit 1
fi

exec bash "${SCRIPT_PATH}" "$@"
