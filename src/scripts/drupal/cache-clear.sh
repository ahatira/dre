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

if [[ -n "${PS_DRUSH_URI:-}" ]]; then
  ps_info "Running drush cache:rebuild for ${PS_DRUSH_URI}..."
  ps_drush_cr
else
  ps_info "Running drush cache:rebuild for all multisite URIs..."
  for country in $(ps_multisite_countries); do
    uri="$(ps_site_uri "${country}")"
    if ps_drush --uri="${uri}" status --fields=bootstrap 2>/dev/null | grep -q 'Successful'; then
      ps_info "Cache rebuild: ${country} (${uri})"
      ps_drush --uri="${uri}" cache:rebuild -y
    fi
  done
fi

ps_success "Cache cleared successfully!"
