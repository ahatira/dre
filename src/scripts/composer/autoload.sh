#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Composer Dump Autoload"
cd "${PS_SRC_DIR}"
composer dump-autoload -o
ps_success "Autoload regenerated"
