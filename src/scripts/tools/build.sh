#!/usr/bin/env bash
# shellcheck disable=SC1091
if [ -z "${BASH_VERSION:-}" ]; then
  exec /usr/bin/env bash "$0" "$@"
fi
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Build — install Composer + NPM dependencies, copy front libraries, compile themes.

Usage: make build-npm [OPTIONS]
       make build -- --npm-only [OPTIONS]   (GNU make: -- required before script options)
       scripts/main.sh tools build [OPTIONS]

Options:
  --npm-only     Skip Composer install (NPM/themes/libs only)
  --production   composer install --no-dev; cleanup node_modules after build
  --no-cache     composer install --no-cache
  --keep-npm     Keep node_modules after build
  -h, --help     Show this help

Environment:
  NPM_ONLY=0|1   Same as omitting or passing --npm-only (default: 0)

Examples:
  make build-npm
  make build -- --npm-only --production
  NPM_ONLY=1 scripts/main.sh tools build

Requires: node, npm on the host (WSL Linux native, not /mnt/c). Composer required unless --npm-only / NPM_ONLY=1.
EOF
}

PS_UI_SUITE_THEME="${PS_WEB_DIR}/themes/custom/ui_suite_bnp"
PS_PS_THEME="${PS_WEB_DIR}/themes/custom/ps_theme"

ps_build_theme() {
  local theme_dir="$1"
  local npm_script="$2"
  local label="$3"
  ps_info "${label}..."
  ps_npm_exec "${theme_dir}" sh -lc "$(ps_npm_install_cmd "${theme_dir}")"
  ps_npm_exec "${theme_dir}" sh -lc "npm run ${npm_script}"
  ps_success "${label} OK"
}

ps_build_npm_assets() {
  ps_info "ui_suite_bnp npm install (Bootstrap source for libs)..."
  ps_npm_exec "${PS_UI_SUITE_THEME}" sh -lc "$(ps_npm_install_cmd "${PS_UI_SUITE_THEME}")"
  ps_success "ui_suite_bnp npm install OK"

  ps_info "NPM install + libs..."
  ps_npm_exec "${PS_SRC_DIR}" sh -lc "$(ps_npm_install_cmd "${PS_SRC_DIR}")"
  ps_npm_exec "${PS_SRC_DIR}" sh -lc 'npm run libs'
  touch "${PS_WEB_DIR}/libraries/.gitkeep" 2>/dev/null || true
  ps_success "NPM libraries OK"

  ps_build_theme "${PS_UI_SUITE_THEME}" "build" "ui_suite_bnp theme"
  ps_build_theme "${PS_PS_THEME}" "gulp-prod" "ps_theme"
}

PRODUCTION=0
NO_CACHE=0
KEEP_NPM=0
NPM_ONLY="${NPM_ONLY:-0}"

while [[ $# -gt 0 ]]; do
  case "$1" in
    --production) PRODUCTION=1; shift ;;
    --no-cache) NO_CACHE=1; shift ;;
    --keep-npm) KEEP_NPM=1; shift ;;
    --npm-only) NPM_ONLY=1; shift ;;
    -h|--help) show_help; exit 0 ;;
    *) ps_die "Unknown option: $1" ;;
  esac
done

case "${NPM_ONLY}" in
  0|1) ;;
  *) ps_die "Invalid NPM_ONLY=${NPM_ONLY} (use 0 or 1, or pass --npm-only)" ;;
esac

if [[ ${NPM_ONLY} -eq 1 ]]; then
  ps_header "Build: NPM assets only (--npm-only)"
else
  ps_header "Build: project dependencies"
fi

ps_require_cmd npm
ps_require_cmd node
ps_npm_usable_on_host || ps_die "Host npm/node required (WSL Linux, not /mnt/c)"

if [[ ${NPM_ONLY} -eq 0 ]]; then
  ps_require_cmd composer

  COMPOSER_OPTS=(install --no-interaction --optimize-autoloader)
  [[ ${NO_CACHE} -eq 1 ]] && COMPOSER_OPTS+=(--no-cache)
  [[ ${PRODUCTION} -eq 1 ]] && COMPOSER_OPTS+=(--no-dev)

  ps_info "Composer install..."
  ( cd "${PS_SRC_DIR}" && composer "${COMPOSER_OPTS[@]}" )
  ps_success "Composer OK"
fi

ps_build_npm_assets

if [[ ${PRODUCTION} -eq 1 && ${KEEP_NPM} -eq 0 ]]; then
  rm -rf \
    "${PS_SRC_DIR}/node_modules" \
    "${PS_UI_SUITE_THEME}/node_modules" \
    "${PS_PS_THEME}/node_modules"
  ps_success "node_modules cleaned"
fi

ps_success "Build complete"
