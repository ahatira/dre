#!/usr/bin/env bash
# B2B smoke tests — Compare feature on property search page (/find-property).
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-cookies.txt"
HTML_FILE="${TMPDIR:-/tmp}/ps-compare-b2b-find-property.html"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

fetch() {
  curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$1" 2>/dev/null || echo ""
}

status_code() {
  local url="$1"
  local code attempt
  for attempt in 1 2 3; do
    code=$(curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
    if [[ "$code" == "200" ]]; then
      echo "$code"
      return
    fi
    sleep 2
  done
  echo "$code"
}

assert_http_200() {
  local url="$1" label="$2"
  local code
  code=$(status_code "$url")
  if [[ "$code" == "200" ]]; then
    pass "$label (HTTP 200)"
  else
    fail "$label (HTTP $code) — $url"
  fi
}

assert_http_403() {
  local url="$1" label="$2"
  local code
  code=$(curl -sL -m 60 -b "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
  if [[ "$code" == "403" ]]; then
    pass "$label (HTTP 403)"
  else
    fail "$label (HTTP $code) — $url"
  fi
}

assert_html_contains() {
  local needle="$1" label="$2"
  if grep -Fq "$needle" "$HTML_FILE"; then
    pass "$label"
  else
    fail "$label (missing: $needle)"
  fi
}

assert_json_field() {
  local url="$1" field="$2" label="$3"
  local body value
  body=$(curl -s -m 60 -b "$COOKIE_JAR" -c "$COOKIE_JAR" "$url" 2>/dev/null || echo "")
  value=$(python3 - <<PY
import json, sys
try:
  data = json.loads('''${body//\'/\\\'\'}''')
  print(data.get("${field}", ""))
except Exception:
  print("")
PY
)
  if [[ -n "$value" ]]; then
    pass "$label ($field=$value)"
  else
    fail "$label (missing field $field) — $url"
  fi
}

extract_ps_compare_settings() {
  python3 - <<'PY' "$HTML_FILE"
import json, re, sys
html = open(sys.argv[1], encoding="utf-8", errors="ignore").read()
match = re.search(r'"psCompare":(\{[^}]+\})', html)
if not match:
    sys.exit(1)
print(json.dumps(json.loads(match.group(1))))
PY
}

echo "=== PS Compare B2B — Search page ($BASE) ==="

rm -f "$COOKIE_JAR"
touch "$COOKIE_JAR"

echo "--- Warm-up & page load ---"
curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null "$BASE/" 2>/dev/null || true
fetch "$BASE/find-property" > "$HTML_FILE"
if [[ ! -s "$HTML_FILE" ]]; then
  fail "Search page returned empty body"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi
pass "Search page HTML downloaded ($(wc -c < "$HTML_FILE") bytes)"

assert_http_200 "$BASE/find-property" "Search page HTTP"

echo "--- Compare UI markup on search cards ---"
assert_html_contains 'data-ps-compare-toggle' "Compare toggle buttons present"
assert_html_contains 'ps-compare-button' "Compare button component markup"
assert_html_contains 'ps-offer-search-card__action--compare' "Compare action slot on offer-search-card"
assert_html_contains 'data-ps-compare-widget' "Compare widget injected in search view"
assert_html_contains 'data-ps-compare-modal' "Compare modal shell on search page"
assert_html_contains 'data-ps-compare-share-open' "Share offcanvas trigger in modal header"
assert_html_contains 'ps-compare-modal__share-cta' "Modal header share CTA present"
assert_html_contains 'shareOffcanvasEndpoint' "shareOffcanvasEndpoint present in drupalSettings"
assert_html_contains '\/api\/compare\/share-offcanvas' "shareOffcanvasEndpoint uses /api/compare/share-offcanvas"
assert_html_contains 'data-ps-compare-cta' "Compare selection CTA present"

echo "--- drupalSettings API endpoints (/api/compare/*) ---"
assert_html_contains 'countEndpoint' "drupalSettings countEndpoint present"
assert_html_contains '\/api\/compare\/count' "countEndpoint uses /api/compare/count"
assert_html_contains '\/api\/compare\/state' "stateEndpoint uses /api/compare/state"
assert_html_contains 'panelListEndpoint' "panelListEndpoint present in drupalSettings"
assert_html_contains 'modalEndpoint' "modalEndpoint present in drupalSettings"
assert_html_contains '\/api\/compare\/modal' "modalEndpoint uses /api/compare/modal"
assert_html_contains 'panelEndpoint' "panelEndpoint present in drupalSettings"
assert_html_contains '\/api\/compare\/panel' "panelEndpoint uses /api/compare/panel"
assert_html_contains '/api/compare/toggle/node/' "toggle data-url uses /api/compare/toggle"

assert_http_403 "$BASE/api/compare/share-offcanvas" "GET /api/compare/share-offcanvas without enough items"

echo "--- Public compare APIs (GET) ---"
assert_http_200 "$BASE/api/compare/count" "GET /api/compare/count"
assert_http_200 "$BASE/api/compare/state" "GET /api/compare/state"
assert_http_200 "$BASE/api/compare/panel" "GET /api/compare/panel"
assert_http_200 "$BASE/api/compare/panel/list" "GET /api/compare/panel/list"
assert_http_200 "$BASE/api/compare/modal" "GET /api/compare/modal"

assert_json_field "$BASE/api/compare/count" "maxItems" "Count API maxItems"
assert_json_field "$BASE/api/compare/count" "minItems" "Count API minItems"
assert_json_field "$BASE/api/compare/count" "compareUrl" "Count API compareUrl"

echo "--- Legacy routes must not exist ---"
LEGACY_COUNT=$(curl -s -m 30 -o /dev/null -w '%{http_code}' "$BASE/compare/count" 2>/dev/null || echo "000")
if [[ "$LEGACY_COUNT" == "404" ]]; then
  pass "Legacy /compare/count returns 404"
else
  fail "Legacy /compare/count should be 404 (got $LEGACY_COUNT)"
fi

echo "--- Toggle flow (Drupal kernel via Drush — session-independent) ---"
TOGGLE_RESULT=$(docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$storage = \\Drupal::entityTypeManager()->getStorage(\"node\");
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 4)->execute());
if (count(\$ids) < 2) { print \"FAIL:not_enough_offers\"; return; }
\$manager = \\Drupal::service(\"ps_compare.manager\");
foreach (\$ids as \$id) { \$manager->removeCompare(\$storage->load(\$id)); }
\$controller = \\Drupal::service(\"Drupal\\\\ps_compare\\\\Controller\\\\CompareToggleController\");
\$csrf = \\Drupal::service(\"csrf_token\")->get(\"ps_compare.toggle\");
\$request = \\Symfony\\Component\\HttpFoundation\\Request::create(\"\", \"POST\", [], [], [], [\"HTTP_X_CSRF_TOKEN\" => \$csrf]);
\$r1 = \$controller->toggle(\$request, \"node\", (int) \$ids[0]);
\$p1 = json_decode(\$r1->getContent(), TRUE);
\$r2 = \$controller->toggle(\$request, \"node\", (int) \$ids[1]);
\$p2 = json_decode(\$r2->getContent(), TRUE);
if ((\$p1[\"isCompared\"] ?? FALSE) !== TRUE || (\$p1[\"count\"] ?? 0) !== 1) { print \"FAIL:add1\"; return; }
if ((\$p2[\"isCompared\"] ?? FALSE) !== TRUE || (\$p2[\"count\"] ?? 0) !== 2) { print \"FAIL:add2\"; return; }
if (\$manager->canOpenComparisonPage() !== TRUE) { print \"FAIL:can_compare\"; return; }
\$r3 = \$controller->toggle(\$request, \"node\", (int) \$ids[0]);
\$p3 = json_decode(\$r3->getContent(), TRUE);
if ((\$p3[\"isCompared\"] ?? TRUE) !== FALSE || (\$p3[\"count\"] ?? 0) !== 1) { print \"FAIL:remove1\"; return; }
print \"PASS:toggle_flow\";
"' 2>/dev/null | tail -1)

if [[ "$TOGGLE_RESULT" == "PASS:toggle_flow" ]]; then
  pass "Toggle add/remove flow (2 offers, CSRF, count)"
else
  fail "Toggle flow via Drush ($TOGGLE_RESULT)"
fi

LIMIT_RESULT=$(docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush php:eval "
\$storage = \\Drupal::entityTypeManager()->getStorage(\"node\");
\$ids = array_values(\$storage->getQuery()->accessCheck(TRUE)->condition(\"type\", \"offer\")->range(0, 5)->execute());
if (count(\$ids) < 5) { print \"SKIP:need_5_offers\"; return; }
\$manager = \\Drupal::service(\"ps_compare.manager\");
foreach (\$ids as \$id) { \$manager->removeCompare(\$storage->load(\$id)); }
\$controller = \\Drupal::service(\"Drupal\\\\ps_compare\\\\Controller\\\\CompareToggleController\");
\$csrf = \\Drupal::service(\"csrf_token\")->get(\"ps_compare.toggle\");
\$request = \\Symfony\\Component\\HttpFoundation\\Request::create(\"\", \"POST\", [], [], [], [\"HTTP_X_CSRF_TOKEN\" => \$csrf]);
foreach (array_slice(\$ids, 0, 4) as \$id) { \$controller->toggle(\$request, \"node\", (int) \$id); }
\$r = \$controller->toggle(\$request, \"node\", (int) \$ids[4]);
if (\$r->getStatusCode() !== 409) { print \"FAIL:status_\" . \$r->getStatusCode(); return; }
\$payload = json_decode(\$r->getContent(), TRUE);
if ((\$payload[\"limit\"] ?? NULL) !== 4) { print \"FAIL:no_limit_flag\"; return; }
print \"PASS:limit_409\";
"' 2>/dev/null | tail -1)

if [[ "$LIMIT_RESULT" == "PASS:limit_409" ]]; then
  pass "5th add returns HTTP 409 with limit=4"
elif [[ "$LIMIT_RESULT" == "SKIP:need_5_offers" ]]; then
  pass "Limit test skipped (< 5 offers in DB)"
else
  fail "Compare limit enforcement ($LIMIT_RESULT)"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
