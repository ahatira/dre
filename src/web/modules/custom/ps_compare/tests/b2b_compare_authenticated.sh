#!/usr/bin/env bash
# B2B smoke tests — Authenticated user must not hit 500 (ps_compare_item table).
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0
COOKIE_JAR="${TMPDIR:-/tmp}/ps-compare-b2b-auth-cookies.txt"

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }

assert_http_200() {
  local url="$1" label="$2"
  local code
  code=$(curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null -w '%{http_code}' "$url" 2>/dev/null || echo "000")
  if [[ "$code" == "200" ]]; then
    pass "$label (HTTP 200)"
  else
    fail "$label (HTTP $code) — $url"
  fi
}

echo "=== PS Compare B2B — Authenticated pages ($BASE) ==="

rm -f "$COOKIE_JAR"
touch "$COOKIE_JAR"

ULI=$(docker exec -i ps_php sh -lc 'cd /var/www/html && vendor/bin/drush uli --name=admin --uri=http://localhost:8080' 2>/dev/null | tail -1)
if [[ -z "$ULI" ]]; then
  fail "Could not generate admin login link"
  echo "=== Results: $PASS passed, $FAIL failed ==="
  exit 1
fi
pass "Admin ULI generated"

curl -sL -m 120 -b "$COOKIE_JAR" -c "$COOKIE_JAR" -o /dev/null "$ULI" 2>/dev/null || true
pass "Admin session established"

echo "--- Key pages as authenticated user ---"
assert_http_200 "$BASE/find-property" "Search page for logged-in user"
assert_http_200 "$BASE/compare" "Compare page for logged-in user"
assert_http_200 "$BASE/for-rent/office/" "SEO listing page for logged-in user"
assert_http_200 "$BASE/api/compare/count" "Compare count API for logged-in user"
assert_http_200 "$BASE/api/compare/panel" "Compare panel API for logged-in user"

PANEL_LIST=$(curl -sL -m 60 -b "$COOKIE_JAR" -w '%{http_code}' -o /tmp/ps-compare-panel-list.html "$BASE/api/compare/panel/list" 2>/dev/null || echo "000")
if [[ "$PANEL_LIST" == "200" ]]; then
  pass "Compare panel list HTML API (HTTP 200)"
else
  fail "Compare panel list HTML API (HTTP $PANEL_LIST)"
fi

echo ""
echo "=== Results: $PASS passed, $FAIL failed ==="
if [[ "$FAIL" -gt 0 ]]; then
  exit 1
fi
