#!/usr/bin/env bash
# Run ps_migrate Kernel tests (requires PostgreSQL SIMPLETEST_DB).
set -euo pipefail

SRC="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)"
ENV_FILE="${SRC}/.env"

if [[ -f "${ENV_FILE}" ]]; then
  set -a
  # shellcheck source=/dev/null
  source "${ENV_FILE}"
  set +a
fi

COUNTRY="${PS_E2E_COUNTRY:-${COUNTRY:-fr}}"
DB_VAR="DB_NAME_$(echo "${COUNTRY}" | tr '[:lower:]' '[:upper:]')"
DB_NAME="${!DB_VAR:-${DB_NAME:-drupal}}"
DB_USER="${DB_USER:-drupal}"
DB_PASSWORD="${DB_PASSWORD:-drupal}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"

export SIMPLETEST_DB="pgsql://${DB_USER}:${DB_PASSWORD}@${DB_HOST}:${DB_PORT}/${DB_NAME}"

echo "SIMPLETEST_DB host=${DB_HOST} db=${DB_NAME} (@ps.${COUNTRY})"
cd "${SRC}"
exec vendor/bin/phpunit -c web/core/phpunit.xml.dist \
  web/modules/custom/ps_migrate/tests/src/Kernel "$@"
