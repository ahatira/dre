#!/usr/bin/env bash
# Creates Solr cores for each Property Search country (run after stack is up).
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="${PROJECT_ROOT}/src/.env"
CONTAINER="${SOLR_CONTAINER:-ps_solr}"
CONFIGSET_NAME="ps_project"
CONFIGSET_PATH="/opt/solr/server/solr/configsets/${CONFIGSET_NAME}"

if [[ ! -f "${ENV_FILE}" ]]; then
  echo "Missing ${ENV_FILE}. Run: make env" >&2
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
  if docker exec "${CONTAINER}" test -d "/var/solr/data/${core}"; then
    echo "Solr core exists: ${core}"
    return 0
  fi
  echo "Creating Solr core: ${core}"
  docker exec "${CONTAINER}" solr create -c "${core}" -d "${CONFIGSET_NAME}"
}

for code in com be es fr ie it lu nl pl; do
  upper="$(echo "${code}" | tr '[:lower:]' '[:upper:]')"
  var="SOLR_CORE_${upper}"
  create_core "${!var:-}"
done
