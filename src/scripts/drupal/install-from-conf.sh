#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Install from config — multisite CMI import (no CRM XML, no demo).

Usage: scripts/main.sh drupal install-from-conf [OPTIONS] [countries...]

Default: all countries from web/sites/countries.yml.

Options:
  --force              Drop and recreate DB per country
  --countries=LIST     Comma-separated country codes
  -h, --help           Show this help

Prerequisites:
  config/sites/{country}/ populated (full CMI per site)
  make env + make up (dev)
  First time: make seed-site-configs (from legacy config/sync) or make export-all-configs

Per country:
  site:install minimal → config:import from config/sites/{country} → post-steps

Workflow:
  make install-from-conf [country]   — this command
  make rbac-sync [country]           — RBAC roles (optional)
  make import [country]              — CRM sample + Solr
  make demo [country]                — demo content (optional)
EOF
}

SITE_NAME="${SITE_NAME:-BNP Paribas Real Estate}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
FORCE_INSTALL=0
COUNTRIES_RAW="all"
COUNTRY_POSITIONAL=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force) FORCE_INSTALL=1; shift ;;
    --countries=*) COUNTRIES_RAW="${1#*=}"; shift ;;
    --country=*) COUNTRIES_RAW="${1#*=}"; shift ;;
    -h|--help) show_help; exit 0 ;;
    *)
      ps_is_country_code "$1" || ps_die "Unknown option or country: $1"
      COUNTRY_POSITIONAL+=("$1")
      shift
      ;;
  esac
done

if [[ ${#COUNTRY_POSITIONAL[@]} -gt 0 ]]; then
  if [[ ${#COUNTRY_POSITIONAL[@]} -eq 1 && "${COUNTRY_POSITIONAL[0]}" == "all" ]]; then
    COUNTRIES_RAW="all"
  else
    codes=()
    for code in "${COUNTRY_POSITIONAL[@]}"; do
      [[ "${code}" == "all" ]] && ps_die "Omit country codes to install all sites"
      codes+=("${code}")
    done
    COUNTRIES_RAW="$(IFS=,; echo "${codes[*]}")"
  fi
fi

export FORCE_INSTALL ADMIN_USER ADMIN_PASS ADMIN_MAIL

ps_header "Drupal: multisite install-from-conf"
ps_load_config

if [[ "$(ps_env_get APP_ENV dev)" == "dev" ]]; then
  ps_require_file "${PS_SRC_DIR}/.env" "Run: make env (from repo root)"
fi

ps_resolve_runtime
ps_success "Drush runtime: ${PS_RUNTIME}"

mapfile -t COUNTRIES < <(ps_parse_countries_arg "${COUNTRIES_RAW}")

for country in "${COUNTRIES[@]}"; do
  ps_ensure_country_database "${country}"
  ps_info "Provisioning file directories for ${country}..."
  ps_provision_country_files "${country}"
done

# shellcheck disable=SC1091
source "${PS_SCRIPTS_DIR}/drupal/install-from-conf-country.sh"

for country in "${COUNTRIES[@]}"; do
  ps_install_country_from_conf "${country}" "${SITE_NAME}"
done

ps_success "Multisite install-from-conf complete"
for country in "${COUNTRIES[@]}"; do
  ps_info "${country}: $(ps_site_uri "${country}") — ${ADMIN_USER}/${ADMIN_PASS}"
done

unset PS_DRUSH_ALIAS PS_COUNTRY_CODE
