#!/usr/bin/env bash
# Generate src/.env from .env.dist (dev only).
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC_DIR="$(dirname "$(dirname "${SCRIPT_DIR}")")"
DIST="${SRC_DIR}/.env.dist"
OUT="${SRC_DIR}/.env"

[[ -f "${DIST}" ]] || { echo "Missing: ${DIST}" >&2; exit 1; }

USER_UID="${USER_UID:-local}"
sed "s/{USER_UID}/${USER_UID}/g" "${DIST}" > "${OUT}"
echo "Created ${OUT} (USER_UID=${USER_UID})"
