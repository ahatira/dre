#!/usr/bin/env bash

set -euo pipefail

# Usage example:
# DB_URL='mysql://user:pass@127.0.0.1/dbname' SITE_URI='http://example.local' ./scripts/install_ui_suite_bnppre_site.sh

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

DRUSH="$PROJECT_ROOT/vendor/bin/drush"
THEME="${THEME:-ui_suite_bnppre}"
INSTALL_PROFILE="${INSTALL_PROFILE:-standard}"
SITE_URI="${SITE_URI:-http://realestate.local}"
SITE_NAME="${SITE_NAME:-BNP PRE Local}"
ADMIN_USER="${ADMIN_USER:-admin}"
ADMIN_PASS="${ADMIN_PASS:-admin}"
ADMIN_MAIL="${ADMIN_MAIL:-admin@example.com}"
DB_URL="${DB_URL:-}"

REQUIRED_MODULES=(
  languageicons
  ui_patterns
  ui_styles
)

log() {
  printf '\n[install] %s\n' "$1"
}

require_cmd() {
  command -v "$1" >/dev/null 2>&1 || {
    echo "Missing required command: $1" >&2
    exit 1
  }
}

is_installed() {
  "$DRUSH" status --uri="$SITE_URI" --fields=bootstrap --format=string 2>/dev/null | grep -q "Successful"
}

import_footer_optional_config() {
  local optional_dir="$PROJECT_ROOT/web/themes/custom/$THEME/config/optional"
  local tmp_dir
  tmp_dir="$(mktemp -d)"

  cp "$optional_dir/block.block.${THEME}_footer.yml" "$tmp_dir/"
  cp "$optional_dir/block.block.${THEME}_footer_branding.yml" "$tmp_dir/"
  cp "$optional_dir/block.block.${THEME}_footer_top_business.yml" "$tmp_dir/"
  cp "$optional_dir/block.block.${THEME}_footer_top_about.yml" "$tmp_dir/"

  "$DRUSH" cim --uri="$SITE_URI" --partial --source="$tmp_dir" -y
  rm -rf "$tmp_dir"
}

configure_theme_settings() {
  "$DRUSH" config:set --uri="$SITE_URI" "$THEME.settings" container container -y
  "$DRUSH" config:set --uri="$SITE_URI" "$THEME.settings" footer_container container-fluid -y
}

disable_legacy_powered_block() {
  "$DRUSH" php:eval --uri="$SITE_URI" '
    $theme = \Drupal::config("system.theme")->get("default");
    $id = $theme . "_powered";
    $block = \Drupal::entityTypeManager()->getStorage("block")->load($id);
    if ($block) {
      $block->setStatus(FALSE);
      $block->save();
      echo "Disabled legacy block: " . $id . PHP_EOL;
    }
    else {
      echo "Legacy block not found: " . $id . PHP_EOL;
    }
  '
}

print_footer_state() {
  "$DRUSH" php:eval --uri="$SITE_URI" '
    $theme = \Drupal::config("system.theme")->get("default");
    $storage = \Drupal::entityTypeManager()->getStorage("block");
    $blocks = $storage->loadByProperties(["theme" => $theme]);
    $rows = [];
    foreach ($blocks as $block) {
      $region = $block->getRegion();
      if (in_array($region, ["footer_top", "footer_bottom", "footer"], TRUE)) {
        $rows[] = [
          $block->id(),
          $region,
          $block->getWeight(),
          $block->getPluginId(),
          $block->label(),
          $block->status() ? "enabled" : "disabled",
        ];
      }
    }
    usort($rows, static fn($a, $b) => [$a[1], $a[2]] <=> [$b[1], $b[2]]);
    foreach ($rows as $row) {
      echo implode(" | ", $row) . PHP_EOL;
    }
  '
}

assert_footer_config() {
  local required=(
    "block.block.${THEME}_footer_top_business"
    "block.block.${THEME}_footer_top_about"
    "block.block.${THEME}_footer_branding"
    "block.block.${THEME}_footer"
  )

  local missing=0
  for cfg in "${required[@]}"; do
    if ! "$DRUSH" config:get --uri="$SITE_URI" "$cfg" id --format=string >/dev/null 2>&1; then
      echo "Missing active config: $cfg" >&2
      missing=1
    fi
  done

  if [[ "$missing" -eq 1 ]]; then
    return 1
  fi

  echo "Footer config verification OK"
}

main() {
  require_cmd "$DRUSH"

  log "Checking Drupal installation status"
  if ! is_installed; then
    if [[ -z "$DB_URL" ]]; then
      echo "DB_URL is required when no Drupal site is installed." >&2
      echo "Example: DB_URL='mysql://user:pass@127.0.0.1/dbname' $0" >&2
      exit 1
    fi

    log "Installing fresh Drupal site"
    "$DRUSH" site:install "$INSTALL_PROFILE" \
      --uri="$SITE_URI" \
      --db-url="$DB_URL" \
      --site-name="$SITE_NAME" \
      --account-name="$ADMIN_USER" \
      --account-pass="$ADMIN_PASS" \
      --account-mail="$ADMIN_MAIL" \
      -y
  else
    log "Drupal is already installed, skipping site:install"
  fi

  log "Enabling required modules"
  "$DRUSH" pm:enable --uri="$SITE_URI" -y "${REQUIRED_MODULES[@]}"

  log "Enabling and setting default theme"
  "$DRUSH" theme:enable --uri="$SITE_URI" "$THEME" -y
  "$DRUSH" config:set --uri="$SITE_URI" system.theme default "$THEME" -y

  log "Applying theme settings"
  configure_theme_settings

  log "Importing footer blocks optional config"
  import_footer_optional_config

  log "Disabling legacy powered-by block in footer legacy region (if present)"
  disable_legacy_powered_block

  log "Rebuilding cache"
  "$DRUSH" cr --uri="$SITE_URI" -y

  log "Verifying footer config and active block placement"
  assert_footer_config
  print_footer_state

  log "Done"
  "$DRUSH" uli --uri="$SITE_URI"
}

main "$@"
