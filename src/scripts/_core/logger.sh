#!/usr/bin/env bash

ps_log() {
  local level="$1"
  local color="$2"
  shift 2
  printf "%b[%s] [%s] %s%b\n" "${color}" "$(ps_now_utc)" "${level}" "$*" "${PS_COLOR_RESET}" >&2
}

ps_info() {
  ps_log "INFO" "${PS_COLOR_BLUE}" "$@"
}

ps_success() {
  ps_log "OK" "${PS_COLOR_GREEN}" "$@"
}

ps_warn() {
  ps_log "WARN" "${PS_COLOR_YELLOW}" "$@"
}

ps_error() {
  ps_log "ERROR" "${PS_COLOR_RED}" "$@"
}

ps_debug() {
  if [[ "${PS_DEBUG:-0}" == "1" ]]; then
    ps_log "DEBUG" "${PS_COLOR_GRAY}" "$@"
  fi
}
