#!/usr/bin/env bash
# Validate multisite environment variables (run from repo root).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SRC="${ROOT}/src"

# shellcheck source=/dev/null
source "${SRC}/scripts/_core/bootstrap.sh"

ps_header "Verify multisite configuration"
ps_load_config

missing=0
checked=0

require_var() {
  local key="$1"
  local value
  value="$(ps_env_get "${key}")"
  if [[ -z "${value}" ]]; then
    ps_error "Missing: ${key}"
    missing=$((missing + 1))
    return 1
  fi
  checked=$((checked + 1))
  return 0
}

ps_info "APP_ENV=$(ps_env_get APP_ENV dev)"

for global_key in DB_USER DB_PASSWORD DB_HOST DB_PORT SOLR_HOST SOLR_PORT CACHE_HOST; do
  require_var "${global_key}" || true
done

ps_countries_init
for country in "${_PS_COUNTRIES_CACHE[@]}"; do
  upper="$(printf '%s' "${country}" | tr '[:lower:]' '[:upper:]')"
  ps_info "Country: ${country}"
  for key in \
    "APP_DOMAIN_${upper}" \
    "APP_DOMAIN_${upper}_ADMIN" \
    "APP_DOMAIN_${upper}_PORT" \
    "DB_NAME_${upper}" \
    "SOLR_CORE_${upper}"; do
    require_var "${key}" || true
  done
done

if [[ "${missing}" -gt 0 ]]; then
  ps_die "Multisite verify failed — ${missing} missing variable(s), ${checked} ok"
fi

ps_success "Multisite verify passed (${checked} variables)"
