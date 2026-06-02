#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Solr Maintenance"
ps_drush search-api:clear || true
ps_drush search-api:index || true
ps_success "Solr workflow completed"
