#!/usr/bin/env bash
# Shared helpers for module E2E shell scripts (host Drush, multisite URLs).
# shellcheck shell=bash

if [[ -n "${PS_E2E_COMMON_LOADED:-}" ]]; then
  return 0 2>/dev/null || true
fi
PS_E2E_COMMON_LOADED=1

_ps_e2e_calling_file="${BASH_SOURCE[1]:-${BASH_SOURCE[0]}}"

ps_e2e_find_src_dir() {
  local dir
  dir="$(cd "$(dirname "${_ps_e2e_calling_file}")" && pwd)"
  while [[ "${dir}" != "/" ]]; do
    if [[ -x "${dir}/vendor/bin/drush" && -d "${dir}/web" ]]; then
      printf '%s' "${dir}"
      return 0
    fi
    dir="$(dirname "${dir}")"
  done
  printf 'E2E: cannot find src/ (vendor/bin/drush). Run composer install in src/.\n' >&2
  return 1
}

PS_E2E_SRC_DIR="${PS_E2E_SRC_DIR:-$(ps_e2e_find_src_dir)}"
PS_E2E_COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-com}}"
PS_E2E_DRUSH_BIN="${PS_E2E_SRC_DIR}/vendor/bin/drush"
PS_E2E_DRUSH_ALIAS="@ps.${PS_E2E_COUNTRY}"

if [[ ! -x "${PS_E2E_DRUSH_BIN}" ]]; then
  echo "E2E: missing ${PS_E2E_DRUSH_BIN} (composer install in src/)" >&2
  exit 1
fi

PS_E2E_BASE_URL="${BASE_URL:-${BASE:-http://com.localhost:8080}}"
BASE="${PS_E2E_BASE_URL}"
BASE_URL="${PS_E2E_BASE_URL}"
export PS_E2E_SRC_DIR PS_E2E_COUNTRY PS_E2E_BASE_URL BASE BASE_URL

ps_e2e_drush() {
  (cd "${PS_E2E_SRC_DIR}" && "${PS_E2E_DRUSH_BIN}" "${PS_E2E_DRUSH_ALIAS}" "$@")
}
