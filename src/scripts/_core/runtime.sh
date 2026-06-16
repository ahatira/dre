#!/usr/bin/env bash
# Runtime detection — host-first Drush, Docker optional.

ps_docker_available() {
  command -v docker >/dev/null 2>&1
}

ps_docker_container_running() {
  local container="$1"
  ps_docker_available \
    && docker ps --filter "name=${container}" --filter "status=running" --format '{{.Names}}' 2>/dev/null \
    | grep -qx "${container}"
}

ps_in_docker() {
  ps_docker_container_running "${PS_PHP_CONTAINER}"
}

ps_docker_exec_php() {
  docker exec -u www-data -i "${PS_PHP_CONTAINER}" bash -c "cd ${PS_DRUPAL_ROOT} && $*"
}

ps_resolve_runtime() {
  if [[ -n "${PS_RUNTIME:-}" ]]; then
    return 0
  fi

  ps_load_config
  local db_host
  db_host="$(ps_env_get DB_HOST postgres)"

  # Docker Compose dev: DB_HOST=postgres resolves inside ps_php only.
  if ps_in_docker && [[ "${db_host}" == "postgres" || "${db_host}" == "db" ]]; then
    PS_RUNTIME="docker"
  elif [[ -x "${PS_SRC_DIR}/vendor/bin/drush" ]]; then
    PS_RUNTIME="host"
  elif ps_in_docker; then
    PS_RUNTIME="docker"
  else
    ps_die "Drush not found. Run: cd src && composer install — or start Docker (make up)"
  fi
  export PS_RUNTIME
}

ps_solr_init_cores() {
  local init_script="${PS_PROJECT_ROOT}/docker/solr/init-cores.sh"
  local solr_container="${SOLR_CONTAINER:-ps_solr}"

  if [[ ! -f "${init_script}" ]]; then
    ps_warn "Solr init script not found: ${init_script}"
    return 1
  fi
  if ! ps_docker_container_running "${solr_container}"; then
    ps_warn "Solr container not running (${solr_container})"
    return 1
  fi
  chmod +x "${init_script}"
  SOLR_CONTAINER="${solr_container}" bash "${init_script}"
}
