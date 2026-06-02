#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

GROUP="${1:-ps_project}"

ps_header "Drupal Migrate"
ps_drush migrate:status --group="${GROUP}"
ps_drush migrate:import --group="${GROUP}" -y
ps_success "Migration group completed: ${GROUP}"
