#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Build"
cd "${PS_SRC_DIR}"
COMPOSER_PROCESS_TIMEOUT=2000 composer install --no-interaction --prefer-dist
ps_success "Build dependencies installed"
