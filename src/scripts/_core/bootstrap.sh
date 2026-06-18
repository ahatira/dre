#!/usr/bin/env bash
# Bootstrap — load shared modules (source once).
set -Eeuo pipefail

if [[ -n "${PS_BOOTSTRAP_LOADED:-}" ]]; then
  return 0
fi
PS_BOOTSTRAP_LOADED=1

PS_CORE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PS_SCRIPTS_DIR="$(dirname "${PS_CORE_DIR}")"
PS_SRC_DIR="$(dirname "${PS_SCRIPTS_DIR}")"
PS_WEB_DIR="${PS_SRC_DIR}/web"

# Monorepo root when src/ is deployed inside the full dev repository.
if [[ -d "${PS_SRC_DIR}/../docker" ]]; then
  PS_REPO_ROOT="$(dirname "${PS_SRC_DIR}")"
else
  PS_REPO_ROOT="${PS_SRC_DIR}"
fi

# shellcheck source=/dev/null
source "${PS_CORE_DIR}/config.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/log.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/runtime.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/drush.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/countries.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/multisite.sh"
# shellcheck source=/dev/null
source "${PS_CORE_DIR}/helpers.sh"

ps_enable_error_trap
