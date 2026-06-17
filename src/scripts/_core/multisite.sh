#!/usr/bin/env bash
# Multisite helpers — countries, URIs, languages, file paths.

readonly PS_COUNTRIES=(com be es fr ie it lu nl pl)

ps_multisite_countries() {
  printf '%s\n' "${PS_COUNTRIES[@]}"
}

ps_country_upper() {
  printf '%s' "$1" | tr '[:lower:]' '[:upper:]'
}

ps_is_country_code() {
  case "$1" in
    com|be|es|fr|ie|it|lu|nl|pl|all) return 0 ;;
    *) return 1 ;;
  esac
}

ps_site_domain() {
  local country="$1"
  local upper="APP_DOMAIN_$(ps_country_upper "${country}")"
  ps_env_get "${upper}"
}

ps_site_port() {
  local country="$1"
  local upper="APP_DOMAIN_$(ps_country_upper "${country}")_PORT"
  ps_env_get "${upper}" "8080"
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
  printf '%s/web/sites/%s/files' "${PS_SRC_DIR}" "$1"
}

ps_private_files_dir() {
  printf '%s/private/%s' "${PS_SRC_DIR}" "$1"
}

ps_crm_xml_target() {
  printf '%s/crm/offers.xml' "$(ps_public_files_dir "$1")"
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
      com|be|es|fr|ie|it|lu|nl|pl) selected+=("${token}") ;;
      *) ps_die "Unknown country code: ${token}" ;;
    esac
  done
  [[ ${#selected[@]} -gt 0 ]] || ps_die "No valid countries in: ${raw}"
  printf '%s\n' "${selected[@]}"
}

ps_foreach_country() {
  local callback="$1"
  local country
  for country in "${PS_COUNTRIES[@]}"; do
    "${callback}" "${country}"
  done
}

ps_provision_country_files() {
  local country="$1"
  local public_dir private_dir
  public_dir="$(ps_public_files_dir "${country}")"
  private_dir="$(ps_private_files_dir "${country}")"

  mkdir -p \
    "${public_dir}/crm" \
    "${public_dir}/translations" \
    "${public_dir}/bnp-media/placeholders" \
    "${private_dir}"

  if ps_in_docker; then
    ps_docker_exec_php "chown -R www-data:www-data web/sites/${country}/files private/${country}" \
      2>/dev/null || ps_warn "chown failed for ${country} (run make fix-permissions)"
  fi
  ps_success "${country}: public=${public_dir} private=${private_dir}"
}

ps_stage_sample_xml() {
  local country="$1"
  local master target
  master="${PS_PROJECT_ROOT}/data/xml/bnppre_sample_100_per_type.xml"
  target="$(ps_crm_xml_target "${country}")"
  ps_require_file "${master}" "Master sample XML not found: ${master} (run: python3 data/xml/build_sample_100_per_type.py)"
  mkdir -p "$(dirname "${target}")"
  cp "${master}" "${target}"
  ps_info "XML staged: ${target}"
}

# --- Languages (per-country matrix) ---

ps_site_default_langcode() {
  case "$1" in
    com|ie) printf 'en' ;;
    fr|be|lu) printf 'fr' ;;
    es) printf 'es' ;;
    it) printf 'it' ;;
    nl) printf 'nl' ;;
    pl) printf 'pl' ;;
    *) ps_die "Unknown country for default lang: $1" ;;
  esac
}

ps_site_language_codes() {
  case "$1" in
    com) printf 'en fr' ;;
    fr) printf 'fr en' ;;
    be) printf 'en fr nl' ;;
    es) printf 'en es' ;;
    ie) printf 'en' ;;
    it) printf 'en it' ;;
    lu) printf 'en fr' ;;
    pl) printf 'en pl' ;;
    nl) printf 'en nl' ;;
    *) ps_die "Unknown country for languages: $1" ;;
  esac
}

ps_site_language_split_id() {
  printf 'language_%s' "$1"
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
  ps_require_file "${PS_SRC_DIR}/config/env/languages/${country}/language.negotiation.yml" \
    "Missing language.negotiation for ${country}"
  ps_info "Applying language negotiation for ${country}..."
  ps_retry 2 2 ps_drush ev '
    \Drupal::service("ps_core.site_language_negotiation_installer")->applyForCountry("'${country}'");
    echo "Language negotiation applied\n";
  ' || ps_warn "language.negotiation apply failed for ${country}"
  ps_drush_cr
}

ps_import_active_language_config_overrides() {
  local country="$1"
  local lang
  for lang in $(ps_site_language_codes "${country}"); do
    ps_info "Importing config overrides for lang=${lang} (${country})..."
    ps_drush_import_language_config_overrides "${lang}"
  done
}
