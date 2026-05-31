#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Composer Audit"
cd "${PS_SRC_DIR}"
composer audit || true
ps_success "Composer audit finished"
