#!/usr/bin/env bash

ps_docker_command_exists() {
  command -v docker >/dev/null 2>&1
}

ps_docker_container_running() {
  local container_name="$1"
  docker ps --filter "name=${container_name}" --filter "status=running" --format '{{.Names}}' | grep -qx "${container_name}"
}

ps_docker_is_ready() {
  local container_name="$1"
  ps_docker_command_exists && ps_docker_container_running "${container_name}"
}

ps_docker_compose() {
  docker compose -f "${PS_DOCKER_COMPOSE_FILE}" "$@"
}

ps_docker_compose_wsl() {
  docker compose -f "${PS_DOCKER_COMPOSE_FILE}" -f "${PS_DOCKER_COMPOSE_WSL_FILE}" "$@"
}

ps_docker_up() {
  ps_docker_compose up -d
}

ps_docker_exec_php() {
  docker exec -i "${PS_PHP_CONTAINER}" sh -lc "cd ${PS_DRUPAL_ROOT} && $*"
}

ps_docker_exec_db() {
  docker exec -i "${PS_DB_CONTAINER}" sh -lc "$*"
}
