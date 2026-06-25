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

# Removes node_modules when not owned/writable by the current user (common after Docker npm).
ps_npm_prepare_dir() {
  local dir="$1"
  local nm="${dir}/node_modules"
  [[ -d "${nm}" ]] || return 0

  if [[ -O "${nm}" && -w "${nm}" ]]; then
    return 0
  fi

  ps_warn "Removing ${nm} (wrong owner — run npm on WSL host, not docker exec)"
  ps_npm_remove_node_modules "${dir}"
}

ps_npm_remove_node_modules() {
  local dir="$1"
  local nm="${dir}/node_modules"
  [[ -d "${nm}" ]] || return 0

  if rm -rf "${nm}" 2>/dev/null; then
    return 0
  fi

  ps_die "Cannot remove ${nm} (permission denied). Run: make fix-npm-permissions  (or: sudo rm -rf '${nm}')"
}

# npm ci with auto-clean retry on ENOTEMPTY / EACCES from stale node_modules.
ps_npm_install_dir() {
  local dir="$1"
  local install_cmd
  install_cmd="$(ps_npm_install_cmd "${dir}")"

  ps_npm_prepare_dir "${dir}"
  if ps_npm_exec "${dir}" sh -lc "${install_cmd}"; then
    return 0
  fi

  ps_warn "npm failed in ${dir} — cleaning node_modules and retrying"
  ps_npm_remove_node_modules "${dir}"
  ps_npm_exec "${dir}" sh -lc "${install_cmd}"
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

ps_resolve_runtime() {
  if [[ -n "${PS_RUNTIME:-}" ]]; then
    return 0
  fi
  ps_load_config
  [[ -x "${PS_SRC_DIR}/vendor/bin/drush" ]] \
    || ps_die "Drush not found. Run: composer install (in src/)"
  PS_RUNTIME="host"
  export PS_RUNTIME
}
