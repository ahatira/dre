#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

ps_header "Drupal Files Cleanup"

cleanup_dir() {
  local pattern="$1"
  local description="$2"
  local count

  count=$(find "${PS_SRC_DIR}" -path "${pattern}" 2>/dev/null | wc -l)
  if [[ "${count}" -gt 0 ]]; then
    ps_info "${description}: ${count} item(s)"
    find "${PS_SRC_DIR}" -path "${pattern}" -delete 2>/dev/null || true
  else
    ps_info "${description}: clean"
  fi
}

cleanup_dir "*/sites/default/files/config_*" "Temporary config exports"
cleanup_dir "*/sites/default/files/offer_sync_check" "Offer sync check artifacts"
cleanup_dir "*/sites/default/files/css" "Generated CSS cache"
cleanup_dir "*/sites/default/files/js" "Generated JS cache"
cleanup_dir "*/sites/default/files/php" "PHP cache"

ps_success "Cleanup completed"
