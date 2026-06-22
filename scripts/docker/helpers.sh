#!/usr/bin/env bash
# Docker helper functions — ensure container is running before executing commands.

# Default container names
: "${PS_PHP_CONTAINER:=ps_php}"
: "${PS_NGINX_CONTAINER:=ps_nginx}"
: "${PS_POSTGRES_CONTAINER:=ps_postgres}"
: "${PS_SOLR_CONTAINER:=ps_solr}"

# Check if a container is running
ps_container_running() {
  local container="$1"
  docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${container}"
}

# Execute command in PHP container with error handling
ps_php_exec() {
  local cmd="$1"
  shift
  if ! ps_container_running "${PS_PHP_CONTAINER}"; then
    echo "Container ${PS_PHP_CONTAINER} not running — run: make up" >&2
    return 1
  fi
  docker exec "${PS_PHP_CONTAINER}" sh -c "cd /var/www/html && ${cmd}" "$@"
}

# Execute PHP script in container
ps_php_run() {
  local script="$1"
  shift
  ps_php_exec "php scripts/_core/${script}" "$@"
}

# Execute composer in container
ps_composer() {
  local args="$1"
  shift
  ps_php_exec "COMPOSER_PROCESS_TIMEOUT=2000 composer ${args}"
}

# Execute Drush in container
ps_drush_container() {
  local alias="${1:-@ps.com}"
  shift
  ps_php_exec "vendor/bin/drush ${alias}" "$@"
}

# Execute npm in container
ps_npm_container() {
  local dir="$1"
  shift
  ps_php_exec "cd ${dir} && npm ${@}"
}

# Wait for container to be ready
ps_wait_container() {
  local container="$1"
  local timeout="${2:-30}"
  local count=0
  while ! ps_container_running "${container}"; do
    if [[ ${count} -ge ${timeout} ]]; then
      echo "Timeout waiting for ${container}" >&2
      return 1
    fi
    sleep 1
    count=$((count + 1))
  done
  return 0
}
