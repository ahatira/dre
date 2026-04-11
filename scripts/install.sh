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

SITE_LANGUAGES=(
  fr
  es
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

MENU_MODULES=(
  menu_ui
  menu_link_content
  link_attributes
  link_attributes_menu_link_content
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

add_languages() {
  local language

  for language in "$@"; do
    if "$DRUSH" language:info "$language" >/dev/null 2>&1; then
      echo "[install] skip language: already exists -> $language"
      continue
    fi

    log "Add language: $language"
    "$DRUSH" language:add "$language"
  done
}

seed_main_menu_items() {
  log "Seed main menu items"
  "$DRUSH" php:eval '
use Drupal\menu_link_content\Entity\MenuLinkContent;

$menu_name = "main";

$find_link_by_title = static function (string $title, string $parent = "") use ($menu_name): ?MenuLinkContent {
  $query = \Drupal::entityTypeManager()->getStorage("menu_link_content")->getQuery();
  $query->accessCheck(FALSE)
    ->condition("menu_name", $menu_name)
    ->condition("title", $title);
  if ($parent !== "") {
    $query->condition("parent", $parent);
  }
  $ids = $query->range(0, 1)->execute();
  if (!$ids) {
    return NULL;
  }
  $id = reset($ids);
  return MenuLinkContent::load($id);
};

$upsert = static function (array $definition) use ($menu_name, $find_link_by_title): MenuLinkContent {
  $title = $definition["title"];
  $parent = $definition["parent"] ?? "";
  $uri = $definition["uri"] ?? "route:<nolink>";
  $weight = $definition["weight"] ?? 0;
  $description = $definition["description"] ?? "";
  $expanded = !empty($definition["expanded"]);
  $classes = $definition["classes"] ?? [];
  $data_attributes = $definition["data_attributes"] ?? [];

  $entity = $find_link_by_title($title, $parent);
  if (!$entity) {
    $entity = MenuLinkContent::create([
      "title" => $title,
      "menu_name" => $menu_name,
      "link" => ["uri" => $uri],
      "parent" => $parent,
      "weight" => $weight,
      "description" => $description,
      "expanded" => $expanded,
      "enabled" => TRUE,
    ]);
  }
  else {
    $entity->set("link", ["uri" => $uri]);
    $entity->set("parent", $parent);
    $entity->set("weight", $weight);
    $entity->set("description", $description);
    $entity->set("expanded", $expanded);
    $entity->set("enabled", TRUE);
  }

  if ($entity->hasField("link_attributes")) {
    $entity->set("link_attributes", [
      "class" => $classes,
      "data" => $data_attributes,
    ]);
  }

  $entity->save();
  return $entity;
};

$root = $upsert([
  "title" => "Nos services",
  "uri" => "route:<nolink>",
  "weight" => 0,
  "description" => "Solutions et expertises",
  "expanded" => TRUE,
]);

$root_plugin_id = "menu_link_content:" . $root->uuid();

$buy = $upsert([
  "title" => "Acheter",
  "uri" => "route:<nolink>",
  "parent" => $root_plugin_id,
  "weight" => 0,
  "description" => "Accompagnement achat",
  "expanded" => TRUE,
]);

$sell = $upsert([
  "title" => "Vendre",
  "uri" => "route:<nolink>",
  "parent" => $root_plugin_id,
  "weight" => 1,
  "description" => "Accompagnement vente",
  "expanded" => TRUE,
  "classes" => ["is-megamenu-aside-source"],
  "data_attributes" => [
    "aside-title" => "Besoin d\x27un accompagnement premium ?",
    "aside-text" => "Nos experts vous accompagnent de l\x27estimation a la signature.",
    "aside-link-label" => "Contacter un conseiller",
    "aside-link-url" => "/contact",
  ],
]);

$rent = $upsert([
  "title" => "Louer",
  "uri" => "route:<nolink>",
  "parent" => $root_plugin_id,
  "weight" => 2,
  "description" => "Location et gestion",
  "expanded" => TRUE,
]);

$buy_plugin_id = "menu_link_content:" . $buy->uuid();
$sell_plugin_id = "menu_link_content:" . $sell->uuid();
$rent_plugin_id = "menu_link_content:" . $rent->uuid();

$upsert([
  "title" => "Programmes neufs",
  "uri" => "internal:/programmes-neufs",
  "parent" => $buy_plugin_id,
  "weight" => 0,
]);
$upsert([
  "title" => "Biens anciens",
  "uri" => "internal:/biens-anciens",
  "parent" => $buy_plugin_id,
  "weight" => 1,
]);
$upsert([
  "title" => "Investissement locatif",
  "uri" => "internal:/investissement-locatif",
  "parent" => $buy_plugin_id,
  "weight" => 2,
]);

$upsert([
  "title" => "Estimation",
  "uri" => "internal:/estimation",
  "parent" => $sell_plugin_id,
  "weight" => 0,
]);
$upsert([
  "title" => "Mise en vente",
  "uri" => "internal:/mise-en-vente",
  "parent" => $sell_plugin_id,
  "weight" => 1,
]);
$upsert([
  "title" => "Conseils vendeur",
  "uri" => "internal:/conseils-vendeur",
  "parent" => $sell_plugin_id,
  "weight" => 2,
]);

$upsert([
  "title" => "Location",
  "uri" => "internal:/location",
  "parent" => $rent_plugin_id,
  "weight" => 0,
]);
$upsert([
  "title" => "Gestion locative",
  "uri" => "internal:/gestion-locative",
  "parent" => $rent_plugin_id,
  "weight" => 1,
]);
$upsert([
  "title" => "Assurances",
  "uri" => "internal:/assurances",
  "parent" => $rent_plugin_id,
  "weight" => 2,
]);

print "Main menu seeded.\n";
'
}

log "Drop database"
"$DRUSH" sql:drop -y || true

log "Install fresh Drupal site (profile: minimal)"
"$DRUSH" site:install minimal --site-name="$SITE_NAME" --account-name="$ADMIN_USER" --account-pass="$ADMIN_PASS" --account-mail="$ADMIN_MAIL" -y

enable_modules "Drupal classic" "${DRUPAL_CLASSIC_MODULES[@]}"
enable_modules "Toolbar classic" "${TOOLBAR_CLASSIC_MODULES[@]}"
enable_modules "UI Suite" "${UI_SUITE_MODULES[@]}"
enable_modules "Translations" "${TRANSLATION_MODULES[@]}"
add_languages "${SITE_LANGUAGES[@]}"
enable_modules "Configuration" "${CONFIGURATION_MODULES[@]}"
enable_modules "Layout Builder" "${LAYOUT_BUILDER_MODULES[@]}"
enable_modules "UI Suite BNPPRE" "${UI_SUITE_BNPPRE_MODULES[@]}"
enable_modules "Menu" "${MENU_MODULES[@]}"

log "Enable themes"
"$DRUSH" theme:enable "$ADMIN_THEME" "$DEFAULT_THEME" -y || true
"$DRUSH" config:set system.theme admin "$ADMIN_THEME" -y || true
"$DRUSH" config:set system.theme default "$DEFAULT_THEME" -y || true

enable_modules "Gin admin" "${GIN_ADMIN_MODULES[@]}"

seed_main_menu_items

log "Rebuild cache"
"$DRUSH" cr -y

log "Done"
"$DRUSH" status
