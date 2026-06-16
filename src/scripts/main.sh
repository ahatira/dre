#!/usr/bin/env bash
# PS Project — scripts CLI entry point.
set -Eeuo pipefail

SCRIPTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

usage() {
  cat <<'EOF'
PS Project — Scripts CLI

Usage: scripts/main.sh <domain> <command> [args...]

Domains:
  drupal    Drupal operations (install, import, demo, deploy, …)
  tools     Build and environment utilities

Examples:
  scripts/main.sh tools build
  scripts/main.sh tools check
  scripts/main.sh drupal install es
  scripts/main.sh drupal deploy

Help: scripts/main.sh <domain> <command> --help
EOF
}

[[ $# -ge 2 ]] || { usage; exit 1; }

DOMAIN="$1"
COMMAND="$2"
shift 2

case "${DOMAIN}" in
  drupal|tools) SCRIPT="${SCRIPTS_DIR}/${DOMAIN}/${COMMAND}.sh" ;;
  *) echo "Unknown domain: ${DOMAIN}" >&2; usage; exit 1 ;;
esac

if [[ ! -f "${SCRIPT}" ]]; then
  echo "Unknown command: ${DOMAIN} ${COMMAND}" >&2
  echo "Available ${DOMAIN} commands:"
  for f in "${SCRIPTS_DIR}/${DOMAIN}/"*.sh; do
    [[ -f "${f}" ]] && echo "  - $(basename "${f}" .sh)"
  done
  exit 1
fi

exec bash "${SCRIPT}" "$@"
