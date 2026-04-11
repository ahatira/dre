#!/usr/bin/env bash

set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

DRUSH="${DRUSH:-$PROJECT_ROOT/vendor/bin/drush}"
MODULE_NAME="ps_default_content"
CONTENT_DIR="$PROJECT_ROOT/web/modules/custom/$MODULE_NAME/content/menu_link_content"

if [[ ! -x "$DRUSH" ]]; then
  echo "[default-content] drush not found or not executable -> $DRUSH" >&2
  exit 1
fi

if [[ ! -d "$CONTENT_DIR" ]]; then
  echo "[default-content] target folder not found -> $CONTENT_DIR" >&2
  exit 1
fi

mkdir -p "$CONTENT_DIR"
rm -f "$CONTENT_DIR"/*.yml

echo "[default-content] Build/update main menu links in Drupal..."
MAP_OUTPUT="$($DRUSH php:eval '
$storage = \Drupal::entityTypeManager()->getStorage("menu_link_content");

$tree = [
  ["key" => "find_property", "uuid" => "7ea169e1-6b13-4c5a-a620-746522248158", "title" => "Find a Property", "uri" => "internal:/en/property-search", "children" => [
    ["key" => "property_search", "uuid" => "959fcf80-a674-4fd6-89bd-1253027a7033", "title" => "Property Search", "uri" => "internal:/en/property-search", "children" => []],
    ["key" => "offices", "uuid" => "41dc667a-5e37-40cd-b9a9-81bce0bfa909", "title" => "Offices", "uri" => "internal:/en/property-search?asset=offices", "children" => [
      ["key" => "madrid_offices", "uuid" => "6db72db1-310e-4a42-a043-f5363a521f1a", "title" => "Madrid Offices", "uri" => "internal:/en/offices/madrid", "children" => []],
      ["key" => "barcelona_offices", "uuid" => "2b0ef7f2-d7a0-4fe8-82e7-1bade66a55ff", "title" => "Barcelona Offices", "uri" => "internal:/en/offices/barcelona", "children" => []],
      ["key" => "valencia_offices", "uuid" => "06d56bfa-9317-4ead-b4cb-fd1c1820b246", "title" => "Valencia Offices", "uri" => "internal:/en/offices/valencia", "children" => []],
    ]],
    ["key" => "industrial_logistics", "uuid" => "541cb480-7b86-4755-99d8-fa5203ddf33e", "title" => "Industrial & Logistics", "uri" => "internal:/en/property-search?asset=industrial", "children" => [
      ["key" => "madrid_industrial", "uuid" => "b2686213-a616-45b6-8806-128f4b6b19ad", "title" => "Madrid Industrial & Logistics", "uri" => "internal:/en/industrial/madrid", "children" => []],
      ["key" => "barcelona_industrial", "uuid" => "cf43e45c-1eec-4d45-801c-95b8bee6346c", "title" => "Barcelona Industrial & Logistics", "uri" => "internal:/en/industrial/barcelona", "children" => []],
      ["key" => "valencia_industrial", "uuid" => "79919ec0-cd48-4e53-8e17-15da58d69952", "title" => "Valencia Industrial & Logistics", "uri" => "internal:/en/industrial/valencia", "children" => []],
    ]],
    ["key" => "living", "uuid" => "2e767889-2274-43a4-893f-076cfcd6c415", "title" => "Living (Residential)", "uri" => "internal:/en/living", "children" => []],
  ]],
  ["key" => "solutions", "uuid" => "827df9df-d4ac-469c-a0a3-82f85239959e", "title" => "Solutions", "uri" => "internal:/en/solutions", "children" => [
    ["key" => "investors", "uuid" => "140ebc88-3f73-42a4-af81-8cfd03ec1931", "title" => "Investors", "uri" => "internal:/en/solutions/investors", "children" => [
      ["key" => "investors_advisory", "uuid" => "f97615a7-7407-47b0-ae7e-ddae739c69dd", "title" => "Advisory & Transactions", "uri" => "internal:/en/solutions/investors/advisory-transactions", "children" => []],
      ["key" => "investors_management", "uuid" => "6a5fd662-dbef-4def-b7e5-656093508a19", "title" => "Property & Project Management", "uri" => "internal:/en/solutions/investors/property-management", "children" => []],
      ["key" => "investors_valuation", "uuid" => "5f8e4046-406d-4bee-bbab-be059382e9eb", "title" => "Valuation", "uri" => "internal:/en/solutions/investors/valuation", "children" => []],
    ]],
    ["key" => "owners", "uuid" => "a0982980-208b-49cc-ae37-1e97e6765bd0", "title" => "Owners", "uri" => "internal:/en/solutions/owners", "children" => [
      ["key" => "owners_land", "uuid" => "c9e8e82b-e649-4b13-bee4-f7d161e23090", "title" => "Land Advisory & Transactions", "uri" => "internal:/en/solutions/owners/land", "children" => []],
      ["key" => "owners_commercial", "uuid" => "e6db0b38-e5e4-426d-9779-cf45ac70a378", "title" => "Commercial Real Estate Advisory", "uri" => "internal:/en/solutions/owners/commercial", "children" => []],
      ["key" => "owners_residential", "uuid" => "18f46da4-fe62-4771-92a2-07f1fbe4e954", "title" => "Residential Advisory", "uri" => "internal:/en/solutions/owners/residential", "children" => []],
      ["key" => "owners_investment", "uuid" => "c910b491-8791-4b14-a723-0f71bf86c92b", "title" => "Investment Management", "uri" => "internal:/en/solutions/owners/investment-management", "children" => []],
      ["key" => "owners_valuation", "uuid" => "85287024-2b91-4073-9ea0-7d1841f6f21e", "title" => "Valuation", "uri" => "internal:/en/solutions/owners/valuation", "children" => []],
    ]],
    ["key" => "corporate_occupiers", "uuid" => "00d6ac42-bc24-457d-a8a0-c8e8d0a46571", "title" => "Corporate Occupiers", "uri" => "internal:/en/solutions/corporate-occupiers", "children" => [
      ["key" => "corporate_transactions", "uuid" => "109a2009-3448-41d1-b656-f590b7245c54", "title" => "Transactions & Advisory", "uri" => "internal:/en/solutions/corporate-occupiers/transactions", "children" => []],
      ["key" => "corporate_change", "uuid" => "6ceb7eeb-2922-4d58-8ecf-5521bcb7165e", "title" => "Change Management", "uri" => "internal:/en/solutions/corporate-occupiers/change-management", "children" => []],
    ]],
  ]],
  ["key" => "about_us", "uuid" => "0eb2c516-235d-44ce-a89d-348727629573", "title" => "About Us", "uri" => "internal:/en/about-us", "children" => [
    ["key" => "about_us_about", "uuid" => "330ae7ab-ff5e-4782-8f11-84368e30c1ef", "title" => "About Us", "uri" => "internal:/en/about-us/about-us", "children" => []],
    ["key" => "our_values", "uuid" => "5c8e882b-2452-4644-9465-88e652396cc8", "title" => "Our Values", "uri" => "internal:/en/about-us/our-values", "children" => []],
    ["key" => "careers", "uuid" => "3cc0931f-6066-4777-b6cb-34ddf05dd181", "title" => "Careers", "uri" => "internal:/en/about-us/work-with-us", "children" => []],
  ]],
  ["key" => "latest_news", "uuid" => "816567ef-c662-4581-9b24-c351fcc86a56", "title" => "Latest News", "uri" => "internal:/en/latest-news", "children" => [
    ["key" => "research_reports", "uuid" => "6bf1def5-6adb-4e3b-99da-3441d8c10bef", "title" => "Research Reports", "uri" => "internal:/en/latest-news/research-reports", "children" => []],
    ["key" => "press_releases", "uuid" => "0d0beb8a-8793-49e9-8656-6ed14d611e12", "title" => "Press Releases", "uri" => "internal:/en/latest-news/press-releases", "children" => []],
    ["key" => "real_estate_trends", "uuid" => "246acdb0-c134-455d-91be-7fa11378b44f", "title" => "Real Estate Trends", "uri" => "internal:/en/latest-news/real-estate-trends", "children" => []],
  ]],
];

$map = [];

$upsert = function (array $item, string $parent, int $weight) use (&$upsert, &$storage, &$map): void {
  $loaded = $storage->loadByProperties(["uuid" => $item["uuid"]]);
  $entity = $loaded ? reset($loaded) : NULL;

  if (!$entity) {
    $entity = $storage->create([
      "uuid" => $item["uuid"],
      "title" => $item["title"],
      "menu_name" => "main",
      "link" => ["uri" => $item["uri"]],
      "parent" => $parent,
      "expanded" => TRUE,
      "enabled" => TRUE,
      "weight" => $weight,
    ]);
  }

  $entity->set("title", $item["title"]);
  $entity->set("menu_name", "main");
  $entity->set("link", ["uri" => $item["uri"]]);
  $entity->set("parent", $parent);
  $entity->set("expanded", TRUE);
  $entity->set("enabled", TRUE);
  $entity->set("weight", $weight);
  $entity->save();

  // Keep a single entity per (title, URI, parent) tuple to avoid duplicates.
  $duplicateIds = \Drupal::entityQuery("menu_link_content")
    ->accessCheck(FALSE)
    ->condition("menu_name", "main")
    ->condition("title", $item["title"])
    ->condition("link.uri", $item["uri"])
    ->condition("parent", $parent)
    ->execute();

  foreach ($duplicateIds as $duplicateId) {
    if ((int) $duplicateId !== (int) $entity->id()) {
      $duplicate = $storage->load($duplicateId);
      if ($duplicate) {
        $duplicate->delete();
      }
    }
  }

  $map[$item["key"]] = [
    "id" => (int) $entity->id(),
    "uuid" => $entity->uuid(),
    "title" => $item["title"],
  ];

  $childWeight = 0;
  foreach ($item["children"] as $child) {
    $upsert($child, "menu_link_content:" . $entity->uuid(), $childWeight++);
  }
};

$weight = 0;
foreach ($tree as $item) {
  $upsert($item, "", $weight++);
}

foreach ($map as $key => $values) {
  print $key . "|" . $values["id"] . "|" . $values["uuid"] . "|" . $values["title"] . PHP_EOL;
}
')"

if [[ -z "$MAP_OUTPUT" ]]; then
  echo "[default-content] no menu entries were created or found" >&2
  exit 1
fi

echo "[default-content] Export menu_link_content entities with drush default-content:export..."
UUID_LIST=""
while IFS='|' read -r KEY ENTITY_ID UUID TITLE; do
  [[ -n "$ENTITY_ID" && -n "$UUID" ]] || continue
  "$DRUSH" default-content:export menu_link_content "$ENTITY_ID" --file="$CONTENT_DIR/$UUID.yml"
  if [[ -z "$UUID_LIST" ]]; then
    UUID_LIST="- $UUID"
  else
    UUID_LIST+=$'\n- '$UUID
  fi
  echo "  - entity_id=$ENTITY_ID uuid=$UUID"
done <<< "$MAP_OUTPUT"

echo "[default-content] Export complete -> $CONTENT_DIR"
echo "[default-content] Suggested default_content list for ps_default_content.info.yml:"
echo "default_content:"
echo "  menu_link_content:"
echo "$UUID_LIST"
