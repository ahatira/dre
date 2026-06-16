#!/usr/bin/env bash
# Structured colored logging.

PS_COLOR_RESET='\033[0m'
PS_COLOR_INFO='\033[36m'
PS_COLOR_SUCCESS='\033[32m'
PS_COLOR_WARN='\033[33m'
PS_COLOR_ERROR='\033[31m'
PS_COLOR_HEADER='\033[1;35m'

ps_header() {
  echo ""
  echo -e "${PS_COLOR_HEADER}==> $*${PS_COLOR_RESET}"
}

ps_info() {
  echo -e "${PS_COLOR_INFO}[INFO]${PS_COLOR_RESET} $*"
}

ps_success() {
  echo -e "${PS_COLOR_SUCCESS}[OK]${PS_COLOR_RESET} $*"
}

ps_warn() {
  echo -e "${PS_COLOR_WARN}[WARN]${PS_COLOR_RESET} $*" >&2
}

ps_error() {
  echo -e "${PS_COLOR_ERROR}[ERROR]${PS_COLOR_RESET} $*" >&2
}

ps_die() {
  ps_error "$*"
  exit 1
}

ps_enable_error_trap() {
  trap 'ps_error "Failed at line ${LINENO}: ${BASH_COMMAND}"; exit 1' ERR
}
