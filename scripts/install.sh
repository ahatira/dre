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
SITE_LANGUAGES=(
  fr
)

# This script intentionally performs a fresh Drupal install only.
# It enables the project's modules and themes, adds configured site languages,
# but does not seed demo content or menus.

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

MENU_MODULES=(
  menu_ui
  menu_link_content
  link_attributes
  link_attributes_menu_link_content
)

UI_SUITE_BNPPRE_MODULES=(
  languageicons
)

GIN_ADMIN_MODULES=(
  gin_toolbar
  gin_login
)

log() {
  printf '\n[install] %s\n' "$1"
}

module_exists() {
  local module="$1"

  [[ -n "$module" ]] || return 1

  [[ -f "web/core/modules/$module/$module.info.yml" ]] && return 0
  [[ -f "web/modules/contrib/$module/$module.info.yml" ]] && return 0
  [[ -f "web/modules/custom/$module/$module.info.yml" ]] && return 0

  find web/modules/contrib web/modules/custom -maxdepth 5 -type f -name "$module.info.yml" 2>/dev/null | grep -q .
}

theme_exists() {
  local theme="$1"

  [[ -n "$theme" ]] || return 1

  [[ -f "web/core/themes/$theme/$theme.info.yml" ]] && return 0
  [[ -f "web/themes/contrib/$theme/$theme.info.yml" ]] && return 0
  [[ -f "web/themes/custom/$theme/$theme.info.yml" ]] && return 0

  find web/themes/contrib web/themes/custom -maxdepth 5 -type f -name "$theme.info.yml" 2>/dev/null | grep -q .
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

language_exists() {
  local langcode="$1"

  [[ -n "$langcode" ]] || return 1

  "$DRUSH" php:eval "exit(\Drupal::languageManager()->getLanguage('$langcode') ? 0 : 1);" >/dev/null 2>&1
}

add_languages() {
  local selected=()
  local langcode

  for langcode in "$@"; do
    if [[ -z "$langcode" ]]; then
      continue
    fi

    if language_exists "$langcode"; then
      echo "[install] skip language: already installed -> $langcode"
    else
      selected+=("$langcode")
    fi
  done

  if [[ ${#selected[@]} -gt 0 ]]; then
    log "Add site languages: ${selected[*]}"
    local language
    for language in "${selected[@]}"; do
      "$DRUSH" language:add "$language" -y
    done
  fi
}

enable_themes() {
  local selected=()
  local theme
  for theme in "$@"; do
    if theme_exists "$theme"; then
      selected+=("$theme")
    else
      echo "[install] skip theme: theme not found -> $theme"
    fi
  done

  if [[ ${#selected[@]} -gt 0 ]]; then
    log "Enable themes: ${selected[*]}"
    "$DRUSH" theme:enable -y "${selected[@]}"
  fi
}

set_theme_config() {
  local config_key="$1"
  local theme="$2"

  if theme_exists "$theme"; then
    log "Set theme config: $config_key -> $theme"
    "$DRUSH" config:set system.theme "$config_key" "$theme" -y
  else
    echo "[install] skip theme config: theme not found -> $theme"
  fi
}

if [[ ! -x "$DRUSH" ]]; then
  echo "[install] drush not found or not executable -> $DRUSH" >&2
  exit 1
fi

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
enable_modules "Menu" "${MENU_MODULES[@]}"
enable_modules "UI Suite BNPPRE" "${UI_SUITE_BNPPRE_MODULES[@]}"

# Gin-related modules require Gin to be enabled/configured first.
enable_themes "$ADMIN_THEME"
set_theme_config admin "$ADMIN_THEME"

enable_modules "Gin admin" "${GIN_ADMIN_MODULES[@]}"
add_languages "${SITE_LANGUAGES[@]}"

enable_themes "$DEFAULT_THEME"
set_theme_config default "$DEFAULT_THEME"

log "Rebuild cache"
"$DRUSH" cr -y

log "Done"
"$DRUSH" status
