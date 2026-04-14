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
  social_media_links
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

DEFAULT_CONTENT_MODULES=(
  default_content
  ps_default_content
)

FAVORITES_MODULES=(
  flag
  ps_favorites
)

PS_CUSTOM_MODULES=(
  ps
  ps_block
  ps_dictionary
  ps_agent
  ps_price
  ps_surface
  ps_features
  ps_diagnostic
  ps_division
  ps_media
  ps_offer
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

enable_modules_non_blocking() {
  local group="$1"
  shift

  local selected=()
  local failures=()
  local module

  for module in "$@"; do
    if module_exists "$module"; then
      selected+=("$module")
    else
      echo "[install] skip $group: module not found -> $module"
    fi
  done

  if [[ ${#selected[@]} -eq 0 ]]; then
    return
  fi

  log "Enable $group modules (non-blocking): ${selected[*]}"

  for module in "${selected[@]}"; do
    echo "[install] enabling module -> $module"
    if ! "$DRUSH" en -y "$module"; then
      echo "[install] error enabling module -> $module"
      failures+=("$module")
    fi
  done

  if [[ ${#failures[@]} -gt 0 ]]; then
    echo "[install] modules with errors in group '$group': ${failures[*]}"
    echo "[install] continue install. Fix these modules in a dedicated pass."
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

configure_phone_international() {
  if ! module_exists "phone_international"; then
    echo "[install] skip phone configuration: module not found -> phone_international"
    return
  fi

  local library_path="$PROJECT_ROOT/web/libraries/intl-tel-input"

  if [[ -f "$library_path/build/js/intlTelInput.min.js" || -f "$library_path/build/js/intlTelInput.js" ]]; then
    echo "[install] phone library already available -> $library_path"
  else
    log "Download local Phone International library"
    "$DRUSH" phone_international:plugin "$library_path"
  fi

  log "Force Phone International local assets"
  "$DRUSH" config:set phone_international.settings cdn 0 -y
}

ensure_dropzone_library() {
  local library_dir="$PROJECT_ROOT/web/libraries/dropzone"
  local js_url="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"
  local css_url="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css"

  if [[ -f "$library_dir/dropzone.css" && ( -f "$library_dir/dropzone-min.js" || -f "$library_dir/dropzone.min.js" ) ]]; then
    echo "[install] dropzone library already available -> $library_dir"
    return
  fi

  if ! command -v curl >/dev/null 2>&1; then
    echo "[install] curl is required to download the Dropzone library" >&2
    exit 1
  fi

  log "Download local Dropzone library"
  mkdir -p "$library_dir/min"
  curl -fsSL "$js_url" -o "$library_dir/dropzone-min.js"
  cp "$library_dir/dropzone-min.js" "$library_dir/dropzone.min.js"
  cp "$library_dir/dropzone-min.js" "$library_dir/min/dropzone.min.js"
  curl -fsSL "$css_url" -o "$library_dir/dropzone.css"
}

ensure_favorites_header_block() {
  if ! module_exists "ps_favorites"; then
    echo "[install] skip favorites block placement: module not found -> ps_favorites"
    return
  fi

  if ! theme_exists "$DEFAULT_THEME"; then
    echo "[install] skip favorites block placement: theme not found -> $DEFAULT_THEME"
    return
  fi

  local theme="$DEFAULT_THEME"
  log "Ensure favorites header block placement"
  "$DRUSH" php:eval '$storage = \Drupal::entityTypeManager()->getStorage("block");
  $id = "ui_suite_bnppre_ps_favorites_header";
  if (!$storage->load($id)) {
    \Drupal\block\Entity\Block::create([
      "id" => $id,
      "theme" => "'"$theme"'",
      "region" => "actions",
      "plugin" => "ps_favorites_header_block",
      "weight" => 19,
      "visibility" => [],
      "settings" => [
        "id" => "ps_favorites_header_block",
        "label" => "Favorites Header",
        "label_display" => "0",
        "provider" => "ps_favorites",
      ],
      "status" => TRUE,
    ])->save();
    echo "Block created.";
  } else {
    echo "Block already exists.";
  }'
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
enable_modules "Favorites" "${FAVORITES_MODULES[@]}"
ensure_dropzone_library
enable_modules_non_blocking "Property Search custom" "${PS_CUSTOM_MODULES[@]}"
enable_modules "Default Content" "${DEFAULT_CONTENT_MODULES[@]}"
configure_phone_international
enable_modules "UI Suite BNPPRE" "${UI_SUITE_BNPPRE_MODULES[@]}"

# Gin-related modules require Gin to be enabled/configured first.
enable_themes "$ADMIN_THEME"
set_theme_config admin "$ADMIN_THEME"

enable_modules "Gin admin" "${GIN_ADMIN_MODULES[@]}"
# add_languages "${SITE_LANGUAGES[@]}"

enable_themes "$DEFAULT_THEME"
set_theme_config default "$DEFAULT_THEME"
ensure_favorites_header_block

log "Run database updates"
"$DRUSH" updb -y || true

log "Rebuild cache"
"$DRUSH" cr -y

log "Done"
"$DRUSH" status
