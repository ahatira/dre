#!/usr/bin/env bash
set -Eeuo pipefail

CORE_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# shellcheck source=/dev/null
source "${CORE_DIR}/constants.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/colors.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/time.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/logger.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/errors.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/env.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/validate.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/filesystem.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/process.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/network.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/docker.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/drush.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/database.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/git.sh"
# shellcheck source=/dev/null
source "${CORE_DIR}/ui.sh"

ps_enable_error_trap

if ps_env_bool "${PS_TRACE:-0}"; then
	PS_TRACE_FILE="${PS_TRACE_FILE:-/tmp/ps-scripts-trace-$(date +%Y%m%d-%H%M%S).log}"
	exec 19>>"${PS_TRACE_FILE}"
	export BASH_XTRACEFD=19
	set -x
	ps_warn "Trace mode enabled: ${PS_TRACE_FILE}"
fi

ps_diag_summary
