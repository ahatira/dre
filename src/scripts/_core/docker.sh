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
  ps_docker_exec "${PS_PHP_CONTAINER}" bash -c "cd ${PS_DRUPAL_ROOT} && $*"
}

ps_docker_exec_db() {
  ps_docker_exec "${PS_DB_CONTAINER}" bash -c "$*"
}

ps_in_docker() {
  ps_docker_available && ps_docker_container_running "${PS_PHP_CONTAINER}"
}
