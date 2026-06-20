#!/usr/bin/env bash
# shellcheck disable=SC1091
if [ -z "${BASH_VERSION:-}" ]; then
  exec /usr/bin/env bash "$0" "$@"
fi
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
NPM audit — security check for runtime and theme build packages.

Usage: scripts/main.sh tools npm-audit

Runs npm audit in:
  - src/ (runtime libraries → web/libraries)
  - web/themes/custom/ui_suite_bnp/
  - web/themes/custom/ps_theme/

Fails on high/critical in src/ (browser-facing deps). Theme audits are informational.
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "NPM audit"
ps_require_cmd npm
ps_require_cmd node
ps_npm_usable_on_host || ps_die "Host npm/node required (WSL Linux, not /mnt/c)"

PS_UI_THEME="${PS_WEB_DIR}/themes/custom/ui_suite_bnp"
PS_PS_THEME="${PS_WEB_DIR}/themes/custom/ps_theme"

audit_dir() {
  local dir="$1"
  local label="$2"
  local level="${3:-moderate}"
  ps_info "npm audit (${label})..."
  if ! ( cd "${dir}" && npm audit --audit-level="${level}" ); then
    return 1
  fi
  ps_success "npm audit OK (${label})"
  return 0
}

FAIL=0
audit_dir "${PS_SRC_DIR}" "src runtime" "high" || FAIL=1
audit_dir "${PS_UI_THEME}" "ui_suite_bnp" "high" || true
audit_dir "${PS_PS_THEME}" "ps_theme" "high" || true

[[ ${FAIL} -eq 0 ]] || ps_die "npm audit failed (src runtime — high/critical)"
ps_success "npm audit passed"
