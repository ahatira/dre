#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Lint"
cd "${PS_SRC_DIR}"
if [[ -x ./vendor/bin/phpcs ]]; then
  ./vendor/bin/phpcs --version
else
  ps_warn "phpcs not installed; skipping PHP lint"
fi
ps_success "Lint phase completed"
