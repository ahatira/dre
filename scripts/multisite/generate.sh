#!/usr/bin/env bash
# Sync root multisite manifest into src/ (deployable artefact).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SRC="${ROOT}/src"
MANIFEST="${ROOT}/scripts/multisite/countries.yml"
CLI="${SRC}/scripts/_core/countries-cli.php"
DEST_MANIFEST="${SRC}/web/sites/countries.yml"
DEST_DRUSH="${SRC}/drush/sites/ps.site.yml"
SITES_ROOT="${SRC}/config/sites"
ENV_SITES="${SRC}/config/env/sites"
CONTAINER="${PS_PHP_CONTAINER:-ps_php}"

[[ -f "${MANIFEST}" ]] || { echo "Missing: ${MANIFEST}" >&2; exit 1; }
[[ -f "${CLI}" ]] || { echo "Missing: ${CLI}" >&2; exit 1; }

# Check if container is running
if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -qx "${CONTAINER}"; then
  echo "Container ${CONTAINER} not running — run: make up" >&2
  exit 1
fi

{
  cat <<'EOF'
# GENERATED — do not edit directly.
# Source: scripts/multisite/countries.yml (repo root monorepo).
# Regenerate: make generate-multisite
#
EOF
  sed -n '/^countries:/,$p' "${MANIFEST}"
} > "${DEST_MANIFEST}"

docker exec "${CONTAINER}" sh -c "cd /var/www/html && php scripts/_core/countries-cli.php drush-site-yml" > "${DEST_DRUSH}"

mkdir -p "${SITES_ROOT}"
while IFS= read -r code; do
  [[ -z "${code}" ]] && continue
  site_dir="${SITES_ROOT}/${code}"
  mkdir -p "${site_dir}"
  if [[ ! -f "${site_dir}/.htaccess" && -f "${ENV_SITES}/com/.htaccess" ]]; then
    cp "${ENV_SITES}/com/.htaccess" "${site_dir}/.htaccess"
  fi
done < <(docker exec "${CONTAINER}" sh -c "cd /var/www/html && php scripts/_core/countries-cli.php codes")

echo "Synced countries.yml → src/web/sites/countries.yml"
echo "Generated src/drush/sites/ps.site.yml"
echo "Scaffolded config/sites/{code}/ directories"
echo "Seed CMI: make seed-site-configs (from legacy config/sync) or make export-all-configs"
