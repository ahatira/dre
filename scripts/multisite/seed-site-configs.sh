#!/usr/bin/env bash
# Seed config/sites/{code}/ from legacy config/sync (one-time migration helper).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SRC="${ROOT}/src"
LEGACY_SYNC="${SRC}/config/sync"
SITES_ROOT="${SRC}/config/sites"
ENV_SITES="${SRC}/config/env/sites"
CLI="${SRC}/scripts/_core/countries-cli.php"
CONTAINER="${PS_PHP_CONTAINER:-ps_php}"

[[ -f "${CLI}" ]] || { echo "Missing: ${CLI}" >&2; exit 1; }

if [[ ! -f "${LEGACY_SYNC}/core.extension.yml" ]]; then
  echo "No legacy config/sync to seed from (missing core.extension.yml)." >&2
  echo "Install a reference site, then: make export-all-configs com" >&2
  exit 1
fi

# Check if container is running
if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${CONTAINER}"; then
  echo "Container ${CONTAINER} not running — run: make up" >&2
  exit 1
fi

mkdir -p "${SITES_ROOT}"

while IFS= read -r code; do
  [[ -z "${code}" ]] && continue
  target="${SITES_ROOT}/${code}"
  mkdir -p "${target}"

  if [[ ! -f "${target}/core.extension.yml" ]]; then
    echo "Seeding ${code} from config/sync..."
    cp -a "${LEGACY_SYNC}/." "${target}/"
  else
    echo "Skipping ${code} (core.extension.yml already present)"
  fi

  legacy_neg="${ENV_SITES}/${code}/language.negotiation.yml"
  if [[ -f "${legacy_neg}" ]]; then
    cp "${legacy_neg}" "${target}/language.negotiation.yml"
    echo "  merged language.negotiation.yml"
  fi

  if [[ ! -f "${target}/.htaccess" && -f "${ENV_SITES}/com/.htaccess" ]]; then
    cp "${ENV_SITES}/com/.htaccess" "${target}/.htaccess"
  fi
done < <(docker exec "${CONTAINER}" sh -c "cd /var/www/html && php scripts/_core/countries-cli.php codes")

echo "Done. Per-country CMI: ${SITES_ROOT}/{code}/"
echo "Next: make install-from-conf com"
