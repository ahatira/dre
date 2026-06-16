#!/usr/bin/env bash
# Creates Property Search multisite databases on first PostgreSQL init.
set -euo pipefail

create_db() {
  local name="$1"
  if [[ -z "${name}" ]]; then
    return 0
  fi
  if psql -v ON_ERROR_STOP=1 --username "${POSTGRES_USER}" --dbname postgres -tAc "SELECT 1 FROM pg_database WHERE datname = '${name}'" | grep -q 1; then
    echo "Database already exists: ${name}"
    return 0
  fi
  echo "Creating database: ${name}"
  psql -v ON_ERROR_STOP=1 --username "${POSTGRES_USER}" --dbname postgres -c "CREATE DATABASE \"${name}\";"
}

create_db "${DB_NAME_COM:-}"
create_db "${DB_NAME_BE:-}"
create_db "${DB_NAME_ES:-}"
create_db "${DB_NAME_FR:-}"
create_db "${DB_NAME_IE:-}"
create_db "${DB_NAME_IT:-}"
create_db "${DB_NAME_LU:-}"
create_db "${DB_NAME_NL:-}"
create_db "${DB_NAME_PL:-}"
