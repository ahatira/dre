#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Drupal Deploy"
ps_timed_run "Run database updates" ps_retry 2 2 ps_drush updatedb -y
ps_timed_run "Import configuration" ps_retry 2 2 ps_drush config:import -y
ps_timed_run "Rebuild caches" ps_retry 2 2 ps_drush cr
ps_success "Deploy workflow completed"
