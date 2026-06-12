#!/usr/bin/env bash
# B2B smoke tests — Compare table page (/compare).
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-page-cookies.txt"
HTML_FILE="${TMPDIR:-/tmp}/ps-compare-b2b-compare-page.html"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

assert_html_contains() {
  local needle="$1" label="$2"
  if grep -Fq "$needle" "$HTML_FILE"; then
    pass "$label"
  else
    fail "$label (missing: $needle)"
  fi
}

assert_html_not_contains() {
  local needle="$1" label="$2"
  if grep -Fq "$needle" "$HTML_FILE"; then
    fail "$label (found: $needle)"
  else
    pass "$label"
  fi
}

drush_clear() {
  docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$account = \\Drupal\\user\\Entity\\User::load(1);
if (\$account !== NULL) {
  \\Drupal::database()->delete(\"ps_compare_item\")->condition(\"uid\", (int) \$account->id())->execute();
}
print \"PASS:cleared\";
"' 2>/dev/null | tail -1
}

drush_seed() {
  local count="$1"
  docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$count = '"$count"';
\$account = \\Drupal\\user\\Entity\\User::load(1);
if (\$account === NULL) { print \"FAIL:no_admin\"; return; }
\\Drupal::service(\"account_switcher\")->switchTo(\$account);
\\Drupal::database()->delete(\"ps_compare_item\")->condition(\"uid\", (int) \$account->id())->execute();
\$storage = \\Drupal::entityTypeManager()->getStorage(\"node\");
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 4)->execute());
if (count(\$ids) < \$count) { \\Drupal::service(\"account_switcher\")->switchBack(); print \"FAIL:not_enough_offers\"; return; }
\$manager = \\Drupal::service(\"ps_compare.manager\");
for (\$i = 0; \$i < \$count; \$i++) { \$manager->addCompare(\$storage->load(\$ids[\$i])); }
\\Drupal::service(\"account_switcher\")->switchBack();
print \"PASS:seeded_\$count\";
"' 2>/dev/null | tail -1
}

login_admin() {
  rm -f "$COOKIE_JAR"
  touch "$COOKIE_JAR"
  local uli
  uli=$(docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush uli --name=admin --uri=http://localhost:8080' 2>/dev/null | tail -1)
  curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null "$uli" 2>/dev/null || true
}

fetch_compare_page() {
  curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$BASE/compare" > "$HTML_FILE"
}

assert_page_has_one_of() {
  local label="$1"
  shift
  for needle in "$@"; do
    if grep -Fq "$needle" "$HTML_FILE"; then
      pass "$label (matched: $needle)"
      return
    fi
  done
  fail "$label (missing all of: $*)"
}

drush_build_columns() {
  local expected="$1"
  docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$expected = '"$expected"';
\$account = \\Drupal\\user\\Entity\\User::load(1);
\\Drupal::service(\"account_switcher\")->switchTo(\$account);
\\Drupal::database()->delete(\"ps_compare_item\")->condition(\"uid\", (int) \$account->id())->execute();
\$storage = \\Drupal::entityTypeManager()->getStorage(\"node\");
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 4)->execute());
\$manager = \\Drupal::service(\"ps_compare.manager\");
for (\$i = 0; \$i < \$expected; \$i++) { \$manager->addCompare(\$storage->load(\$ids[\$i])); }
\$builder = \\Drupal::service(\"ps_compare.page_builder\");
\$build = \$builder->buildPage();
\$columns = \$build[\"#columns\"] ?? [];
\$sections = \$build[\"#sections\"] ?? [];
\$rowCount = 0;
foreach (\$sections as \$section) {
  \$rowCount += count(\$section[\"rows\"] ?? []);
}
\\Drupal::service(\"account_switcher\")->switchBack();
if (count(\$columns) !== \$expected) { print \"FAIL:columns_\" . count(\$columns); return; }
if (\$expected >= 2 && \$rowCount < 5) { print \"FAIL:rows_\" . \$rowCount; return; }
if (\$expected >= 2 && count(\$sections) < 3) { print \"FAIL:sections_\" . count(\$sections); return; }
print \"PASS:page_builder_\$expected\";
"' 2>/dev/null | tail -1
}

echo "=== PS Compare B2B — Compare page ($BASE) ==="

echo "--- Empty state (anonymous) ---"
rm -f "$COOKIE_JAR"
touch "$COOKIE_JAR"
drush_clear >/dev/null || true

fetch_compare_page
CODE=$(curl -sL -m 60 -b "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$BASE/compare" 2>/dev/null || echo "000")
if [[ "$CODE" == "200" ]]; then
  pass "Anonymous /compare HTTP 200"
else
  fail "Anonymous /compare HTTP $CODE"
fi
assert_html_contains 'ps-compare-empty-state' "Empty state markup when list is empty"

echo "--- Insufficient selection (1 offer, authenticated admin) ---"
login_admin
RESULT=$(drush_seed 1)
if [[ "$RESULT" != "PASS:seeded_1" ]]; then
  fail "Seed 1 offer for compare ($RESULT)"
else
  pass "Seeded 1 offer in compare list (uid=1 via Drush)"
fi

fetch_compare_page
CODE=$(curl -sL -m 60 -b "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$BASE/compare" 2>/dev/null || echo "000")
if [[ "$CODE" == "200" ]]; then
  pass "Authenticated /compare with 1 item HTTP 200"
else
  fail "Authenticated /compare with 1 item HTTP $CODE"
fi
assert_page_has_one_of "Insufficient selection guidance" 'ps-compare-empty-state' 'Select at least' 'at least 2'

echo "--- Comparison table (2 offers, authenticated admin) ---"
RESULT=$(drush_seed 2)
if [[ "$RESULT" != "PASS:seeded_2" ]]; then
  fail "Seed 2 offers for compare ($RESULT)"
else
  pass "Seeded 2 offers in compare list"
fi

fetch_compare_page
CODE=$(curl -sL -m 60 -b "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$BASE/compare" 2>/dev/null || echo "000")
if [[ "$CODE" == "200" ]]; then
  pass "Authenticated /compare with 2 items HTTP 200"
else
  fail "Authenticated /compare with 2 items HTTP $CODE"
fi

assert_html_contains 'ps-compare-page' "Compare page wrapper present"
assert_html_contains 'ps-compare-table' "Comparison table present"
assert_html_contains 'data-ps-compare-table-head-pin' "Comparison table sticky head pin"
assert_html_contains 'ps-compare-table--body' "Comparison table body markup"
assert_html_not_contains 'ps-compare-table__column-reference' "Column header omits reference (summary rows only)"
assert_html_not_contains 'ps-compare-table__column-price' "Column header omits price (summary rows only)"
assert_html_contains 'ps-compare-table__section-row' "Comparison table section headers present"
assert_html_contains 'View property' "Column CTA uses View property label"
assert_html_contains 'ps-compare-table__row--photos' "Photos row as first table body row"
assert_html_not_contains 'compare-section-photos' "Photos section header removed"
assert_html_not_contains 'ps-compare-table__row--reference' "Reference row removed from comparison table"
assert_page_has_one_of "Aménagement section present" 'Aménagement' 'Layout'
assert_html_contains 'ps-compare-table__row--price' "Price row in Aménagement section"
assert_html_contains 'ps-compare-table__row--surface' "Surface row in Aménagement section"
assert_html_contains 'ps-surface-compare-wrap' "Surface cell includes floor plan icon wrapper"
assert_page_has_one_of "Compare budget from prefix or amount" 'ps-offer-budget-compare__from' 'ps-offer-budget-compare'
assert_html_not_contains 'ps-compare-table__row--location' "Location row removed from comparison table"
assert_html_not_contains 'ps-compare-location-cell' "Location mini-map cell removed from comparison table"
assert_html_not_contains 'ps-compare-page__summary' "Comparison summary banner removed"
assert_html_contains 'data-ps-compare-share' "Share comparison button present"
assert_html_not_contains 'data-ps-compare-share" disabled' "Share button not disabled"
assert_html_contains 'data-ps-compare-gallery-track' "Compare gallery track present"
assert_html_contains 'ps-compare-table__column-actions' "Column header action row present"
assert_html_contains 'ps-compare-button--search' "Compare remove button uses search variant in column header"
assert_html_contains 'ps-favorite-button--search' "Favorite button uses search variant in column header"
assert_html_contains 'ps-compare-table__column-action--compare' "Compare action wrapper present"
assert_html_contains 'ps-compare-table__column-action--favorite' "Favorite action wrapper present"

BUILD_RESULT=$(drush_build_columns 2)
if [[ "$BUILD_RESULT" == "PASS:page_builder_2" ]]; then
  pass "ComparePageBuilder renders 2 columns and rows"
else
  fail "ComparePageBuilder ($BUILD_RESULT)"
fi

UNDO_LIB=$(docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$lib = \\Drupal::service(\"library.discovery\")->getLibraryByName(\"ps_compare\", \"compare-toggle\");
\$paths = array_map(static fn (array \$item): string => (string) (\$item[\"data\"] ?? \"\"), \$lib[\"js\"] ?? []);
print in_array(\"modules/custom/ps_compare/js/ps-compare-undo.js\", \$paths, true) ? \"PASS:undo_lib\" : \"FAIL:undo_lib\";
"' 2>/dev/null | tail -1)
if [[ "$UNDO_LIB" == "PASS:undo_lib" ]]; then
  pass "Compare undo JS registered in compare-toggle library"
else
  fail "Compare undo library ($UNDO_LIB)"
fi

echo "--- Cleanup ---"
drush_clear >/dev/null || true
pass "Compare list cleared after tests"

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
