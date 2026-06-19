#!/usr/bin/env bash
# Multisite helpers — URIs, languages, file paths.
# Country list and language matrix: web/sites/countries.yml (see countries.sh).

ps_country_upper() {
  printf '%s' "$1" | tr '[:lower:]' '[:upper:]'
}

ps_site_domain() {
  local country="$1"
  local upper="APP_DOMAIN_$(ps_country_upper "${country}")"
  ps_env_get "${upper}"
}

ps_site_port() {
  local country="$1"
  local upper port
  upper="APP_DOMAIN_$(ps_country_upper "${country}")_PORT"
  port="$(ps_env_get "${upper}")"
  if [[ -n "${port}" ]]; then
    printf '%s' "${port}"
    return 0
  fi
  if [[ "$(ps_env_get APP_ENV dev)" == "dev" ]]; then
    ps_country_dev_port "${country}"
    return 0
  fi
  printf '80'
}

ps_site_uri() {
  local country="$1"
  local domain port
  domain="$(ps_site_domain "${country}")"
  port="$(ps_site_port "${country}")"
  [[ -n "${domain}" ]] || ps_die "Missing APP_DOMAIN_$(ps_country_upper "${country}") in configuration"
  if [[ "${port}" == "80" ]]; then
    printf 'http://%s' "${domain}"
  else
    printf 'http://%s:%s' "${domain}" "${port}"
  fi
}

ps_site_admin_uri() {
  local country="$1"
  local upper domain port
  upper="$(ps_country_upper "${country}")"
  domain="$(ps_env_get "APP_DOMAIN_${upper}_ADMIN")"
  [[ -n "${domain}" ]] || return 1
  port="$(ps_site_port "${country}")"
  if [[ "${port}" == "80" ]]; then
    printf 'http://%s' "${domain}"
  else
    printf 'http://%s:%s' "${domain}" "${port}"
  fi
}

ps_public_files_dir() {
  local country="$1"
  local site_dir base rel
  site_dir="$(ps_country_site_dir "${country}")"
  base="$(ps_env_path_base APP_PUBLIC_PATH)"
  if [[ -n "${base}" ]]; then
    rel="${base%/}/${site_dir}/files"
    printf '%s/web/%s' "${PS_SRC_DIR}" "${rel#/}"
  else
    printf '%s/web/sites/%s/files' "${PS_SRC_DIR}" "${site_dir}"
  fi
}

# Global env base + /{site_dir} (mirrors load-env.php). No per-country APP_*_{CODE} vars.
ps_path_from_global_base() {
  local base="$1"
  local site_dir="$2"
  if [[ "${base}" == /* ]]; then
    printf '%s/%s' "${base%/}" "${site_dir}"
  else
    printf '%s/%s/%s' "${PS_SRC_DIR}" "${base#/}" "${site_dir}"
  fi
}

ps_resolve_private_files_dir() {
  local country="$1"
  local base site_dir
  site_dir="$(ps_country_site_dir "${country}")"
  base="$(ps_env_path_base APP_PRIVATE_PATH)"
  if [[ -n "${base}" ]]; then
    ps_path_from_global_base "${base}" "${site_dir}"
    return 0
  fi
  printf '%s/private/%s' "${PS_SRC_DIR}" "${site_dir}"
}

ps_private_files_dir() {
  ps_resolve_private_files_dir "$1"
}

ps_private_path_is_configured() {
  [[ -n "$(ps_env_path_base APP_PRIVATE_PATH)" ]]
}

ps_private_path_should_provision() {
  [[ "$(ps_env_get APP_ENV dev)" == "dev" ]] || return 1
  ps_private_path_is_configured && return 1
  return 0
}

ps_crm_xml_target() {
  printf '%s/crm/offers.xml' "$(ps_public_files_dir "$1")"
}

# Solr core name for a country (SOLR_CORE_{CODE} in .env).
ps_solr_core_name() {
  local country="$1"
  local upper var
  upper="$(ps_country_upper "${country}")"
  var="SOLR_CORE_${upper}"
  ps_env_get "${var}"
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
    if ps_is_country_code "${token}"; then
      selected+=("${token}")
    else
      ps_die "Unknown country code: ${token}"
    fi
  done
  [[ ${#selected[@]} -gt 0 ]] || ps_die "No valid countries in: ${raw}"
  printf '%s\n' "${selected[@]}"
}

ps_foreach_country() {
  local callback="$1"
  local country
  ps_countries_init
  for country in "${_PS_COUNTRIES_CACHE[@]}"; do
    "${callback}" "${country}"
  done
}

ps_provision_country_files() {
  local country="$1"
  local public_dir private_dir
  public_dir="$(ps_public_files_dir "${country}")"
  private_dir="$(ps_resolve_private_files_dir "${country}")"

  mkdir -p \
    "${public_dir}/crm" \
    "${public_dir}/translations" \
    "${public_dir}/bnp-media/placeholders"

  if ps_private_path_should_provision; then
    mkdir -p "${private_dir}"
  elif [[ ! -d "${private_dir}" ]]; then
    ps_warn "${country}: private path not found (expected mount or pre-provisioned): ${private_dir}"
  fi

  ps_success "${country}: public=${public_dir} private=${private_dir}"
}

ps_stage_sample_xml() {
  local country="$1"
  local master target
  master="${PS_REPO_ROOT}/data/xml/bnppre_sample_100_per_type.xml"
  target="$(ps_crm_xml_target "${country}")"
  ps_require_file "${master}" "Master sample XML not found: ${master} (run: python3 data/xml/build_sample_100_per_type.py)"
  mkdir -p "$(dirname "${target}")"
  cp "${master}" "${target}"
  ps_info "XML staged: ${target}"
}

ps_site_config_split_id() {
  printf 'site_%s' "$1"
}

# Backward-compatible alias for scripts referencing the old split id.
ps_site_language_split_id() {
  ps_site_config_split_id "$1"
}

ps_add_site_languages() {
  local country="$1"
  local default_lang lang

  default_lang="$(ps_site_default_langcode "${country}")"
  ps_info "Adding languages for ${country} (default=${default_lang})..."

  for lang in $(ps_site_language_codes "${country}"); do
    if ps_drush language:info 2>/dev/null | grep -q "(${lang})"; then
      continue
    fi
    ps_retry 2 2 ps_drush language:add "${lang}" --skip-translations -y \
      || ps_warn "Could not add language ${lang} on ${country}"
  done
  ps_drush config:set -y system.site default_langcode "${default_lang}"
}

	ps_apply_site_language_negotiation() {
  local country="$1"
  local file="${PS_SRC_DIR}/config/sites/${country}/language.negotiation.yml"
  if [[ ! -f "${file}" ]]; then
    ps_warn "No language.negotiation in config/sites/${country} (run: make seed-site-configs)"
    return 0
  fi
  ps_info "Applying language.negotiation for ${country}..."
  local partial_dir="${PS_SRC_DIR}/tmp/lang-neg-${country}"
  rm -rf "${partial_dir}"
  mkdir -p "${partial_dir}"
  cp "${file}" "${partial_dir}/"
  ps_drush config:import -y --partial --source="${partial_dir}" \
    || ps_warn "language.negotiation import failed for ${country}"
  rm -rf "${partial_dir}"
}

ps_bootstrap_site_config_split() {
  local country="$1"
  ps_warn "Config Split per country removed — language.negotiation is in config/sites/${country}/"
  ps_apply_site_language_negotiation "${country}"
}

ps_apply_site_config_overrides() {
  ps_apply_site_language_negotiation "$1"
}

ps_import_active_language_config_overrides() {
  local country="$1"
  local lang
  for lang in $(ps_site_language_codes "${country}"); do
    ps_info "Importing config overrides for lang=${lang} (${country})..."
    ps_drush_import_language_config_overrides "${lang}"
  done
}
