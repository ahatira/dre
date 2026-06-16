#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Drupal multisite installation orchestrator

show_help() {
  cat <<'EOF'
Install Script - Install Property Search multisite countries

Usage: scripts/main.sh drupal install [OPTIONS]

Options:
  --force              Force reinstall (drop DB per country)
  --minimal            Skip post-install (demo, offers, Solr) on all countries
  --dev                Enable development modules (devel, stage_file_proxy)
  --countries=LIST     Country codes comma-separated or "all" (default: all)
  --country=CODE       Install a single country (shortcut for --countries)
  --master-only        Post-install only on COM (legacy shortcut)
  -h, --help           Show this help

Each country uses isolated file directories and its own language set:
  - public:  web/sites/{code}/files/
  - private: src/private/{code}/

By default post-install (demo, offers, Solr) runs on every country.

Prerequisites:
  - make env (src/.env)
  - Docker running

Environment variables:
  SITE_NAME        Base site name (default: "PS Project")
  ADMIN_USER       Admin username (default: "admin")
  ADMIN_PASS       Admin password (default: "admin")
  ADMIN_MAIL       Admin email (default: "admin@example.com")

Examples:
  scripts/main.sh drupal install
  scripts/main.sh drupal install com
  scripts/main.sh drupal install com fr be
  scripts/main.sh drupal install --minimal fr
  scripts/main.sh drupal install --countries=fr,com
  scripts/main.sh drupal install --country=fr --force

  make install com
  make install com fr --minimal
EOF
}

ps_is_country_code() {
  case "$1" in
    com|be|es|fr|ie|it|lu|nl|pl|all)
      return 0
      ;;
    *)
      return 1
      ;;
  esac
}

SITE_NAME="${SITE_NAME:-PS Project}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
FORCE_INSTALL=0
ENABLE_DEV=0
MINIMAL_INSTALL=0
POST_INSTALL_MASTER_ONLY=0
COUNTRIES_RAW="all"
MASTER_COUNTRY="com"
COUNTRY_POSITIONAL=()

while [[ $# -gt 0 ]]; do
  case "$1" in
    --force)
      FORCE_INSTALL=1
      shift
      ;;
    --minimal)
      MINIMAL_INSTALL=1
      shift
      ;;
    --dev)
      ENABLE_DEV=1
      shift
      ;;
    --master-only)
      POST_INSTALL_MASTER_ONLY=1
      shift
      ;;
    --post-install-all)
      shift
      ;;
    --countries=*)
      COUNTRIES_RAW="${1#*=}"
      shift
      ;;
    --country=*)
      COUNTRIES_RAW="${1#*=}"
      shift
      ;;
    -h|--help)
      show_help
      exit 0
      ;;
    *)
      if ps_is_country_code "$1"; then
        COUNTRY_POSITIONAL+=("$1")
        shift
      else
        ps_die "Unknown option or country code: $1 (expected com, be, es, fr, ie, it, lu, nl, pl, or all)"
      fi
      ;;
  esac
done

if [[ ${#COUNTRY_POSITIONAL[@]} -gt 0 ]]; then
  if [[ ${#COUNTRY_POSITIONAL[@]} -eq 1 && "${COUNTRY_POSITIONAL[0]}" == "all" ]]; then
    COUNTRIES_RAW="all"
  else
    local_codes=()
    for code in "${COUNTRY_POSITIONAL[@]}"; do
      [[ "${code}" == "all" ]] && ps_die "Use 'make install' without country codes to install all sites"
      local_codes+=("${code}")
    done
    COUNTRIES_RAW="$(IFS=,; echo "${local_codes[*]}")"
  fi
fi

export FORCE_INSTALL ENABLE_DEV MINIMAL_INSTALL ADMIN_USER ADMIN_PASS ADMIN_MAIL

ps_header "Drupal: Multisite install"

ps_info "Checking prerequisites..."
ps_require_cmd docker
ps_require_file "${PS_DOCKER_COMPOSE_FILE}"
ps_require_file "${PS_SRC_DIR}/.env"
ps_in_docker || ps_die "Docker containers not running. Start them first: docker compose up -d"
ps_success "Prerequisites OK"

mapfile -t COUNTRIES < <(ps_parse_countries_arg "${COUNTRIES_RAW}")

bash "${PS_SCRIPTS_DIR}/drupal/provision-databases.sh"
bash "${PS_SCRIPTS_DIR}/drupal/provision-site-files.sh" --countries="${COUNTRIES_RAW}"

# shellcheck disable=SC1091
source "${PS_SCRIPTS_DIR}/drupal/install-country.sh"

for country in "${COUNTRIES[@]}"; do
  country_label="$(ps_country_upper "${country}")"
  site_name="${SITE_NAME} ${country_label}"
  ps_install_country_site "${country}" "${site_name}"
  ps_import_active_language_config_overrides "${country}"
done

ps_success "Multisite install complete!"
echo ""

for country in "${COUNTRIES[@]}"; do
  uri="$(ps_site_uri "${country}")"
  admin_uri="$(ps_site_admin_uri "${country}" 2>/dev/null || true)"
  if [[ -n "${admin_uri}" ]]; then
    ps_info "${country}: front=${uri} admin=${admin_uri}/admin"
  else
    ps_info "${country}: ${uri}/admin"
  fi
  ps_info "  login: ${ADMIN_USER} / ${ADMIN_PASS}"
done

should_post_install() {
  local country="$1"
  if [[ ${MINIMAL_INSTALL} -eq 1 ]]; then
    return 1
  fi
  if [[ ${POST_INSTALL_MASTER_ONLY} -eq 1 ]]; then
    [[ "${country}" == "${MASTER_COUNTRY}" ]]
    return
  fi
  return 0
}

for country in "${COUNTRIES[@]}"; do
  if should_post_install "${country}"; then
    ps_info "Post-install for ${country}..."
    PS_DRUSH_URI="$(ps_site_uri "${country}")"
    export PS_DRUSH_URI
    PS_COUNTRY_CODE="${country}"
    export PS_COUNTRY_CODE
    bash "${PS_SCRIPTS_DIR}/drupal/post-install.sh"
  else
    ps_info "Skipping post-install for ${country}"
  fi
done

unset PS_DRUSH_URI PS_COUNTRY_CODE
