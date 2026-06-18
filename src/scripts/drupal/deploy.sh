#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Deploy — config import, database updates, cache rebuild.

Usage: scripts/main.sh drupal deploy [country...]

  deploy           all countries from web/sites/countries.yml
  deploy fr        single country
  deploy fr be     multiple countries (comma or space separated)

Per country: drush cim -y → updb -y → cr
Fail-fast on first error. Run tools build before deploy in production.
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Deploy"
ps_load_config
ps_resolve_runtime

bash "${PS_SCRIPTS_DIR}/tools/check.sh" || ps_die "Build verify failed. Run: make build"

ps_countries_init
deploy_countries=()
if [[ $# -gt 0 ]]; then
  countries_raw="$(IFS=,; printf '%s' "$*")"
  mapfile -t deploy_countries < <(ps_parse_countries_arg "${countries_raw}")
else
  deploy_countries=("${_PS_COUNTRIES_CACHE[@]}")
fi

for country in "${deploy_countries[@]}"; do
  ps_drush_for_country "${country}"
  ps_info "Deploy ${country} (${PS_DRUSH_ALIAS})..."
  ps_drush_bootstrapped || ps_die "Site ${country} not bootstrapped — aborting deploy"
  ps_drush config:import -y
  ps_drush updatedb -y
  ps_drush_cr
  ps_success "Deployed ${country}"
done

if [[ $# -gt 0 ]]; then
  ps_success "Deploy complete (${#deploy_countries[@]} countries)"
else
  ps_success "Deploy complete (all countries)"
fi
