#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/_source.sh"

# Check if build has been executed

show_help() {
  cat <<'EOF'
Check Script - Verify build dependencies

Usage: scripts/main.sh tools check

Verifies that build has been executed:
  - Composer dependencies installed (vendor/)
  - NPM libraries copied (web/libraries/)

This script does NOT install anything, only checks.
Use 'scripts/main.sh tools build' to install dependencies.

Examples:
  scripts/main.sh tools check
EOF
}

if [[ "${1:-}" == "--help" ]] || [[ "${1:-}" == "-h" ]]; then
  show_help
  exit 0
fi

ps_header "Check: Verifying build dependencies"

ERRORS=0

# Check vendor/
ps_info "Checking Composer dependencies..."
if [[ ! -d "${PS_SRC_DIR}/vendor" ]]; then
  ps_error "vendor/ directory not found"
  ERRORS=$((ERRORS + 1))
elif [[ ! -f "${PS_SRC_DIR}/vendor/autoload.php" ]]; then
  ps_error "vendor/autoload.php not found"
  ERRORS=$((ERRORS + 1))
else
  ps_success "Composer dependencies OK"
fi

# Check web/libraries/
ps_info "Checking NPM libraries..."
REQUIRED_LIBS=(
  "ace"
  "clipboard"
  "dropzone"
  "nouislider"
  "slick-carousel/slick"
  "ckeditor5/plugins/media-embed"
)

MISSING_LIBS=()
for lib in "${REQUIRED_LIBS[@]}"; do
  if [[ ! -d "${PS_WEB_DIR}/libraries/${lib}" ]]; then
    MISSING_LIBS+=("${lib}")
  fi
done

if [[ ${#MISSING_LIBS[@]} -gt 0 ]]; then
  ps_error "Missing libraries in web/libraries/:"
  for lib in "${MISSING_LIBS[@]}"; do
    echo "  - ${lib}" >&2
  done
  ERRORS=$((ERRORS + 1))
else
  ps_success "NPM libraries OK"
fi

# Summary
if [[ ${ERRORS} -gt 0 ]]; then
  echo ""
  ps_error "Build verification failed (${ERRORS} errors)"
  ps_info "Run: scripts/main.sh tools build"
  exit 1
fi

ps_success "Build verification passed!"
