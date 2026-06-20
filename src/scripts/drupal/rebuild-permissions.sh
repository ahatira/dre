#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Rebuild permissions — rebuild node content access records (node_access table).

Usage: scripts/main.sh drupal rebuild-permissions [country...]

  rebuild-permissions           all bootstrapped countries
  rebuild-permissions fr          single country

Equivalent to Administration → Reports → Status → Rebuild permissions.
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Rebuild content permissions"
ps_load_config
ps_resolve_runtime

ps_countries_init
target_countries=()
if [[ $# -gt 0 ]]; then
  countries_raw="$(IFS=,; printf '%s' "$*")"
  mapfile -t target_countries < <(ps_parse_countries_arg "${countries_raw}")
else
  target_countries=("${_PS_COUNTRIES_CACHE[@]}")
fi

for country in "${target_countries[@]}"; do
  ps_drush_for_country "${country}"
  if ps_drush_bootstrapped; then
    ps_drush_rebuild_permissions
    ps_success "Content permissions rebuilt: ${country}"
  else
    ps_warn "Skip ${country} (not bootstrapped)"
  fi
done
