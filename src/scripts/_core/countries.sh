#!/usr/bin/env bash
# Country manifest helpers — reads web/sites/countries.yml via countries-cli.php.

_PS_COUNTRIES_CLI="${PS_CORE_DIR}/countries-cli.php"
_PS_COUNTRIES_CACHE=()

ps_countries_cli() {
  [[ -f "${_PS_COUNTRIES_CLI}" ]] || ps_die "Missing countries CLI: ${_PS_COUNTRIES_CLI}"
  php "${_PS_COUNTRIES_CLI}" "$@"
}

ps_countries_init() {
  if [[ ${#_PS_COUNTRIES_CACHE[@]} -gt 0 ]]; then
    return 0
  fi
  mapfile -t _PS_COUNTRIES_CACHE < <(ps_countries_cli codes)
  [[ ${#_PS_COUNTRIES_CACHE[@]} -gt 0 ]] || ps_die "No countries defined in web/sites/countries.yml"
}

ps_multisite_countries() {
  ps_countries_init
  printf '%s\n' "${_PS_COUNTRIES_CACHE[@]}"
}

ps_is_country_code() {
  case "$1" in
    all) return 0 ;;
  esac
  ps_countries_cli is_valid "$1" | grep -q '^1$'
}

ps_site_default_langcode() {
  ps_countries_cli default_lang "$1"
}

ps_site_language_codes() {
  ps_countries_cli languages "$1"
}

ps_country_dev_port() {
  ps_countries_cli dev_port "$1"
}

ps_country_site_dir() {
  ps_countries_cli site_dir "$1"
}
