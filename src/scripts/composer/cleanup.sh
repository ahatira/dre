#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Composer Cleanup"
cd "${PS_SRC_DIR}"
composer clear-cache
ps_success "Composer cache cleared"
