#!/usr/bin/env bash
# Drush - Drush command wrapper with Docker support

ps_drush() {
  if ps_in_docker; then
    local quoted_args=()
    for arg in "$@"; do
      quoted_args+=("$(printf '%q' "${arg}")")
    done
    ps_docker_exec_php "vendor/bin/drush ${quoted_args[*]}"
  else
    if [[ ! -x "${PS_SRC_DIR}/vendor/bin/drush" ]]; then
      ps_die "Drush not found at ${PS_SRC_DIR}/vendor/bin/drush"
    fi
    (cd "${PS_SRC_DIR}" && vendor/bin/drush "$@")
  fi
}

ps_drush_cr() {
  ps_drush cache:rebuild "$@"
}
