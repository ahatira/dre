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

NPM runtime:
  Host npm when available (Linux). Otherwise Node via Docker (node:20-alpine).
  CI/Jenkins (CI, JENKINS_URL): Docker npm by default — set PS_NPM_DOCKER=0 to use agent npm.
  Override image: PS_NODE_IMAGE=node:20-alpine

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
  ps_npm_fix_ownership_if_needed "${theme_dir}"
  ps_npm_exec "${theme_dir}" sh -lc "$(ps_npm_install_cmd "${theme_dir}")"
  if ! ps_npm_usable_on_host; then
    ps_npm_prepare "${theme_dir}"
  fi
  ps_npm_exec "${theme_dir}" sh -lc "npm run ${npm_script}"
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
if ! ps_npm_usable_on_host; then
  ps_require_cmd docker
fi

COMPOSER_OPTS=(install --no-interaction --optimize-autoloader)
[[ ${NO_CACHE} -eq 1 ]] && COMPOSER_OPTS+=(--no-cache)
[[ ${PRODUCTION} -eq 1 ]] && COMPOSER_OPTS+=(--no-dev)

ps_info "Composer install..."
( cd "${PS_SRC_DIR}" && composer "${COMPOSER_OPTS[@]}" )
ps_success "Composer OK"

ps_info "NPM install + libs..."
ps_npm_fix_ownership_if_needed "${PS_SRC_DIR}/node_modules"
ps_npm_fix_libraries_permissions
ps_npm_exec "${PS_SRC_DIR}" sh -lc "$(ps_npm_install_cmd "${PS_SRC_DIR}")"
if ! ps_npm_usable_on_host; then
  ps_npm_prepare "${PS_SRC_DIR}"
fi
ps_npm_exec "${PS_SRC_DIR}" sh -lc 'npm run libs'
touch "${PS_WEB_DIR}/libraries/.gitkeep" 2>/dev/null || true
ps_success "NPM libraries OK"

ps_build_theme "${PS_UI_SUITE_THEME}" "build" "ui_suite_bnp theme"
ps_link_theme_bootstrap_library
ps_build_theme "${PS_PS_THEME}" "gulp-prod" "ps_theme"

if [[ ${PRODUCTION} -eq 1 && ${KEEP_NPM} -eq 0 ]]; then
  rm -rf \
    "${PS_SRC_DIR}/node_modules" \
    "${PS_UI_SUITE_THEME}/node_modules" \
    "${PS_PS_THEME}/node_modules"
  ps_success "node_modules cleaned"
fi

ps_success "Build complete"
