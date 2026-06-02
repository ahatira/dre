#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ROLE="${1:-ps_admin}"
USER_NAME="${2:-admin}"

ps_header "Drupal Permissions"
ps_drush user:role:add "${ROLE}" "${USER_NAME}" -y
ps_success "Role assigned: ${ROLE} -> ${USER_NAME}"
