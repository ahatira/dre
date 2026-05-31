#!/usr/bin/env bash

ps_require_cmd() {
  local cmd="$1"
  command -v "${cmd}" >/dev/null 2>&1 || ps_die "Required command not found: ${cmd}"
}

ps_detect_os() {
  case "$(uname -s)" in
    Linux) echo "linux" ;;
    Darwin) echo "macos" ;;
    *) echo "unknown" ;;
  esac
}

ps_env_bool() {
  local value="${1:-0}"
  [[ "${value}" == "1" || "${value}" == "true" || "${value}" == "yes" ]]
}

ps_validate_exec_mode() {
  case "${PS_EXEC_MODE}" in
    auto|docker|local)
      return 0
      ;;
    *)
      ps_die "Invalid PS_EXEC_MODE='${PS_EXEC_MODE}'. Expected: auto|docker|local"
      ;;
  esac
}

ps_resolve_exec_mode() {
  ps_validate_exec_mode

  if [[ "${PS_EXEC_MODE}" == "docker" || "${PS_EXEC_MODE}" == "local" ]]; then
    echo "${PS_EXEC_MODE}"
    return 0
  fi

  if ps_docker_is_ready "${PS_PHP_CONTAINER}"; then
    echo "docker"
  else
    echo "local"
  fi
}
