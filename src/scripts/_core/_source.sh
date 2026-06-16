#!/usr/bin/env bash
# Bootstrap - Load all core modules
set -Eeuo pipefail

# Prevent double-load when install.sh sources install-country.sh.
if [[ -n "${PS_SOURCE_LOADED:-}" ]]; then
  return 0
fi
PS_SOURCE_LOADED=1

CORE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# shellcheck source=/dev/null
source "${CORE_DIR}/constants.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/colors.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/logger.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/errors.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/docker.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/drush.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/multisite.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/site-languages.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/helpers.sh"

# Enable error trap by default
ps_enable_error_trap
