#!/usr/bin/env bash

ps_now_utc() {
  date -u +"%Y-%m-%dT%H:%M:%SZ"
}

ps_now_epoch() {
  date +%s
}

ps_elapsed_seconds() {
  local start_ts="$1"
  echo "$(( $(ps_now_epoch) - start_ts ))"
}
