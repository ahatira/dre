#!/usr/bin/env bash

ps_drush() {
  local mode
  mode="$(ps_resolve_exec_mode)"

  case "${mode}" in
    docker)
      local quoted_args=()
      local arg
      for arg in "$@"; do
        quoted_args+=("$(printf '%q' "${arg}")")
      done
      ps_docker_exec_php "vendor/bin/drush ${quoted_args[*]}"
      ;;
    local)
      if [[ ! -x "${PS_SRC_DIR}/vendor/bin/drush" ]]; then
        ps_die "Local drush not found at ${PS_SRC_DIR}/vendor/bin/drush"
      fi
      (
        cd "${PS_SRC_DIR}"
        ./vendor/bin/drush "$@"
      )
      ;;
    *)
      ps_die "Unsupported execution mode: ${mode}"
      ;;
  esac
}

ps_drush_bootstrapped() {
  ps_drush status --field=bootstrap 2>/dev/null | grep -qi successful
}
