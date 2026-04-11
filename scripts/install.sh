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

$find = $upsert([
  "title" => "Find a property",
  "uri" => "route:<nolink>",
  "weight" => 0,
  "description" => "Browse opportunities",
  "expanded" => TRUE,
]);

$about = $upsert([
  "title" => "About us",
  "uri" => "route:<nolink>",
  "weight" => 1,
  "description" => "Who we are",
  "expanded" => TRUE,
]);

$solutions = $upsert([
  "title" => "Solutions",
  "uri" => "route:<nolink>",
  "weight" => 2,
  "description" => "Business expertise",
  "expanded" => TRUE,
]);

$news = $upsert([
  "title" => "Latest News",
  "uri" => "route:<nolink>",
  "weight" => 3,
  "description" => "Insights and press",
  "expanded" => TRUE,
]);

$nos_services = $upsert([
  "title" => "Nos services",
  "uri" => "route:<nolink>",
  "weight" => 4,
  "description" => "Solutions et expertises",
  "expanded" => TRUE,
]);

$find_id = "menu_link_content:" . $find->uuid();
$about_id = "menu_link_content:" . $about->uuid();
$solutions_id = "menu_link_content:" . $solutions->uuid();
$news_id = "menu_link_content:" . $news->uuid();
$nos_services_id = "menu_link_content:" . $nos_services->uuid();

$find_buy = $upsert([
  "title" => "Buy",
  "uri" => "route:<nolink>",
  "parent" => $find_id,
  "weight" => 0,
  "description" => "Acquisition support",
  "expanded" => TRUE,
]);
$find_lease = $upsert([
  "title" => "Lease",
  "uri" => "route:<nolink>",
  "parent" => $find_id,
  "weight" => 1,
  "description" => "Leasing support",
  "expanded" => TRUE,
]);
$find_invest = $upsert([
  "title" => "Invest",
  "uri" => "route:<nolink>",
  "parent" => $find_id,
  "weight" => 2,
  "description" => "Investor services",
  "expanded" => TRUE,
]);

$find_buy_id = "menu_link_content:" . $find_buy->uuid();
$find_lease_id = "menu_link_content:" . $find_lease->uuid();
$find_invest_id = "menu_link_content:" . $find_invest->uuid();

$upsert(["title" => "Offices", "uri" => "internal:/offices", "parent" => $find_buy_id, "weight" => 0]);
$upsert(["title" => "Retail", "uri" => "internal:/retail", "parent" => $find_buy_id, "weight" => 1]);
$upsert(["title" => "Logistics", "uri" => "internal:/logistics", "parent" => $find_buy_id, "weight" => 2]);

$upsert(["title" => "Flexible offices", "uri" => "internal:/flexible-offices", "parent" => $find_lease_id, "weight" => 0]);
$upsert(["title" => "Coworking", "uri" => "internal:/coworking", "parent" => $find_lease_id, "weight" => 1]);
$upsert(["title" => "Asset management", "uri" => "internal:/asset-management", "parent" => $find_lease_id, "weight" => 2]);

$upsert(["title" => "Funds", "uri" => "internal:/funds", "parent" => $find_invest_id, "weight" => 0]);
$upsert(["title" => "Transactions", "uri" => "internal:/transactions", "parent" => $find_invest_id, "weight" => 1]);
$upsert(["title" => "Research", "uri" => "internal:/research", "parent" => $find_invest_id, "weight" => 2]);

$about_company = $upsert([
  "title" => "Company",
  "uri" => "route:<nolink>",
  "parent" => $about_id,
  "weight" => 0,
  "description" => "Our profile",
  "expanded" => TRUE,
]);
$about_people = $upsert([
  "title" => "People",
  "uri" => "route:<nolink>",
  "parent" => $about_id,
  "weight" => 1,
  "description" => "Leadership and teams",
  "expanded" => TRUE,
]);
$about_careers = $upsert([
  "title" => "Careers",
  "uri" => "route:<nolink>",
  "parent" => $about_id,
  "weight" => 2,
  "description" => "Join us",
  "expanded" => TRUE,
]);

$about_company_id = "menu_link_content:" . $about_company->uuid();
$about_people_id = "menu_link_content:" . $about_people->uuid();
$about_careers_id = "menu_link_content:" . $about_careers->uuid();

$upsert(["title" => "Our story", "uri" => "internal:/our-story", "parent" => $about_company_id, "weight" => 0]);
$upsert(["title" => "Values", "uri" => "internal:/values", "parent" => $about_company_id, "weight" => 1]);
$upsert(["title" => "Governance", "uri" => "internal:/governance", "parent" => $about_company_id, "weight" => 2]);

$upsert(["title" => "Executive team", "uri" => "internal:/executive-team", "parent" => $about_people_id, "weight" => 0]);
$upsert(["title" => "Local teams", "uri" => "internal:/local-teams", "parent" => $about_people_id, "weight" => 1]);
$upsert(["title" => "Diversity", "uri" => "internal:/diversity", "parent" => $about_people_id, "weight" => 2]);

$upsert(["title" => "Open roles", "uri" => "internal:/open-roles", "parent" => $about_careers_id, "weight" => 0]);
$upsert(["title" => "Graduate program", "uri" => "internal:/graduate-program", "parent" => $about_careers_id, "weight" => 1]);
$upsert(["title" => "Life at BNPPRE", "uri" => "internal:/life-at-bnppre", "parent" => $about_careers_id, "weight" => 2]);

$solutions_consulting = $upsert([
  "title" => "Consulting",
  "uri" => "route:<nolink>",
  "parent" => $solutions_id,
  "weight" => 0,
  "description" => "Advisory services",
  "expanded" => TRUE,
]);
$solutions_capital = $upsert([
  "title" => "Capital Markets",
  "uri" => "route:<nolink>",
  "parent" => $solutions_id,
  "weight" => 1,
  "description" => "Transactions and debt",
  "expanded" => TRUE,
  "classes" => ["is-megamenu-aside-source"],
  "data_attributes" => [
    "aside-title" => "Need strategic support?",
    "aside-text" => "Talk with our consultants to shape your next move.",
    "aside-link-label" => "Contact our experts",
    "aside-link-url" => "/contact",
  ],
]);
$solutions_management = $upsert([
  "title" => "Property Management",
  "uri" => "route:<nolink>",
  "parent" => $solutions_id,
  "weight" => 2,
  "description" => "Operations and performance",
  "expanded" => TRUE,
]);

$solutions_consulting_id = "menu_link_content:" . $solutions_consulting->uuid();
$solutions_capital_id = "menu_link_content:" . $solutions_capital->uuid();
$solutions_management_id = "menu_link_content:" . $solutions_management->uuid();

$upsert(["title" => "Workplace strategy", "uri" => "internal:/workplace-strategy", "parent" => $solutions_consulting_id, "weight" => 0]);
$upsert(["title" => "Valuation", "uri" => "internal:/valuation", "parent" => $solutions_consulting_id, "weight" => 1]);
$upsert(["title" => "Sustainability", "uri" => "internal:/sustainability", "parent" => $solutions_consulting_id, "weight" => 2]);

$upsert(["title" => "Investment sales", "uri" => "internal:/investment-sales", "parent" => $solutions_capital_id, "weight" => 0]);
$upsert(["title" => "Debt advisory", "uri" => "internal:/debt-advisory", "parent" => $solutions_capital_id, "weight" => 1]);
$upsert(["title" => "Structured finance", "uri" => "internal:/structured-finance", "parent" => $solutions_capital_id, "weight" => 2]);

$upsert(["title" => "Facilities", "uri" => "internal:/facilities", "parent" => $solutions_management_id, "weight" => 0]);
$upsert(["title" => "Technical", "uri" => "internal:/technical", "parent" => $solutions_management_id, "weight" => 1]);
$upsert(["title" => "Data & analytics", "uri" => "internal:/data-analytics", "parent" => $solutions_management_id, "weight" => 2]);

$news_press = $upsert([
  "title" => "Press",
  "uri" => "route:<nolink>",
  "parent" => $news_id,
  "weight" => 0,
  "description" => "Media releases",
  "expanded" => TRUE,
]);
$news_insights = $upsert([
  "title" => "Insights",
  "uri" => "route:<nolink>",
  "parent" => $news_id,
  "weight" => 1,
  "description" => "Market views",
  "expanded" => TRUE,
]);
$news_events = $upsert([
  "title" => "Events",
  "uri" => "route:<nolink>",
  "parent" => $news_id,
  "weight" => 2,
  "description" => "Upcoming events",
  "expanded" => TRUE,
]);

$news_press_id = "menu_link_content:" . $news_press->uuid();
$news_insights_id = "menu_link_content:" . $news_insights->uuid();
$news_events_id = "menu_link_content:" . $news_events->uuid();

$upsert(["title" => "Press releases", "uri" => "internal:/press-releases", "parent" => $news_press_id, "weight" => 0]);
$upsert(["title" => "Media contacts", "uri" => "internal:/media-contacts", "parent" => $news_press_id, "weight" => 1]);
$upsert(["title" => "Brand assets", "uri" => "internal:/brand-assets", "parent" => $news_press_id, "weight" => 2]);

$upsert(["title" => "Reports", "uri" => "internal:/reports", "parent" => $news_insights_id, "weight" => 0]);
$upsert(["title" => "Case studies", "uri" => "internal:/case-studies", "parent" => $news_insights_id, "weight" => 1]);
$upsert(["title" => "Podcasts", "uri" => "internal:/podcasts", "parent" => $news_insights_id, "weight" => 2]);

$upsert(["title" => "Webinars", "uri" => "internal:/webinars", "parent" => $news_events_id, "weight" => 0]);
$upsert(["title" => "Conferences", "uri" => "internal:/conferences", "parent" => $news_events_id, "weight" => 1]);
$upsert(["title" => "On-demand", "uri" => "internal:/on-demand", "parent" => $news_events_id, "weight" => 2]);

$nos_services_market = $upsert([
  "title" => "Marches",
  "uri" => "route:<nolink>",
  "parent" => $nos_services_id,
  "weight" => 0,
  "description" => "Expertises marche",
  "expanded" => TRUE,
]);
$nos_services_support = $upsert([
  "title" => "Accompagnement",
  "uri" => "route:<nolink>",
  "parent" => $nos_services_id,
  "weight" => 1,
  "description" => "Conseils et support",
  "expanded" => TRUE,
]);
$nos_services_tools = $upsert([
  "title" => "Outils",
  "uri" => "route:<nolink>",
  "parent" => $nos_services_id,
  "weight" => 2,
  "description" => "Services digitaux",
  "expanded" => TRUE,
]);

$nos_services_market_id = "menu_link_content:" . $nos_services_market->uuid();
$nos_services_support_id = "menu_link_content:" . $nos_services_support->uuid();
$nos_services_tools_id = "menu_link_content:" . $nos_services_tools->uuid();

$upsert(["title" => "Bureaux", "uri" => "internal:/bureaux", "parent" => $nos_services_market_id, "weight" => 0]);
$upsert(["title" => "Commerces", "uri" => "internal:/commerces", "parent" => $nos_services_market_id, "weight" => 1]);
$upsert(["title" => "Logistique", "uri" => "internal:/logistique", "parent" => $nos_services_market_id, "weight" => 2]);

$upsert(["title" => "Audit", "uri" => "internal:/audit", "parent" => $nos_services_support_id, "weight" => 0]);
$upsert(["title" => "Pilotage", "uri" => "internal:/pilotage", "parent" => $nos_services_support_id, "weight" => 1]);
$upsert(["title" => "Formation", "uri" => "internal:/formation", "parent" => $nos_services_support_id, "weight" => 2]);

$upsert(["title" => "Estimateur", "uri" => "internal:/estimateur", "parent" => $nos_services_tools_id, "weight" => 0]);
$upsert(["title" => "Comparateur", "uri" => "internal:/comparateur", "parent" => $nos_services_tools_id, "weight" => 1]);
$upsert(["title" => "Alertes", "uri" => "internal:/alertes", "parent" => $nos_services_tools_id, "weight" => 2]);

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
