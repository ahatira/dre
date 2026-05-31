#!/usr/bin/env bash

ps_wait_for_http() {
  local url="$1"
  local retries="${2:-20}"
  local delay_seconds="${3:-2}"

  local i
  for ((i=1; i<=retries; i++)); do
    if curl -sSf --max-time 5 "${url}" >/dev/null 2>&1; then
      return 0
    fi
    sleep "${delay_seconds}"
  done

  ps_die "HTTP endpoint did not become ready: ${url}"
}
