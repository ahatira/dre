#!/usr/bin/env bash
# Fix node_modules ownership after npm/gulp run as root in Docker (WSL bind mount).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
CONTAINER="${PS_PHP_CONTAINER:-ps_php}"
SRC="${ROOT}/src"
UID_GID="$(id -u):$(id -g)"

PATHS=(
  "${SRC}/node_modules"
  "${SRC}/web/themes/custom/ui_suite_bnp/node_modules"
  "${SRC}/web/themes/custom/ps_theme/node_modules"
)

fix_host() {
  local path
  for path in "${PATHS[@]}"; do
    [[ -d "${path}" ]] || continue
    if [[ -O "${path}" && -w "${path}" ]]; then
      echo "OK ${path}"
      continue
    fi
    echo "Fixing ${path} → ${UID_GID}"
    if chown -R "${UID_GID}" "${path}" 2>/dev/null; then
      continue
    fi
    sudo chown -R "${UID_GID}" "${path}"
  done
}

if docker ps --filter "name=${CONTAINER}" --filter "status=running" --format '{{.Names}}' 2>/dev/null | grep -qx "${CONTAINER}"; then
  echo "Fixing node_modules via ${CONTAINER}..."
  docker exec -u root "${CONTAINER}" chown -R "${UID_GID}" \
    /var/www/html/node_modules \
    /var/www/html/web/themes/custom/ui_suite_bnp/node_modules \
    /var/www/html/web/themes/custom/ps_theme/node_modules \
    2>/dev/null || true
fi

fix_host
echo "node_modules permissions OK"
