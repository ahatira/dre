#!/usr/bin/env bash
# B2B URL + security smoke tests for ps_search path migration and filters.
set -euo pipefail

BASE="${BASE_URL:-http://localhost:8080}"
PASS=0
FAIL=0
SKIP=0

pass() { echo "  PASS: $1"; PASS=$((PASS + 1)); }
fail() { echo "  FAIL: $1"; FAIL=$((FAIL + 1)); }
skip() { echo "  SKIP: $1"; SKIP=$((SKIP + 1)); }

status_code() {
  curl -sI -m 120 -o /dev/null -w '%{http_code}' "$1" 2>/dev/null || echo "000"
}

final_location() {
  curl -sI -m 30 -L -o /dev/null -w '%{url_effective}' "$1" 2>/dev/null || echo ""
}

assert_status() {
  local url="$1" expected="$2" label="$3"
  local got
  got=$(status_code "$url")
  if [[ "$got" == "$expected" ]]; then
    pass "$label ($got)"
  else
    fail "$label (expected $expected, got $got) — $url"
  fi
}

assert_redirect_to() {
  local url="$1" expect_fragment="$2" label="$3"
  local loc
  loc=$(curl -sI -m 30 "$url" 2>/dev/null | awk 'BEGIN{IGNORECASE=1} /^Location:/ {sub(/\r$/,""); print substr($0,11); exit}')
  if [[ "$loc" == *"$expect_fragment"* ]]; then
    pass "$label → $loc"
  else
    fail "$label (Location: ${loc:-none}, expected *${expect_fragment}*)"
  fi
}

assert_body_not_contains() {
  local url="$1" needle="$2" label="$3"
  local body
  body=$(curl -s -m 60 "$url" 2>/dev/null || true)
  if echo "$body" | grep -Fq "$needle"; then
    fail "$label (found unescaped: $needle)"
  else
    pass "$label"
  fi
}

assert_body_contains() {
  local url="$1" needle="$2" label="$3"
  local body
  body=$(curl -s -m 60 "$url" 2>/dev/null || true)
  if echo "$body" | grep -Fq "$needle"; then
    pass "$label"
  else
    fail "$label (missing: $needle)"
  fi
}

echo "=== PS Search B2B URL tests ($BASE) ==="

echo "--- Canonical search paths ---"
assert_status "$BASE/find-property" "200" "EN search /find-property"
assert_status "$BASE/fr/recherche-immobiliere" "200" "FR search /fr/recherche-immobiliere"
assert_status "$BASE/fr/find-property" "200" "FR accepts EN slug /fr/find-property"

echo "--- Legacy redirects ---"
assert_redirect_to "$BASE/recherche" "/find-property" "Legacy /recherche (EN)"
assert_redirect_to "$BASE/fr/recherche" "/fr/recherche-immobiliere" "Legacy /fr/recherche"

echo "--- SEO URLs ---"
assert_status "$BASE/fr/a-louer/bureaux/paris-75/paris-75015/" "200" "FR SEO rent+office+Paris"
assert_status "$BASE/fr/a-louer/bureaux/" "200" "FR SEO rent+office"
assert_status "$BASE/for-rent/office/" "200" "EN SEO rent+office"

echo "--- Invalid paths (must 404) ---"
assert_status "$BASE/fr/recherche-immobiliere/paris-75/paris-75015/" "404" "Locality appended to flexible FR base"
assert_status "$BASE/find-property/paris-75/" "404" "Locality appended to flexible EN base"
assert_status "$BASE/fr/recherche/paris-75/" "404" "Legacy slug + invalid locality segment"

echo "--- Locality query (flexible search) ---"
assert_status "$BASE/fr/recherche-immobiliere?locality=75015" "200" "FR locality postal code"
assert_status "$BASE/find-property?locality=Nancy" "200" "EN locality city name"

echo "--- Canonical redirect with operation_type ---"
loc=$(curl -sI -m 120 "$BASE/fr/recherche-immobiliere?operation_type=LOC" 2>/dev/null | awk 'BEGIN{IGNORECASE=1} /^Location:/ {sub(/\r$/,""); print substr($0,11); exit}')
if [[ "$loc" == *"/fr/a-louer"* ]]; then
  pass "operation_type=LOC → SEO URL ($loc)"
else
  fail "operation_type=LOC redirect (Location: ${loc:-none})"
fi

echo ""
echo "=== Security smoke tests ==="

XSS_PAYLOAD='<script>alert("xss")</script>'
ENCODED=$(python3 -c "import urllib.parse; print(urllib.parse.quote('''$XSS_PAYLOAD'''))")
assert_body_not_contains "$BASE/fr/recherche-immobiliere?locality=$ENCODED" "$XSS_PAYLOAD" "XSS locality not reflected raw"
assert_body_not_contains "$BASE/find-property?keywords=$ENCODED" "$XSS_PAYLOAD" "XSS keywords not reflected raw"

SQL_PAYLOAD="' OR 1=1--"
ENCODED_SQL=$(python3 -c "import urllib.parse; print(urllib.parse.quote('''$SQL_PAYLOAD'''))")
assert_status "$BASE/fr/recherche-immobiliere?locality=$ENCODED_SQL" "200" "SQLi locality does not crash (200)"
assert_body_not_contains "$BASE/fr/recherche-immobiliere?locality=$ENCODED_SQL" "SQLSTATE" "SQLi no SQL error leaked"

TRAV="../etc/passwd"
assert_status "$BASE/fr/recherche-immobiliere/$TRAV" "404" "Path traversal segment blocked"

HEADER_INJ=$'75015%0d%0aX-Injected:%20true'
assert_status "$BASE/fr/recherche-immobiliere?locality=$HEADER_INJ" "200" "CRLF in locality param handled"
inj_header=$(curl -sI -m 30 "$BASE/fr/recherche-immobiliere?locality=$HEADER_INJ" 2>/dev/null | awk 'BEGIN{IGNORECASE=1} /^X-Injected:/ {print; exit}')
if [[ -z "$inj_header" ]]; then
  pass "CRLF locality did not inject response header"
else
  fail "CRLF locality injected header: $inj_header"
fi

LONG_LOCALITY=$(python3 -c "print('A'*500)")
ENCODED_LONG=$(python3 -c "import urllib.parse; print(urllib.parse.quote('''$LONG_LOCALITY'''))")
assert_status "$BASE/fr/recherche-immobiliere?locality=$ENCODED_LONG" "200" "Oversized locality param (200, no 500)"

echo ""
echo "=== Summary: $PASS passed, $FAIL failed, $SKIP skipped ==="
[[ "$FAIL" -eq 0 ]]
