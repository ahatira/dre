#!/usr/bin/env bash
# Helpers - Utility functions (retry, validation, etc.)

ps_retry() {
  local attempts="$1"
  local delay="$2"
  shift 2

  local n=1
  until "$@"; do
    if [[ ${n} -ge ${attempts} ]]; then
      ps_error "Command failed after ${attempts} attempts"
      return 1
    fi
    ps_warn "Attempt ${n}/${attempts} failed, retrying in ${delay}s..."
    sleep "${delay}"
    n=$((n + 1))
  done
}

ps_require_cmd() {
  local cmd="$1"
  command -v "${cmd}" >/dev/null 2>&1 || ps_die "Required command not found: ${cmd}"
}

ps_require_file() {
  local file="$1"
  [[ -f "${file}" ]] || ps_die "Required file not found: ${file}"
}

ps_drush_bootstrapped() {
  ps_drush status --field=bootstrap 2>/dev/null | grep -qi successful
}
