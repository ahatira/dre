#!/usr/bin/env bash
# Creates Solr cores for each Property Search country (run after stack is up).
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="${PROJECT_ROOT}/src/.env"
COUNTRIES_CLI="${PROJECT_ROOT}/src/scripts/_core/countries-cli.php"
PHP_CONTAINER="${PS_PHP_CONTAINER:-ps_php}"
SOLR_CONTAINER="${SOLR_CONTAINER:-ps_solr}"
CORES_ROOT="/opt/solr/conf-export"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "Missing ${ENV_FILE}. Run: make env" >&2
  exit 1
fi

if [[ ! -f "${COUNTRIES_CLI}" ]]; then
  echo "Missing ${COUNTRIES_CLI}" >&2
  exit 1
fi

# Check if containers are running
if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${PHP_CONTAINER}"; then
  echo "Container ${PHP_CONTAINER} not running — run: make up" >&2
  exit 1
fi

if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${SOLR_CONTAINER}"; then
  echo "Container ${SOLR_CONTAINER} not running — run: make up" >&2
  exit 1
fi

set -a
# shellcheck disable=SC1090
source "${ENV_FILE}"
set +a

create_core() {
  local core="$1"
  if [[ -z "${core}" ]]; then
    return 0
  fi
  if docker exec "${SOLR_CONTAINER}" test -d "/var/solr/data/${core}"; then
    echo "Solr core exists: ${core}"
    return 0
  fi
  if ! docker exec "${SOLR_CONTAINER}" test -d "${CORES_ROOT}/${core}/conf"; then
    echo "Missing Solr core config: ${CORES_ROOT}/${core}/ (run: make export-solr)" >&2
    return 1
  fi
  echo "Creating Solr core: ${core}"
  docker exec "${SOLR_CONTAINER}" solr create -c "${core}" -d "${CORES_ROOT}/${core}"
}

while IFS= read -r code; do
  [[ -z "${code}" ]] && continue
  upper="$(echo "${code}" | tr '[:lower:]' '[:upper:]')"
  var="SOLR_CORE_${upper}"
  create_core "${!var:-}"
done < <(docker exec "${PHP_CONTAINER}" sh -c "cd /var/www/html && php scripts/_core/countries-cli.php codes")
