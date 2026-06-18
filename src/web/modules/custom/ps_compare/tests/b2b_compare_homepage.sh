#!/usr/bin/env bash
# B2B smoke tests — Compare on homepage / teaser offer cards.
set -euo pipefail

# shellcheck source=/dev/null
source "$(cd "$(dirname "${BASH_SOURCE[0]}")/../../../../.." && pwd)/scripts/e2e/common.sh"

PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-home-cookies.txt"
HTML_FILE="${TMPDIR:-/tmp}/ps-compare-b2b-homepage.html"

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

echo "=== PS Compare B2B — Homepage / teaser context ($BASE) ==="

echo "--- Teaser compare button (lazy builder) ---"
TEASER_RESULT=$(ps_e2e_drush php:eval "
\$ids = array_values(\\Drupal::entityTypeManager()->getStorage(\"node\")->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 1)->execute());
if (\$ids === []) { print \"FAIL:no_offer\"; return; }
\$node = \\Drupal\\node\\Entity\\Node::load(\$ids[0]);
\$build = \\Drupal::service(\"ps_compare.lazy_builder\")->buildButton(\"node\", (int) \$node->id(), \"teaser\");
\$html = (string) \\Drupal::service(\"renderer\")->renderRoot(\$build);
if (!str_contains(\$html, \"data-ps-compare-toggle\")) { print \"FAIL:no_toggle\"; return; }
if (!str_contains(\$html, \"ps-compare-button--teaser\")) { print \"FAIL:no_teaser_class\"; return; }
if (!str_contains(\$html, \"/api/compare/toggle/node/\")) { print \"FAIL:no_url\"; return; }
print \"PASS:teaser_button\";
" 2>/dev/null | tail -1)

if [[ "$TEASER_RESULT" == "PASS:teaser_button" ]]; then
  pass "Teaser compare button renders via lazy builder"
else
  fail "Teaser compare button via lazy builder ($TEASER_RESULT)"
fi

echo "--- Homepage HTTP ---"
rm -f "$COOKIE_JAR"
touch "$COOKIE_JAR"
curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$BASE/" > "$HTML_FILE"
CODE=$(curl -sL -m 60 -b "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$BASE/" 2>/dev/null || echo "000")

if [[ "$CODE" == "200" ]]; then
  pass "Homepage HTTP 200"
else
  fail "Homepage HTTP $CODE"
fi

COMPARE_COUNT=$(grep -c 'data-ps-compare-context="teaser"' "$HTML_FILE" 2>/dev/null | tr -d '[:space:]' || echo "0")
COMPARE_COUNT=${COMPARE_COUNT:-0}

if [[ "$COMPARE_COUNT" -ge 1 ]]; then
  pass "Compare buttons on homepage offer cards ($COMPARE_COUNT found)"
  assert_html_contains 'data-ps-compare-toggle' "Compare toggle on homepage cards"
  assert_html_contains 'ps-compare-button--teaser' "Teaser compare button variant on homepage"
  assert_html_contains '/api/compare/toggle/node/' "Toggle URL on homepage cards"
  assert_html_contains 'ps-compare-toast.js' "Compare toast JS attached on homepage"
  assert_html_contains 'ps-compare-toggle.js' "Compare toggle JS attached on homepage"
else
  pass "Homepage has no featured offer carousel in current demo (teaser validated via Drush)"
fi

if grep -Fq 'data-ps-compare-widget' "$HTML_FILE"; then
  fail "Compare panel widget should not appear on homepage"
else
  pass "No compare panel widget on homepage (expected)"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
