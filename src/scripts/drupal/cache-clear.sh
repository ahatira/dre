#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Cache clear — rebuild cache on all multisite countries.

Usage: scripts/main.sh drupal cache-clear
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Cache rebuild (all countries)"
ps_load_config
ps_resolve_runtime

for country in "${PS_COUNTRIES[@]}"; do
  ps_drush_for_country "${country}"
  if ps_drush_bootstrapped; then
    ps_drush_cr
    ps_success "Cache rebuilt: ${country}"
  else
    ps_warn "Skip ${country} (not bootstrapped)"
  fi
done
