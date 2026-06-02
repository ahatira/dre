#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Cache clear - Rebuild Drupal cache

show_help() {
  cat <<'EOF'
Cache Clear - Rebuild Drupal caches

Usage: scripts/main.sh drupal cache-clear

This rebuilds all Drupal caches using drush cache:rebuild.

Examples:
  scripts/main.sh drupal cache-clear
EOF
}

if [[ "${1:-}" == "--help" ]] || [[ "${1:-}" == "-h" ]]; then
  show_help
  exit 0
fi

ps_header "Drupal: Clearing all caches"

ps_info "Running drush cache:rebuild..."
ps_drush_cr

ps_success "Cache cleared successfully!"
