#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Build automation - Install dependencies and libraries

show_help() {
  cat <<'EOF'
Build Script - Install and prepare project dependencies

Usage: scripts/main.sh tools build [OPTIONS]

Options:
  --production     Skip dev dependencies and npm cleanup
  --no-cache       Run composer install without cache
  --keep-npm       Keep node_modules after library copy
  -h, --help       Show this help

Examples:
  scripts/main.sh tools build
  scripts/main.sh tools build --production
  scripts/main.sh tools build --no-cache --keep-npm
EOF
}

# Parse arguments
PRODUCTION_MODE=0
NO_CACHE=0
KEEP_NPM=0

while [[ $# -gt 0 ]]; do
  case "$1" in
    --production)
      PRODUCTION_MODE=1
      shift
      ;;
    --no-cache)
      NO_CACHE=1
      shift
      ;;
    --keep-npm)
      KEEP_NPM=1
      shift
      ;;
    -h|--help)
      show_help
      exit 0
      ;;
    *)
      ps_die "Unknown option: $1"
      ;;
  esac
done

ps_header "Build: Installing project dependencies"

# Composer install
ps_info "Installing Composer dependencies..."
COMPOSER_OPTS="--no-interaction --optimize-autoloader"
[[ ${NO_CACHE} -eq 1 ]] && COMPOSER_OPTS="${COMPOSER_OPTS} --no-cache"
[[ ${PRODUCTION_MODE} -eq 1 ]] && COMPOSER_OPTS="${COMPOSER_OPTS} --no-dev"

if ps_in_docker; then
  ps_docker_exec_php "composer install ${COMPOSER_OPTS}"
else
  (cd "${PS_SRC_DIR}" && composer install ${COMPOSER_OPTS})
fi
ps_success "Composer dependencies installed"

# NPM install
ps_info "Installing NPM dependencies..."
if ps_in_docker; then
  ps_docker_exec_php "npm install --no-save"
else
  (cd "${PS_SRC_DIR}" && npm install --no-save)
fi
ps_success "NPM dependencies installed"

# Copy libraries
ps_info "Copying libraries to web/libraries/..."
if ps_in_docker; then
  ps_docker_exec_php "npm run libs"
else
  (cd "${PS_SRC_DIR}" && npm run libs)
fi
ps_success "Libraries copied successfully"

# Cleanup
if [[ ${KEEP_NPM} -eq 0 ]] && [[ ${PRODUCTION_MODE} -eq 1 ]]; then
  ps_info "Cleaning up node_modules..."
  if ps_in_docker; then
    ps_docker_exec_php "rm -rf node_modules package-lock.json"
  else
    (cd "${PS_SRC_DIR}" && rm -rf node_modules package-lock.json)
  fi
  ps_success "Cleanup complete"
fi

ps_success "Build complete!"
