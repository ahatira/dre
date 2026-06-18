#!/usr/bin/env bash
# Initialize Solr cores in local Docker (dev only).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
INIT="${ROOT}/docker/solr/init-cores.sh"

[[ -f "${INIT}" ]] || { echo "Missing: ${INIT}" >&2; exit 1; }
chmod +x "${INIT}"
bash "${INIT}"
