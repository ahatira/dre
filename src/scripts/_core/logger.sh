#!/usr/bin/env bash
# Logger - Structured logging functions

ps_log() {
  local level="$1"
  local color="$2"
  shift 2
  printf "%b[%s] %s%b\n" "${color}" "${level}" "$*" "${PS_COLOR_RESET}" >&2
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

ps_header() {
  echo ""
  printf "%b==> %s%b\n" "${PS_COLOR_CYAN}" "$*" "${PS_COLOR_RESET}" >&2
  echo ""
}
