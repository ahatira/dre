#!/usr/bin/env bash

ps_require_file() {
  local file_path="$1"
  [[ -f "${file_path}" ]] || ps_die "Missing required file: ${file_path}"
}

ps_require_dir() {
  local dir_path="$1"
  [[ -d "${dir_path}" ]] || ps_die "Missing required directory: ${dir_path}"
}

ps_require_container_running() {
  local container_name="$1"
  docker ps --filter "name=${container_name}" --filter "status=running" --format '{{.Names}}' | grep -qx "${container_name}" \
    || ps_die "Container is not running: ${container_name}"
}
