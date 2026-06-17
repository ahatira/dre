#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Build — install Composer + NPM dependencies, copy front libraries, compile themes.

Usage: scripts/main.sh tools build [OPTIONS]

Options:
  --production   composer install --no-dev; cleanup node_modules after build
  --no-cache     composer install --no-cache
  --keep-npm     Keep node_modules after build
  -h, --help     Show this help

Themes (in order):
  1. ui_suite_bnp — npm run build (CSS + icons)
  2. ps_theme     — npm run gulp-prod
EOF
}

PS_UI_SUITE_THEME="${PS_WEB_DIR}/themes/custom/ui_suite_bnp"
PS_PS_THEME="${PS_WEB_DIR}/themes/custom/ps_theme"

ps_build_theme() {
  local theme_dir="$1"
  local npm_script="$2"
  local label="$3"
  ps_info "${label}..."
  ( cd "${theme_dir}" && npm install --no-save && npm run "${npm_script}" )
  ps_success "${label} OK"
}

PRODUCTION=0
NO_CACHE=0
KEEP_NPM=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --production) PRODUCTION=1; shift ;;
    --no-cache) NO_CACHE=1; shift ;;
    --keep-npm) KEEP_NPM=1; shift ;;
    -h|--help) show_help; exit 0 ;;
    *) ps_die "Unknown option: $1" ;;
  esac
done

ps_header "Build: project dependencies"
ps_require_cmd composer
ps_require_cmd npm

COMPOSER_OPTS=(install --no-interaction --optimize-autoloader)
[[ ${NO_CACHE} -eq 1 ]] && COMPOSER_OPTS+=(--no-cache)
[[ ${PRODUCTION} -eq 1 ]] && COMPOSER_OPTS+=(--no-dev)

ps_info "Composer install..."
( cd "${PS_SRC_DIR}" && composer "${COMPOSER_OPTS[@]}" )
ps_success "Composer OK"

ps_info "NPM install + libs..."
( cd "${PS_SRC_DIR}" && npm install --no-save && npm run libs )
ps_success "NPM libraries OK"

ps_build_theme "${PS_UI_SUITE_THEME}" "build" "ui_suite_bnp theme"
ps_build_theme "${PS_PS_THEME}" "gulp-prod" "ps_theme"

if [[ ${PRODUCTION} -eq 1 && ${KEEP_NPM} -eq 0 ]]; then
  rm -rf \
    "${PS_SRC_DIR}/node_modules" \
    "${PS_SRC_DIR}/package-lock.json" \
    "${PS_UI_SUITE_THEME}/node_modules" \
    "${PS_PS_THEME}/node_modules"
  ps_success "node_modules cleaned"
fi

ps_success "Build complete"
