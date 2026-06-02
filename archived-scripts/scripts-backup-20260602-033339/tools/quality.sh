#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Quality"
"$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/tools/lint.sh"
"$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/tools/test.sh"
ps_success "Quality checks completed"
