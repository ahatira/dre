#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Profile"
START_TS="$(ps_now_epoch)"
ps_drush status --fields=bootstrap,db-status,drupal-version,drush-version
ps_info "Elapsed seconds: $(ps_elapsed_seconds "${START_TS}")"
