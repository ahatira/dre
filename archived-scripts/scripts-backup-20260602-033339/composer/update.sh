#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Composer Update"
cd "${PS_SRC_DIR}"
COMPOSER_PROCESS_TIMEOUT=2000 composer update --no-interaction --prefer-dist
ps_success "Composer update completed"
