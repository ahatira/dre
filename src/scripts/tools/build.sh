#!/usr/bin/env bash
# shellcheck disable=SC1091
if [ -z "${BASH_VERSION:-}" ]; then
  exec /usr/bin/env bash "$0" "$@"
fi
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Build — install Composer + NPM dependencies, copy front libraries, compile themes.

Usage: make build [OPTIONS]
       scripts/main.sh tools build [OPTIONS]

Options:
  --production   composer install --no-dev; cleanup node_modules after build
  --no-cache     composer install --no-cache
  --keep-npm     Keep node_modules after build
  -h, --help     Show this help

Requires: Docker (container ps_php), node, npm on the host (WSL).
EOF
}

PS_UI_SUITE_THEME="${PS_WEB_DIR}/themes/custom/ui_suite_bnp"
PS_PS_THEME="${PS_WEB_DIR}/themes/custom/ps_theme"

ps_build_theme() {
  local theme_dir="$1"
  local npm_script="$2"
  local label="$3"
  ps_info "${label}..."
  if [[ "${PS_RUNTIME}" == "container" ]]; then
    cd "${theme_dir}" && npm ci --no-audit --no-fund 2>/dev/null || npm install --no-save --no-audit --no-fund
    npm run "${npm_script}"
  else
    ps_npm_exec "${theme_dir}" sh -lc "$(ps_npm_install_cmd "${theme_dir}")"
    ps_npm_exec "${theme_dir}" sh -lc "npm run ${npm_script}"
  fi
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

# Detect if we're in container or host
if [[ -f /.dockerenv ]]; then
  PS_RUNTIME="container"
else
  PS_RUNTIME="host"
fi

COMPOSER_OPTS=(install --no-interaction --optimize-autoloader)
[[ ${NO_CACHE} -eq 1 ]] && COMPOSER_OPTS+=(--no-cache)
[[ ${PRODUCTION} -eq 1 ]] && COMPOSER_OPTS+=(--no-dev)

ps_info "Composer install..."
if [[ "${PS_RUNTIME}" == "container" ]]; then
  composer "${COMPOSER_OPTS[@]}"
else
  ps_require_cmd docker
  docker compose -f "${PS_REPO_ROOT}/docker/docker-compose.yml" exec -T php sh -c "cd ${PS_SRC_DIR} && COMPOSER_PROCESS_TIMEOUT=2000 composer ${COMPOSER_OPTS[@]}"
fi
ps_success "Composer OK"

ps_info "NPM install + libs..."
if [[ "${PS_RUNTIME}" == "container" ]]; then
  npm ci --no-audit --no-fund 2>/dev/null || npm install --no-save --no-audit --no-fund
  npm run libs
else
  ps_npm_usable_on_host || ps_die "Host npm/node required (WSL Linux, not /mnt/c)"
  ps_npm_exec "${PS_SRC_DIR}" sh -lc "$(ps_npm_install_cmd "${PS_SRC_DIR}")"
  ps_npm_exec "${PS_SRC_DIR}" sh -lc 'npm run libs'
fi
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
