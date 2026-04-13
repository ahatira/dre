#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

DRUSH="${DRUSH:-$PROJECT_ROOT/vendor/bin/drush}"
MODULE_NAME="ps_default_content"
CONTENT_DIR="$PROJECT_ROOT/web/modules/custom/$MODULE_NAME/content/menu_link_content"
TARGET_MENUS=("footer_business_websites" "footer_about_bnppre" "footer")

if [[ ! -x "$DRUSH" ]]; then
  echo "[default-content] drush not found or not executable -> $DRUSH" >&2
  exit 1
fi

if [[ ! -d "$CONTENT_DIR" ]]; then
  echo "[default-content] target folder not found -> $CONTENT_DIR" >&2
  exit 1
fi

echo "[default-content] Resolve menu_link_content IDs for footer menus..."
MAP_OUTPUT="$($DRUSH php:eval '
$menus = ["footer_business_websites", "footer_about_bnppre", "footer"];
$storage = \Drupal::entityTypeManager()->getStorage("menu_link_content");

foreach ($menus as $menu) {
  $entities = $storage->loadByProperties(["menu_name" => $menu]);
  usort($entities, fn($a, $b) => $a->getWeight() <=> $b->getWeight());
  foreach ($entities as $entity) {
    print $menu . "|" . $entity->id() . "|" . $entity->uuid() . "|" . $entity->label() . PHP_EOL;
  }
}
')"

if [[ -z "$MAP_OUTPUT" ]]; then
  echo "[default-content] no footer links found in menus: ${TARGET_MENUS[*]}" >&2
  exit 1
fi

echo "[default-content] Export footer menu links with default-content:export..."
UUID_LIST=""
while IFS='|' read -r MENU ENTITY_ID UUID TITLE; do
  [[ -n "$ENTITY_ID" && -n "$UUID" ]] || continue
  "$DRUSH" default-content:export menu_link_content "$ENTITY_ID" --file="$CONTENT_DIR/$UUID.yml"
  if [[ -z "$UUID_LIST" ]]; then
    UUID_LIST="- $UUID"
  else
    UUID_LIST+=$'\n- '$UUID
  fi
  echo "  - menu=$MENU entity_id=$ENTITY_ID uuid=$UUID title=$TITLE"
done <<< "$MAP_OUTPUT"

echo "[default-content] Export complete -> $CONTENT_DIR"
echo "[default-content] Suggested UUIDs to keep in ps_default_content.info.yml:"
echo "$UUID_LIST"
