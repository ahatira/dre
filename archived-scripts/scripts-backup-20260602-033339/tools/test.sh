#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Tests"
"$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/test/regression.sh"
