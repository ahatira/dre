#!/usr/bin/env bash
# Sync root multisite manifest into src/ (deployable artefact).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
SRC="${ROOT}/src"
MANIFEST="${ROOT}/scripts/multisite/countries.yml"
CLI="${SRC}/scripts/_core/countries-cli.php"
DEST_MANIFEST="${SRC}/web/sites/countries.yml"
DEST_DRUSH="${SRC}/drush/sites/ps.site.yml"

[[ -f "${MANIFEST}" ]] || { echo "Missing: ${MANIFEST}" >&2; exit 1; }
[[ -f "${CLI}" ]] || { echo "Missing: ${CLI}" >&2; exit 1; }

{
  cat <<'EOF'
# GENERATED — do not edit directly.
# Source: scripts/multisite/countries.yml (repo root monorepo).
# Regenerate: make generate-multisite
#
EOF
  sed -n '/^countries:/,$p' "${MANIFEST}"
} > "${DEST_MANIFEST}"

php "${CLI}" drush-site-yml > "${DEST_DRUSH}"

echo "Synced countries.yml → src/web/sites/countries.yml"
echo "Generated src/drush/sites/ps.site.yml"
