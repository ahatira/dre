#!/usr/bin/env bash
# Bootstrap — load shared modules (source once).
set -Eeuo pipefail

if [[ -n "${PS_BOOTSTRAP_LOADED:-}" ]]; then
  return 0
fi
PS_BOOTSTRAP_LOADED=1

PS_CORE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PS_SCRIPTS_DIR="$(dirname "${PS_CORE_DIR}")"
PS_SRC_DIR="$(dirname "${PS_SCRIPTS_DIR}")"
PS_PROJECT_ROOT="$(dirname "${PS_SRC_DIR}")"
PS_WEB_DIR="${PS_SRC_DIR}/web"

PS_DOCKER_COMPOSE_FILE="${PS_PROJECT_ROOT}/docker/docker-compose.yml"
PS_PHP_CONTAINER="${PS_PHP_CONTAINER:-ps_php}"
PS_DB_CONTAINER="${PS_DB_CONTAINER:-ps_postgres}"
PS_DRUPAL_ROOT="/var/www/html"
PS_HTTP_PORT="${PS_HTTP_PORT:-8080}"

# shellcheck source=/dev/null
source "${PS_CORE_DIR}/config.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/log.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/runtime.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/drush.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/multisite.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/helpers.sh"

ps_enable_error_trap
