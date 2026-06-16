#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Build — install Composer + NPM dependencies and copy front libraries.

Usage: scripts/main.sh tools build [OPTIONS]

Options:
  --production   composer install --no-dev; cleanup node_modules after libs
  --no-cache     composer install --no-cache
  --keep-npm     Keep node_modules after build
  -h, --help     Show this help
EOF
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

if [[ ${PRODUCTION} -eq 1 && ${KEEP_NPM} -eq 0 ]]; then
  rm -rf "${PS_SRC_DIR}/node_modules" "${PS_SRC_DIR}/package-lock.json"
  ps_success "node_modules cleaned"
fi

ps_success "Build complete"
