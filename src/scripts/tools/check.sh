#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Check — verify build artefacts (CI gate, no Drupal bootstrap).

Usage: scripts/main.sh tools check

Checks:
  - src/vendor/autoload.php
  - Required web/libraries/* paths

Does not install anything. Run: scripts/main.sh tools build
EOF
}

[[ "${1:-}" == "--help" || "${1:-}" == "-h" ]] && { show_help; exit 0; }

ps_header "Verify: build artefacts"
ERRORS=0

if [[ ! -f "${PS_SRC_DIR}/vendor/autoload.php" ]]; then
  ps_error "Missing vendor/autoload.php"
  ERRORS=$((ERRORS + 1))
else
  ps_success "Composer vendor OK"
fi

REQUIRED_LIBS=(
  ace
  clipboard
  dropzone
  nouislider
  slick-carousel/slick
  ckeditor5/plugins/media-embed
)

MISSING=()
for lib in "${REQUIRED_LIBS[@]}"; do
  [[ -d "${PS_WEB_DIR}/libraries/${lib}" ]] || MISSING+=("${lib}")
done

if [[ ${#MISSING[@]} -gt 0 ]]; then
  ps_error "Missing web/libraries:"
  printf '  - %s\n' "${MISSING[@]}" >&2
  ERRORS=$((ERRORS + 1))
else
  ps_success "Front libraries OK"
fi

[[ ${ERRORS} -eq 0 ]] || ps_die "Verify failed (${ERRORS} error(s)). Run: make build"
ps_success "Verify passed"
