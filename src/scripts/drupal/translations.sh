#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

LANGCODE="${1:-fr}"
PO_PATH="${2:-translations/${LANGCODE}.po}"

ps_header "Drupal Translations"
ps_drush locale:import "${LANGCODE}" "${PO_PATH}" --type=customized --override=all -y
ps_drush cr
ps_success "Translation import completed: ${LANGCODE}"
