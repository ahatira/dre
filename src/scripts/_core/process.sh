#!/usr/bin/env bash

ps_retry() {
  local attempts="$1"
  local delay_seconds="$2"
  shift 2

  local n=1
  until "$@"; do
    if [[ "${n}" -ge "${attempts}" ]]; then
      ps_error "Command failed after ${attempts} attempts: $*"
      return 1
    fi
    ps_warn "Attempt ${n} failed, retrying in ${delay_seconds}s: $*"
    sleep "${delay_seconds}"
    n=$((n + 1))
  done
}

ps_timed_run() {
  local label="$1"
  shift

  local start_ts
  start_ts="$(ps_now_epoch)"
  ps_info "START: ${label}"

  "$@"

  ps_info "END: ${label} (elapsed: $(ps_elapsed_seconds "${start_ts}")s)"
}

ps_diag_summary() {
  if ! ps_env_bool "${PS_DIAG:-0}"; then
    return 0
  fi

  ps_info "Diagnostics context"
  ps_info "  Execution mode (resolved): $(ps_resolve_exec_mode)"
  ps_info "  OS: $(ps_detect_os)"
  ps_info "  Project root: ${PS_PROJECT_ROOT}"
  ps_info "  Source root: ${PS_SRC_DIR}"
  ps_info "  PHP container: ${PS_PHP_CONTAINER}"
  ps_info "  DB container: ${PS_DB_CONTAINER}"
}
