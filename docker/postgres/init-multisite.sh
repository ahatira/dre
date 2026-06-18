#!/usr/bin/env bash
# Creates Property Search multisite databases on first PostgreSQL init.
# Reads DB_NAME_{CODE} from container environment (src/.env via docker-compose).
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

while IFS= read -r line; do
  case "${line}" in
    DB_NAME_*=*)
      create_db "${line#*=}"
      ;;
  esac
done < <(env | grep -E '^DB_NAME_' | sort)
