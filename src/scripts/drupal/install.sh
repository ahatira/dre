#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Install — greenfield multisite shell (no CRM XML, no demo).

Usage: scripts/main.sh drupal install [OPTIONS] [countries...]

Default: all countries (com, be, es, fr, ie, it, lu, nl, pl).

Options:
  --force              Drop and recreate DB per country
  --dev                Enable devel / stage_file_proxy
  --countries=LIST     Comma-separated country codes or "all"
  -h, --help           Show this help

Workflow:
  make install [country]   — this command
  make import [country]    — CRM sample + Solr
  make demo [country]      — demo content (optional)

Prerequisites (dev): make env
RBAC: run make rbac-sync separately after install.
EOF
}

SITE_NAME="${SITE_NAME:-BNP Paribas Real Estate}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
FORCE_INSTALL=0
ENABLE_DEV=0
COUNTRIES_RAW="all"
COUNTRY_POSITIONAL=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force) FORCE_INSTALL=1; shift ;;
    --dev) ENABLE_DEV=1; shift ;;
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

export FORCE_INSTALL ENABLE_DEV ADMIN_USER ADMIN_PASS ADMIN_MAIL

ps_header "Drupal: multisite install (shell)"
ps_load_config

if [[ "$(ps_env_get APP_ENV dev)" == "dev" ]]; then
  ps_require_file "${PS_SRC_DIR}/.env" "Run: make env"
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
source "${PS_SCRIPTS_DIR}/drupal/install-country.sh"

for country in "${COUNTRIES[@]}"; do
  ps_install_country_site "${country}" "${SITE_NAME}"
done

ps_success "Multisite shell install complete"
for country in "${COUNTRIES[@]}"; do
  ps_info "${country}: $(ps_site_uri "${country}") — ${ADMIN_USER}/${ADMIN_PASS}"
  ps_info "  make rbac-sync (optional) | make import ${country} | make demo ${country}"
done

unset PS_DRUSH_ALIAS PS_COUNTRY_CODE
