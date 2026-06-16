#!/usr/bin/env bash
# Docker - Docker and container utilities

ps_docker_available() {
  command -v docker >/dev/null 2>&1
}

ps_docker_container_running() {
  local container="$1"
  docker ps --filter "name=${container}" --filter "status=running" --format '{{.Names}}' 2>/dev/null | grep -qx "${container}"
}

ps_docker_compose() {
  docker compose -f "${PS_DOCKER_COMPOSE_FILE}" "$@"
}

ps_docker_up() {
  ps_docker_compose up -d
}

ps_docker_exec() {
  local container="$1"
  shift
  docker exec -i "${container}" "$@"
}

ps_docker_exec_php() {
  # Run as www-data (uid 1000) so bind-mounted files stay writable on the WSL host.
  docker exec -u www-data -i "${PS_PHP_CONTAINER}" bash -c "cd ${PS_DRUPAL_ROOT} && $*"
}

ps_docker_exec_db() {
  ps_docker_exec "${PS_DB_CONTAINER}" bash -c "$*"
}

ps_in_docker() {
  ps_docker_available && ps_docker_container_running "${PS_PHP_CONTAINER}"
}

# Create missing Solr cores for all countries (multisite local dev).
ps_solr_init_cores() {
  local init_script="${PS_PROJECT_ROOT}/docker/solr/init-cores.sh"
  local solr_container="${SOLR_CONTAINER:-ps_solr}"

  ps_require_file "${init_script}"
  if ! ps_docker_container_running "${solr_container}"; then
    ps_warn "Solr container not running (${solr_container}) — skipping core init"
    return 1
  fi
  chmod +x "${init_script}"
  SOLR_CONTAINER="${solr_container}" bash "${init_script}"
}
