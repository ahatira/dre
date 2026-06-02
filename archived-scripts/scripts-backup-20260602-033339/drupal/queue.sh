#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

QUEUE_NAME="${1:-}"

ps_header "Drupal Queue"
if [[ -n "${QUEUE_NAME}" ]]; then
  ps_drush queue:run "${QUEUE_NAME}"
else
  ps_drush queue:run
fi
ps_success "Queue processing completed"
