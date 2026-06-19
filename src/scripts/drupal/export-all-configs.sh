#!/usr/bin/env bash
# shellcheck disable=SC1091
# Export full CMI per country into config/sites/{code}/ (drush cex).
# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"
# shellcheck source=/dev/null
source "${PS_SCRIPTS_DIR}/_core/config-sync.sh"

show_help() {
  cat <<'EOF'
Export all configs — one full CMI folder per country.

Usage: scripts/main.sh drupal export-all-configs [countries...]

Default: all countries from web/sites/countries.yml.

Each export writes to config/sites/{code}/ (see settings.bootstrap.php).

Prerequisites:
  Site installed and configured for each country
  make up + bootstrapped DB per country
EOF
}

COUNTRIES_RAW="all"
COUNTRY_POSITIONAL=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    -h|--help) show_help; exit 0 ;;
    *)
      ps_is_country_code "$1" || ps_die "Unknown country: $1"
      COUNTRY_POSITIONAL+=("$1")
      shift
      ;;
  esac
done

if [[ ${#COUNTRY_POSITIONAL[@]} -gt 0 ]]; then
  COUNTRIES_RAW="$(IFS=,; echo "${COUNTRY_POSITIONAL[*]}")"
fi

ps_header "Drupal: export-all-configs"
ps_load_config
ps_resolve_runtime

mapfile -t COUNTRIES < <(ps_parse_countries_arg "${COUNTRIES_RAW}")

for country in "${COUNTRIES[@]}"; do
  ps_drush_for_country "${country}"
  ps_header "Export ${country} → config/sites/${country}"
  ps_drush_bootstrapped || ps_die "Site ${country} not bootstrapped — install first"
  mkdir -p "$(ps_config_sites_root)/${country}"
  ps_drush config:export -y
  ps_success "Exported: config/sites/${country}/"
done

unset PS_DRUSH_ALIAS PS_COUNTRY_CODE
