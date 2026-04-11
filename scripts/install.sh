#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

DRUSH="${DRUSH:-$PROJECT_ROOT/vendor/bin/drush}"
SITE_NAME="${SITE_NAME:-Drupal Minimal}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
DEFAULT_THEME="${DEFAULT_THEME:-ui_suite_bnppre}"
ADMIN_THEME="${ADMIN_THEME:-gin}"

DRUPAL_CLASSIC_MODULES=(
  token
  pathauto
  metatag
  redirect
  simple_sitemap
)

TOOLBAR_CLASSIC_MODULES=(
  admin_toolbar
  admin_toolbar_tools
  admin_toolbar_search
)

UI_SUITE_MODULES=(
  ui_patterns
  ui_styles
  ui_icons
  ui_styles_page
  ui_styles_block
)

TRANSLATION_MODULES=(
  language
  locale
  content_translation
  config_translation
)

CONFIGURATION_MODULES=(
  config_split
  config_ignore
)

LAYOUT_BUILDER_MODULES=(
  layout_builder
  layout_discovery
)

GIN_ADMIN_MODULES=(
  gin_toolbar
  gin_login
)

UI_SUITE_BNPPRE_MODULES=(
  languageicons
  default_content
)

log() {
  printf '\n[install] %s\n' "$1"
}

module_exists() {
  local module="$1"
  [[ -f "web/core/modules/$module/$module.info.yml" ]] && return 0
  [[ -f "web/modules/contrib/$module/$module.info.yml" ]] && return 0
  [[ -f "web/modules/custom/$module/$module.info.yml" ]] && return 0

  find web/modules/contrib web/modules/custom -maxdepth 5 -type f -name "$module.info.yml" 2>/dev/null | grep -q .
}

enable_modules() {
  local group="$1"
  shift

  local selected=()
  local module
  for module in "$@"; do
    if module_exists "$module"; then
      selected+=("$module")
    else
      echo "[install] skip $group: module not found -> $module"
    fi
  done

  if [[ ${#selected[@]} -gt 0 ]]; then
    log "Enable $group modules: ${selected[*]}"
    "$DRUSH" en -y "${selected[@]}"
  fi
}

log "Drop database"
"$DRUSH" sql:drop -y || true

log "Install fresh Drupal site (profile: minimal)"
"$DRUSH" site:install minimal --site-name="$SITE_NAME" --account-name="$ADMIN_USER" --account-pass="$ADMIN_PASS" --account-mail="$ADMIN_MAIL" -y

enable_modules "Drupal classic" "${DRUPAL_CLASSIC_MODULES[@]}"
enable_modules "Toolbar classic" "${TOOLBAR_CLASSIC_MODULES[@]}"
enable_modules "UI Suite" "${UI_SUITE_MODULES[@]}"
enable_modules "Translations" "${TRANSLATION_MODULES[@]}"
enable_modules "Configuration" "${CONFIGURATION_MODULES[@]}"
enable_modules "Layout Builder" "${LAYOUT_BUILDER_MODULES[@]}"
enable_modules "UI Suite BNPPRE" "${UI_SUITE_BNPPRE_MODULES[@]}"

log "Enable themes"
"$DRUSH" theme:enable "$ADMIN_THEME" "$DEFAULT_THEME" -y || true
"$DRUSH" config:set system.theme admin "$ADMIN_THEME" -y || true
"$DRUSH" config:set system.theme default "$DEFAULT_THEME" -y || true

enable_modules "Gin admin" "${GIN_ADMIN_MODULES[@]}"

log "Rebuild cache"
"$DRUSH" cr -y

log "Done"
"$DRUSH" status
