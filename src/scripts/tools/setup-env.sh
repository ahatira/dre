#!/usr/bin/env bash
# Creates src/.env from .env.dist with USER_UID substitution.
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC_DIR="$(dirname "$(dirname "${SCRIPT_DIR}")")"
DIST_FILE="${SRC_DIR}/.env.dist"
OUT_FILE="${SRC_DIR}/.env"

if [[ ! -f "${DIST_FILE}" ]]; then
  echo "Missing template: ${DIST_FILE}" >&2
  exit 1
fi

USER_UID="${USER_UID:-local}"
sed "s/{USER_UID}/${USER_UID}/g" "${DIST_FILE}" > "${OUT_FILE}"
echo "Created ${OUT_FILE} (USER_UID=${USER_UID})"
