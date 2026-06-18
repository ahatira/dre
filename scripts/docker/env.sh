#!/usr/bin/env bash
# Generate src/.env from .env.dist (local dev only).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DIST="${ROOT}/src/.env.dist"
OUT="${ROOT}/src/.env"

[[ -f "${DIST}" ]] || { echo "Missing: ${DIST}" >&2; exit 1; }

USER_UID="${USER_UID:-local}"
sed "s/{USER_UID}/${USER_UID}/g" "${DIST}" > "${OUT}"
echo "Created ${OUT} (USER_UID=${USER_UID})"
