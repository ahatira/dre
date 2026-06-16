#!/usr/bin/env bash
# Drush - Drush command wrapper with Docker support

ps_drush() {
  local drush_args=()
  if [[ -n "${PS_DRUSH_URI:-}" ]]; then
    drush_args+=(--uri="${PS_DRUSH_URI}")
  fi

  if ps_in_docker; then
    local quoted_args=()
    for arg in "${drush_args[@]}" "$@"; do
      quoted_args+=("$(printf '%q' "${arg}")")
    done
    ps_docker_exec_php "vendor/bin/drush ${quoted_args[*]}"
  else
    if [[ ! -x "${PS_SRC_DIR}/vendor/bin/drush" ]]; then
      ps_die "Drush not found at ${PS_SRC_DIR}/vendor/bin/drush"
    fi
    (cd "${PS_SRC_DIR}" && vendor/bin/drush "${drush_args[@]}" "$@")
  fi
}

ps_drush_cr() {
  ps_drush cache:rebuild "$@"
}

# Count published offers without requiring psql in the PHP container.
ps_drush_published_offer_count() {
  ps_drush ev 'echo (int) \Drupal::entityTypeManager()->getStorage("node")->getQuery()->accessCheck(FALSE)->condition("type","offer")->condition("status",1)->count()->execute();' 2>/dev/null | tr -d '[:space:]'
}
