#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Deploy — config import, database updates, cache rebuild (all countries).

Usage: scripts/main.sh drupal deploy

Per country: drush cim -y → updb -y → cr
Fail-fast on first error. Run tools build before deploy in production.
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Deploy (all countries)"
ps_load_config
ps_resolve_runtime

bash "${PS_SCRIPTS_DIR}/tools/check.sh" || ps_die "Build verify failed. Run: make build"

for country in "${PS_COUNTRIES[@]}"; do
  ps_drush_for_country "${country}"
  ps_info "Deploy ${country} (${PS_DRUSH_ALIAS})..."
  ps_drush_bootstrapped || ps_die "Site ${country} not bootstrapped — aborting deploy"
  ps_drush config:import -y
  ps_drush updatedb -y
  ps_drush_cr
  ps_success "Deployed ${country}"
done

ps_success "Deploy complete (all countries)"
