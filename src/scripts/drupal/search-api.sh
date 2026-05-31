#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Search API"
ps_drush search-api:status || true
ps_drush search-api:index || true
ps_success "Search API workflow completed"
