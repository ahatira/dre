#!/usr/bin/env bash
# Bi-mode configuration: dev (.env) | int/staging/prod (system environment).

ps_load_config() {
  if [[ -n "${PS_CONFIG_LOADED:-}" ]]; then
    return 0
  fi
  PS_CONFIG_LOADED=1

  PS_APP_ENV="${APP_ENV:-dev}"
  if [[ "${PS_APP_ENV}" == "dev" && -f "${PS_SRC_DIR}/.env" ]]; then
    set -a
    # shellcheck disable=SC1090
    source "${PS_SRC_DIR}/.env"
    set +a
  fi
}

ps_env_get() {
  local key="$1"
  local default="${2:-}"
  ps_load_config
  local value="${!key:-}"
  if [[ -n "${value}" ]]; then
    printf '%s' "${value}"
  else
    printf '%s' "${default}"
  fi
}

# Path env vars: unset, empty or whitespace-only → treated as unset.
ps_env_path_base() {
  local key="$1"
  local value
  value="$(ps_env_get "${key}")"
  value="${value#"${value%%[![:space:]]*}"}"
  value="${value%"${value##*[![:space:]]}"}"
  printf '%s' "${value}"
}

ps_require_env() {
  local key="$1"
  local value
  value="$(ps_env_get "${key}")"
  if [[ -z "${value}" ]]; then
    ps_die "Missing required config: ${key} (set in src/.env for dev or export for prod)"
  fi
  printf '%s' "${value}"
}
