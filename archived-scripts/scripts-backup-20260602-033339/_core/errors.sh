#!/usr/bin/env bash

ps_die() {
  ps_error "$*"
  exit 1
}

ps_on_error() {
  local exit_code="$1"
  local line_no="$2"
  ps_error "Command failed with exit code ${exit_code} at line ${line_no}."
  exit "${exit_code}"
}

ps_enable_error_trap() {
  trap 'ps_on_error $? $LINENO' ERR
}
