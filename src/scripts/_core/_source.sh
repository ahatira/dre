#!/usr/bin/env bash
# Bootstrap - Load all core modules
set -Eeuo pipefail

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
source "${CORE_DIR}/helpers.sh"

# Enable error trap by default
ps_enable_error_trap
