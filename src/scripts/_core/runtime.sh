#!/usr/bin/env bash
# Host runtime helpers — Drush and npm on WSL/host (no Docker in src scripts).

ps_npm_install_cmd() {
  local dir="$1"
  if [[ -f "${dir}/package-lock.json" ]]; then
    printf '%s' 'npm ci --no-audit --no-fund'
  else
    printf '%s' 'npm install --no-save --no-audit --no-fund'
  fi
}

ps_npm_usable_on_host() {
  local npm_path node_path
  npm_path="$(command -v npm 2>/dev/null || true)"
  node_path="$(command -v node 2>/dev/null || true)"
  [[ -n "${npm_path}" && -n "${node_path}" ]] || return 1
  [[ "${npm_path}" != /mnt/c/* && "${node_path}" != /mnt/c/* ]] || return 1
  node --version >/dev/null 2>&1 && npm --version >/dev/null 2>&1
}

ps_npm_exec() {
  local cwd="$1"
  shift
  ps_require_cmd npm
  ps_require_cmd node
  ( cd "${cwd}" && "$@" )
}

ps_link_theme_bootstrap_library() {
  local bootstrap_src="${PS_WEB_DIR}/themes/custom/ui_suite_bnp/node_modules/bootstrap"
  local lib_dest="${PS_WEB_DIR}/libraries/bootstrap"

  ps_require_file "${bootstrap_src}/scss/_functions.scss" \
    "Bootstrap not found in ui_suite_bnp (build ui_suite_bnp theme first)"

  mkdir -p "${PS_WEB_DIR}/libraries"
  rm -rf "${lib_dest}"
  cp -a "${bootstrap_src}" "${lib_dest}"
  ps_success "Bootstrap copied to web/libraries/bootstrap"
}

ps_resolve_runtime() {
  if [[ -n "${PS_RUNTIME:-}" ]]; then
    return 0
  fi
  ps_load_config
  [[ -x "${PS_SRC_DIR}/vendor/bin/drush" ]] \
    || ps_die "Drush not found. Run: composer install (in src/)"
  # Check if we're in a container (/.dockerenv exists)
  if [[ -f /.dockerenv ]]; then
    PS_RUNTIME="container"
    export PS_RUNTIME
    return 0
  fi
  # Check if host PHP is not available
  if ! command -v php >/dev/null 2>&1; then
    # Check if container is running
    if command -v docker >/dev/null 2>&1 && docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "ps_php"; then
      PS_RUNTIME="container"
      export PS_RUNTIME
      return 0
    fi
  fi
  PS_RUNTIME="host"
  export PS_RUNTIME
}

ps_php_exec() {
  local script="$1"
  shift
  # Try host PHP first, then fall back to Docker container
  if command -v php >/dev/null 2>&1; then
    php "${script}" "$@"
  elif command -v docker >/dev/null 2>&1 && docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "ps_php"; then
    local args=""
    for arg in "$@"; do
      args="${args} $(printf '%s' "$arg" | sed "s/'/'\\\\''/g")"
    done
    docker exec ps_php sh -c "cd /var/www/html && php scripts/_core/$(basename "${script}")${args}"
  else
    ps_die "PHP not found on host and ps_php container not running"
  fi
}
