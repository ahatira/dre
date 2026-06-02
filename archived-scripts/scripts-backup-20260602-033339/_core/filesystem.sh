#!/usr/bin/env bash

ps_ensure_dir() {
  local dir_path="$1"
  mkdir -p "${dir_path}"
}

ps_remove_if_exists() {
  local path="$1"
  [[ -e "${path}" ]] && rm -rf "${path}"
}

ps_abs_path() {
  local target="$1"
  if [[ -d "${target}" ]]; then
    (cd "${target}" && pwd)
  else
    local base_dir
    base_dir="$(cd "$(dirname "${target}")" && pwd)"
    echo "${base_dir}/$(basename "${target}")"
  fi
}
