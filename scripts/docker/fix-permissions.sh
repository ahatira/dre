#!/usr/bin/env bash
# Fix ownership for Drupal writable paths inside ps_php (WSL dev).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
CONTAINER="${PS_PHP_CONTAINER:-ps_php}"

if ! docker ps --filter "name=${CONTAINER}" --filter "status=running" --format '{{.Names}}' | grep -qx "${CONTAINER}"; then
  echo "Container ${CONTAINER} not running — run: make up" >&2
  exit 1
fi

docker exec -i "${CONTAINER}" chown -R www-data:www-data \
  /var/www/html/config/sync \
  /var/www/html/web/modules/custom \
  /var/www/html/web/themes/custom

echo "Permissions fixed in ${CONTAINER}"
