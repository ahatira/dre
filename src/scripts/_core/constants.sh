#!/usr/bin/env bash
# Constants - Global project variables

readonly PS_CORE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
readonly PS_SCRIPTS_DIR="$(dirname "${PS_CORE_DIR}")"
readonly PS_SRC_DIR="$(dirname "${PS_SCRIPTS_DIR}")"
readonly PS_PROJECT_ROOT="$(dirname "${PS_SRC_DIR}")"

# Docker
readonly PS_DOCKER_COMPOSE_FILE="${PS_PROJECT_ROOT}/docker/docker-compose.yml"
readonly PS_PHP_CONTAINER="${PS_PHP_CONTAINER:-ps_php}"
readonly PS_DB_CONTAINER="${PS_DB_CONTAINER:-ps_postgres}"

# Drupal
readonly PS_DRUPAL_ROOT="/var/www/html"
readonly PS_WEB_DIR="${PS_SRC_DIR}/web"

# HTTP
readonly PS_HTTP_PORT="${PS_HTTP_PORT:-8080}"
readonly PS_HTTP_URL="http://localhost:${PS_HTTP_PORT}"
