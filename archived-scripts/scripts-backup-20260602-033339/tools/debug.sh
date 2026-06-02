#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Debug"
ps_drush watchdog:show --count=50 || true
ps_success "Debug dump completed"
