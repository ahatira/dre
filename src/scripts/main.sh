#!/usr/bin/env bash
# Main entry point for PS Project scripts
set -Eeuo pipefail

SCRIPTS_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

usage() {
  cat <<'EOF'
PS Project - Scripts CLI

Usage: scripts/main.sh <domain> <command> [args...]

Domains:
  drupal     Drupal-specific operations (install, cache, etc.)
  tools      Development tools (build, quality, etc.)

Examples:
  scripts/main.sh drupal install
  scripts/main.sh drupal cache-clear
  scripts/main.sh tools build
  scripts/main.sh tools build --production

For command-specific help:
  scripts/main.sh <domain> <command> --help
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
  drupal|tools)
    SCRIPT_PATH="${SCRIPTS_DIR}/${DOMAIN}/${COMMAND}.sh"
    ;;
  *)
    echo "Unknown domain: ${DOMAIN}" >&2
    usage
    exit 1
    ;;
esac

if [[ ! -f "${SCRIPT_PATH}" ]]; then
  echo "Unknown command: ${DOMAIN} ${COMMAND}" >&2
  echo ""
  echo "Available ${DOMAIN} commands:"
  for script in "${SCRIPTS_DIR}/${DOMAIN}"/*.sh; do
    [[ -f "${script}" ]] && echo "  - $(basename "${script}" .sh)"
  done
  exit 1
fi

exec bash "${SCRIPT_PATH}" "$@"
