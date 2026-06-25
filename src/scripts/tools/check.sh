#!/usr/bin/env bash
# shellcheck disable=SC1091
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)/_core/bootstrap.sh"

show_help() {
  cat <<'EOF'
Check — verify build artefacts (CI gate, no Drupal bootstrap).

Usage: scripts/main.sh tools check

Checks:
  - src/vendor/autoload.php
  - Required web/libraries/* paths (ace, swiper, photoswipe, bootstrap, …)
  - Compiled theme CSS (ui_suite_bnp, ps_theme)
  - ps_theme compiled CSS not tracked in Git (source-only mode)

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
  bootstrap
  clipboard
  dropzone
  nouislider
  photoswipe
  slick-carousel/slick
  swiper
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

REQUIRED_FILES=(
  "${PS_WEB_DIR}/libraries/swiper/swiper-bundle.min.js"
  "${PS_WEB_DIR}/libraries/photoswipe/photoswipe.umd.min.js"
  "${PS_WEB_DIR}/libraries/bootstrap/scss/_functions.scss"
  "${PS_WEB_DIR}/themes/custom/ui_suite_bnp/assets/vendor/bootstrap/bootstrap.min.css"
  "${PS_WEB_DIR}/themes/custom/ps_theme/assets/css/styles.css"
)

MISSING_FILES=()
for file in "${REQUIRED_FILES[@]}"; do
  [[ -f "${file}" ]] || MISSING_FILES+=("${file#${PS_SRC_DIR}/}")
done

if [[ ${#MISSING_FILES[@]} -gt 0 ]]; then
  ps_error "Missing build artefacts:"
  printf '  - %s\n' "${MISSING_FILES[@]}" >&2
  ERRORS=$((ERRORS + 1))
else
  ps_success "Theme build artefacts OK"
fi

if ! ps_check_ps_theme_source_only; then
  ERRORS=$((ERRORS + 1))
fi

[[ ${ERRORS} -eq 0 ]] || ps_die "Verify failed (${ERRORS} error(s)). Run: make build"
ps_success "Verify passed"
