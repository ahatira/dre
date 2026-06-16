#!/usr/bin/env bash
# Multisite helpers — country URIs and per-site file paths from src/.env

ps_load_dotenv() {
  if [[ -f "${PS_SRC_DIR}/.env" ]]; then
    set -a
    # shellcheck disable=SC1090
    source "${PS_SRC_DIR}/.env"
    set +a
  fi
}

ps_multisite_countries() {
  printf '%s\n' com be es fr ie it lu nl pl
}

ps_country_upper() {
  printf '%s' "$1" | tr '[:lower:]' '[:upper:]'
}

ps_is_country_code() {
  case "$1" in
    com|be|es|fr|ie|it|lu|nl|pl) return 0 ;;
    *) return 1 ;;
  esac
}

ps_site_domain() {
  local country="$1"
  local upper var
  upper="$(ps_country_upper "${country}")"
  var="APP_DOMAIN_${upper}"
  ps_load_dotenv
  printf '%s' "${!var:-}"
}

ps_site_port() {
  local country="$1"
  local upper var
  upper="$(ps_country_upper "${country}")"
  var="APP_DOMAIN_${upper}_PORT"
  ps_load_dotenv
  printf '%s' "${!var:-8080}"
}

ps_site_uri() {
  local country="$1"
  local domain port
  domain="$(ps_site_domain "${country}")"
  port="$(ps_site_port "${country}")"
  if [[ -z "${domain}" ]]; then
    ps_die "Missing APP_DOMAIN_$(ps_country_upper "${country}") in src/.env"
  fi
  if [[ "${port}" == "80" ]]; then
    printf 'http://%s' "${domain}"
  else
    printf 'http://%s:%s' "${domain}" "${port}"
  fi
}

ps_public_files_dir() {
  local country="$1"
  printf '%s/web/sites/%s/files' "${PS_SRC_DIR}" "${country}"
}

ps_private_files_dir() {
  local country="$1"
  printf '%s/private/%s' "${PS_SRC_DIR}" "${country}"
}

ps_site_admin_uri() {
  local country="$1"
  local upper var domain port
  upper="$(ps_country_upper "${country}")"
  var="APP_DOMAIN_${upper}_ADMIN"
  ps_load_dotenv
  domain="${!var:-}"
  if [[ -z "${domain}" ]]; then
    return 1
  fi
  port="$(ps_site_port "${country}")"
  if [[ "${port}" == "80" ]]; then
    printf 'http://%s' "${domain}"
  else
    printf 'http://%s:%s' "${domain}" "${port}"
  fi
}

ps_crm_xml_target() {
  printf '%s/crm/offers.xml' "$(ps_public_files_dir "$1")"
}

# Generated sample XML for a country (data/xml/samples/{country}/offers.xml).
ps_sample_xml_source() {
  local country="$1"
  printf '%s/data/xml/samples/%s/offers.xml' "${PS_PROJECT_ROOT}" "${country}"
}

# Generate sample XML if missing, then return path to staged CRM file.
ps_stage_country_sample_xml() {
  local country="$1"
  local source target generator
  source="$(ps_sample_xml_source "${country}")"
  generator="${PS_SRC_DIR}/scripts/tools/generate_country_sample_xml.php"

  if [[ ! -f "${source}" ]]; then
    ps_info "Generating sample XML for ${country}..."
    if [[ ! -f "${generator}" ]]; then
      ps_die "Sample XML generator not found: ${generator}"
    fi
    php "${generator}" "${country}" || ps_die "Failed to generate sample XML for ${country}"
  fi

  if [[ ! -f "${source}" ]]; then
    ps_die "Sample XML not found after generation: ${source}"
  fi

  target="$(ps_crm_xml_target "${country}")"
  mkdir -p "$(dirname "${target}")"
  cp "${source}" "${target}"
  ps_success "XML staged for ${country}: ${target}"
}

ps_parse_countries_arg() {
  local raw="$1"
  local -a selected=()
  local token

  if [[ -z "${raw}" || "${raw}" == "all" ]]; then
    ps_multisite_countries
    return 0
  fi

  IFS=',' read -r -a tokens <<< "${raw}"
  for token in "${tokens[@]}"; do
    token="$(printf '%s' "${token}" | tr '[:upper:]' '[:lower:]' | xargs)"
    [[ -z "${token}" ]] && continue
    case "${token}" in
      com|be|es|fr|ie|it|lu|nl|pl)
        selected+=("${token}")
        ;;
      *)
        ps_die "Unknown country code: ${token}"
        ;;
    esac
  done

  if [[ ${#selected[@]} -eq 0 ]]; then
    ps_die "No valid countries in: ${raw}"
  fi

  printf '%s\n' "${selected[@]}"
}
